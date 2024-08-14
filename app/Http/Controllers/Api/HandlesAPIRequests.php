<?php

namespace App\Http\Controllers\Api;

use App\Enums\ApiResponse;
use App\Exceptions\DisallowedApiFieldException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/**
 * This trait file simplifies API controllers by taking the incoming GET request and forming the
 * eloquent query that grabs the data to be returned to the API controller. It sets a default
 * limit and offset for results, and automatically caches the API response to implement
 * caching at the API level, which is easily "busted" by passing in `cached=false`
 * into the URL query parameter string. Have a look through the methods.
 *
 * It also allows API calls to perform ad-hoc filtering and ordering, by using "where", "orderBy", "like" and
 * "whereIn" clauses, giving you some quite powerful capabilities when requesting data from the API. It
 * also lets you set a comma-separated "fields" list, to filter the object attributes set that is
 * returned by the API.
 *
 * It also allows usage of the API to get related data, using the 'relations' query parameter
 *
 * Simple Example:
 * Get all users' id, name, and email (cached)
 * GET /api/v1/users?cached=false&fields=id,name,email
 *
 * Advanced Example:
 * Get all users' id, name and email who use Gmail, who signed up after 2010, ordered by id descending,
 * limited to 100 records, but ALSO grab their followers list
 * GET
 * /api/v1/users?cached=false&fields=id,name,email&where[]=email,like,*gmail*&where=created_at,>,2010-01-01&orderBy=id,desc&limit=100&relations=followers
 *
 * Data is returned in a "data" attribute.
 *
 * Lastly, it prepends a "meta" object, giving the API consumer some information about the request they just made.
 *
 *
 * Trait HandlesAPIRequests
 */
trait HandlesAPIRequests
{
    public Request     $request;
    public array       $fields       = [];
    public int         $limit        = 50;
    public string      $message      = '';
    public int         $offset       = 0;
    public int         $responseCode = 200;
    public mixed       $data         = [];
    protected mixed    $query        = false;
    public bool        $cached       = true;
    public string      $cacheKey     = '';

    /**
     * Set the related data we'll ask for in GET API requests
     *
     * @var array
     */
    public array $associatedData = [];

    /**
     * HandlesAPIRequests constructor.
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {

        $this->request = $request;

        if (strtolower($this->request->getMethod()) == 'get') {

            $this->cacheKey = $this->request->fullUrl();
            $this->setRelations();
            $this->setLimit();
            $this->setOffset();
            $this->setCached();
        }

    }

    /**
     * Get the fields being retrieved in the data set.
     */
    protected function getFields(): array
    {
        return $this->fields;
    }

    /**
     * Set the fields being retrieved from the data set
     *
     * @param mixed $fields
     */
    protected function setFields(string $fields)
    {
        if (strlen(trim($fields)) >= 1) {
            $fields = explode(',', strtolower($fields));

            $this->fields = (count($fields) >= 1) ? $fields : $this->fields;
        }

    }

    /**
     * Get the LIMIT param for read queries
     */
    protected function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * Set the LIMIT param for read queries
     */
    protected function setLimit()
    {
        if (isset($this->request->limit)) {
            $limit       = (int) $this->request->limit;
            $this->limit = (($limit >= 1) && ($limit <= 10000)) ? $limit : $this->limit;
        }
    }

    /**
     * Allow the relations to be included in the GET API call.
     */
    protected function setRelations()
    {
        if (isset($this->request->relations)) {

            $relationsArray       = explode(',', $this->request->relations);
            $relationsArray       = array_map('trim', $relationsArray);
            $this->associatedData = array_intersect($relationsArray, $this->availableRelations);
        }
    }

    /**
     * Get the CACHED param for read queries
     *
     * @return int
     */
    protected function getCached()
    {
        return $this->cached;
    }

    /**
     * Set the CACHED param for read queries
     */
    protected function setCached()
    {
        $cached = $this->request->get('cached');

        if (isset($cached)) {
            switch (strtolower($cached)) {
                case 'false':
                case 'f':
                case 'no':
                case 'n':
                case '':
                    $this->cached = false;
                    break;
            }
        }
    }

    /**
     * Get the OFFSET param for read queries
     *
     * @return int
     */
    protected function getOffset()
    {
        return $this->offset;
    }

    /**
     * Set the OFFSET param for read queries
     */
    protected function setOffset()
    {
        if (isset($this->request->offset)) {
            $offset = (int) $this->request->offset;

            $this->offset = ($offset >= 1) ? $offset : $this->offset;
        }
    }

    /**
     * @param mixed $field
     *
     * @throws DisallowedApiFieldException
     */
    private static function checkFieldAllowedState(string $field)
    {
        if (!in_array($field, self::$searchableFields)) {
            throw new DisallowedApiFieldException(ApiResponse::RESPONSE_QUERY_FILTER_DISALLOWED->value . ' on field ' . $field . '.');
        }
    }

    /**
     * For GET requests, build the data set based on URL parameters
     *
     * Order By - order the data set by a field and direction - default is ASC
     * eg. https://API.com/v1/endpoint?orderBy=id
     * eg. https://API.com/v1/endpoint?orderBy=id,desc
     * eg. https://API.com/v1/endpoint?orderBy[]=property_type,asc&orderBy[]=id,desc  <-- Check out this one - multiple
     * orderBys!
     *
     * Where - filter the data set by specific fields
     * eg. https://API.com/v1/endpoint?where=id,1234
     * eg. https://API.com/v1/endpoint?where=id,=,1234
     * eg. https://API.com/v1/endpoint?where=id,>=,1234
     * eg. https://API.com/v1/endpoint?where[]=id,>=,1234&where[]=property_type,=,business <-- Check out this one -
     * multiple wheres!
     *
     * @return $this->query
     *
     * @throws DisallowedApiFieldException
     */
    public function updateReadQueryBasedOnUrl()
    {

        // Order By
        if (isset($this->request['orderBy'])) {

            if (!is_array($this->request['orderBy'])) {
                $dir            = 'asc';
                $paramValueBits = explode(',', $this->request['orderBy']);

                self::checkFieldAllowedState($paramValueBits[0]);

                if (isset($paramValueBits[1])) {
                    $dir = $paramValueBits[1];
                }

                $this->query = $this->query->orderBy($paramValueBits[0], $dir);
            }
            else {
                foreach ($this->request['orderBy'] as $individualOrderBy) {
                    $dir            = 'asc';
                    $paramValueBits = explode(',', $individualOrderBy);

                    self::checkFieldAllowedState($paramValueBits[0]);

                    if (isset($paramValueBits[1])) {
                        $dir = $paramValueBits[1];
                    }

                    $this->query = $this->query->orderBy($paramValueBits[0], $dir);
                }
            }

        }

        // Group By
        if (isset($this->request['groupBy'])) {

            $fieldToGroupBy = $this->request['groupBy'];
            self::checkFieldAllowedState($fieldToGroupBy);
            $this->query = $this->query->groupBy($fieldToGroupBy);
        }

        // Where In
        if (isset($this->request['whereIn'])) {
            $paramValueBits = explode(',', $this->request['whereIn']);
            $valueArray     = explode('|', $paramValueBits[1]);
            $this->query    = $this->query->whereIn($paramValueBits[0], $valueArray);

            self::checkFieldAllowedState($paramValueBits[0]);
        }

        // Where
        if (isset($this->request['where'])) {

            if (!is_array($this->request['where'])) {
                $paramValueBits = explode(',', $this->request['where']);
                switch (count($paramValueBits)) {
                    case 2:
                        $field   = $paramValueBits[0];
                        $operand = '=';
                        $value   = $paramValueBits[1];
                        break;
                    case 3:
                        $field   = $paramValueBits[0];
                        $operand = $paramValueBits[1];
                        $value   = $paramValueBits[2];
                        break;
                    default:
                        /**
                         * The developer is playing with the query.
                         * De-scope the query to zero results.
                         */

                        return $this->query->limit(0);
                }
                if (substr($value, 0, 1) == '*') {
                    $value = '%' . substr($value, 1, 1000);
                }

                if (substr($value, -1, 1) == '*') {
                    $value = substr($value, 0, -1) . '%';
                }

                // Handle IN queries
                if (trim(strtolower($operand)) == 'in') {
                    $valueArray = explode('|', $value);

                    $this->query = $this->query->whereIn($field, $operand, $valueArray);
                }
                else {

                    switch (trim(strtolower($value))) {
                        case 'null':
                            $this->query = $this->query->whereNull($field);
                            break;
                        case 'notnull':
                            $this->query = $this->query->whereNotNull($field);
                            break;
                        default:
                            // Handle standard WHERE
                            $this->query = $this->query->where($field, $operand, $value);
                    }
                }

                self::checkFieldAllowedState($field);

            }
            else {
                foreach ($this->request['where'] as $individualWhere) {
                    $paramValueBits = explode(',', $individualWhere);
                    switch (count($paramValueBits)) {
                        case 2:
                            // &where=key,value
                            $field   = $paramValueBits[0];
                            $operand = '=';
                            $value   = urldecode($paramValueBits[1]);
                            break;
                        case 3:
                            // &where=key,>=,1234
                            $field   = $paramValueBits[0];
                            $operand = $paramValueBits[1];
                            $value   = urldecode($paramValueBits[2]);
                            break;
                        default:
                            /**
                             * The developer is playing with the query.
                             * De-scope the query to zero results.
                             */
                            return $this->query->limit(0);
                    }

                    if (str_starts_with($value, '*')) {
                        $value = '%' . substr($value, 1, 1000);
                    }

                    if (str_ends_with($value, '*')) {
                        $value = substr($value, 0, -1) . '%';
                    }

                    // Handle IN queries
                    if (trim(strtolower($operand)) == 'in') {
                        $valueArray  = explode('|', $value);
                        $this->query = $this->query->whereIn($field, $valueArray);
                    }
                    else {

                        switch (trim(strtolower($value))) {
                            case 'null':
                                $this->query = $this->query->whereNull($field);
                                break;
                            case 'notnull':

                                $this->query = $this->query->whereNotNull($field);
                                break;
                            default:
                                // Handle standard WHERE
                                $this->query = $this->query->where($field, $operand, $value);
                        }

                    }

                    self::checkFieldAllowedState($field);
                }
            }
        }

        // Limit & Offset
        $this->query = $this->query->limit($this->limit)->offset($this->offset);

        // Filter to fields
        if (isset($this->request['fields'])) {
            $this->setFields($this->request['fields']);

            $this->query = (count($this->fields) >= 1) ? $this->query->select($this->fields) : $this->query;
        }

        return $this->query;
    }

    /**
     * Generic response
     *
     * @return JsonResponse
     */
    protected function respond()
    {

        $this->handleDataNotFound();

        $reply = [
            'meta' => [
                'responseCode' => $this->responseCode,
                'limit'        => $this->limit,
                'offset'       => $this->offset,
                'message'      => $this->message,
            ],
            'data' => $this->data,
        ];

        /**
         * Cache GET requests, if required.
         */
        if (strtolower($this->request->getMethod()) == 'get') {

            $reply['meta']['cached'] = $this->getCached();
            if ($this->cached) {
                $reply['meta']['cached_at'] = date('Y-m-d H:i:s');
            }

            $reply['meta']['availableRelations'] = $this->availableRelations;

            /**
             * If the request asks for cached data, AND it's a 200, AND a GET,
             */
            if (($this->getCached() !== false) && ($this->responseCode == 200)) {

                if (Cache::has($this->cacheKey)) {
                    return Cache::get($this->cacheKey);
                }

                Cache::remember($this->cacheKey, 15, function () use ($reply) {

                    return response()->json($reply, $this->responseCode);
                });

            }

        }

        return response()->json($reply, $this->responseCode);
    }

    /**
     * Update the data object and the response code if no data is found.
     */
    private function handleDataNotFound()
    {

        if (!$this->data && ($this->responseCode == 200) && (strtolower($this->request->method()) == 'get')) {
            $this->data         = [];
            $this->responseCode = 404;
            $this->message      = ApiResponse::RESPONSE_NOT_FOUND->value;
        }
    }
}
