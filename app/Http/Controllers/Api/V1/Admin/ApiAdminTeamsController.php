<?php

/** @noinspection PhpUndefinedMethodInspection */

namespace App\Http\Controllers\Api\V1\Admin;

use App\Enums\ApiResponse;
use App\Exceptions\DisallowedApiFieldException;
use App\Http\Controllers\Api\HandlesAPIRequests;
use App\Http\Controllers\Controller;
use App\Models\Team;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Knuckles\Scribe\Attributes\Authenticated;
use Knuckles\Scribe\Attributes\BodyParam;
use Knuckles\Scribe\Attributes\Endpoint;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\QueryParam;
use Knuckles\Scribe\Attributes\Response;
use Knuckles\Scribe\Attributes\Subgroup;

#[Group('Admin Endpoints')]
#[Subgroup('/admin/teams', 'API for managing teams')]
class ApiAdminTeamsController extends Controller
{
    use HandlesAPIRequests;

    /**
     * Set the related data the GET request is allowed to ask for
     */
    public array $availableRelations = [
        'teamsThisTeamIsMerchantFor',
        'teamsThisTeamIsServiceFor',
    ];

    public static array $searchableFields = [
        'id',
        'name',

    ];

    /**
     * GET /
     *
     * @return JsonResponse
     *
     * @throws DisallowedApiFieldException
     */
    #[Endpoint(
        title        : 'GET /',
        description  : 'Retrieve teams.',
        authenticated: true
    )]
    #[Authenticated]
    #[QueryParam(
        name       : 'cached',
        type       : 'bool',
        description: 'Request the response to be cached. Default: `true`.',
        required   : false,
        example    : true
    )]
    #[QueryParam(
        name       : 'page',
        type       : 'int',
        description: 'The pagination page number.',
        required   : false,
        example    : 1
    )]
    #[QueryParam(
        name       : 'limit',
        type       : 'int',
        description: 'The number of entries returned per pagination page.',
        required   : false,
        example    : 50
    )]
    #[QueryParam(
        name       : 'fields',
        type       : 'string',
        description: 'Comma-separated list of database fields to return within the object.',
        required   : false,
        example    : 'id,created_at'
    )]
    #[QueryParam(
        name       : 'orderBy',
        type       : 'comma-separated',
        description: 'Order the data by a given field. Comma-separated string.',
        required   : false,
        example    : 'orderBy=id,desc'
    )]
    #[QueryParam(
        name       : 'orderBy[]',
        type       : 'comma-separated',
        description: 'Compound `orderBy`. Order the data by a given field. Comma-separated string. Can not be used in conjunction as standard `orderBy`.',
        required   : false,
        example    : 'orderBy[]=id,desc&orderBy[]=created_at,asc'
    )]
    #[QueryParam(
        name       : 'where',
        type       : 'comma-separated',
        description: 'Filter the request on a single field. Key-Value or Key-Operator-Value comma-separated string.',
        required   : false,
        example    : 'where=id,like,*550e*'
    )]
    #[QueryParam(
        name       : 'where[]',
        type       : 'comma-separated',
        description: 'Compound `where`. Use when you need to filter on multiple `where`\'s. Note only AND is possible; ORWHERE is not available.',
        required   : false,
        example    : 'where[]=id,like,*550e*&where[]=created_at,>=,2024-01-01'
    )]
    #[Response(
        content    : '{"meta":{"responseCode":200,"limit":50,"offset":0,"message":"","cached":false,"availableRelations":[]},"data":{"current_page":1,"data":[{"id": "550e8400-e29b-41d4-a716-446655440000", "created_at": "2024-01-01 00:00:00"}],"first_page_url":"https:\/\/open-food-network-vouchers.test\/api\/v1\/admin\/system-statistics?page=1","from":null,"last_page":1,"last_page_url":"https:\/\/open-food-network-vouchers.test\/api\/v1\/admin\/system-statistics?page=1","links":[{"url":null,"label":"&laquo; Previous","active":false},{"url":"https:\/\/open-food-network-vouchers.test\/api\/v1\/admin\/system-statistics?page=1","label":"1","active":true},{"url":null,"label":"Next &raquo;","active":false}],"next_page_url":null,"path":"https:\/\/open-food-network-vouchers.test\/api\/v1\/admin\/system-statistics","per_page":1,"prev_page_url":null,"to":null,"total":0}}',
        status     : 200,
        description: ''
    )]
    public function index(): JsonResponse
    {
        $this->query = Team::with($this->associatedData);
        $this->query = $this->updateReadQueryBasedOnUrl();
        $this->data  = $this->query->paginate($this->limit);

        return $this->respond();
    }

    /**
     * POST /
     *
     * @return JsonResponse
     */
    #[Endpoint(
        title        : 'POST /',
        description  : 'Create a team.',
        authenticated: true
    )]
    #[Authenticated]
    #[BodyParam(
        name       : 'name',
        type       : 'string',
        description: 'The team name.',
        required   : true
    )]
    #[BodyParam(
        name       : 'country_id',
        type       : 'int',
        description: 'The database countries.id of the team you are adding.',
        required   : true
    )]
    public function store(): JsonResponse
    {
        /**
         * The validation array.
         */
        $validationArray = [
            'name' => [
                'required',
                'string',
            ],
            'country_id' => [
                'required',
                'integer',
                Rule::exists('countries', 'id'),
            ],

        ];

        $validator = Validator::make($this->request->all(), $validationArray);

        if ($validator->fails()) {

            $this->responseCode = 400;
            $this->message      = $validator->errors()
                ->first();

        }
        else {

            try {

                $model = new Team();

                foreach ($validationArray as $key => $validationRule) {
                    $value = $this->request->get($key);
                    if (isset($value)) {
                        $model->$key = $value;
                    }
                }

                $model->save();

                $this->message = ApiResponse::RESPONSE_SAVED->value;
                $this->data    = $model;

            }
            catch (Exception $e) {

                $this->responseCode = 500;
                $this->message      = ApiResponse::RESPONSE_ERROR->value . ': "' . $e->getMessage() . '".';

            }
        }

        return $this->respond();

    }

    /**
     * GET / {id}
     *
     * @param string $id
     *
     * @return JsonResponse
     *
     * @throws DisallowedApiFieldException
     */
    #[Endpoint(
        title        : 'GET /{id}',
        description  : 'Retrieve a single team by ID.',
        authenticated: true,
    )]
    #[Authenticated]
    #[QueryParam(
        name       : 'cached',
        type       : 'bool',
        description: 'Request the response to be cached. Default: `true`.',
        required   : false,
        example    : 1
    )]
    #[QueryParam(
        name       : 'fields',
        type       : 'string',
        description: 'Comma-separated list of database fields to return within the object.',
        required   : false,
        example    : 'id,created_at'
    )]
    #[Response(
        content    : '{
  "meta": {
    "responseCode": 200,
    "limit": 50,
    "offset": 0,
    "message": "",
    "cached": true,
    "cached_at": "2024-08-13 08:58:19",
    "availableRelations": []
  },
  "data": {"id": 1234, "created_at": "2024-01-01 00:00:00"}
}',
        status     : 200,
        description: ''
    )]
    public function show(string $id)
    {
        $this->query = Team::with($this->associatedData);
        $this->query = $this->updateReadQueryBasedOnUrl();
        $this->data  = $this->query->find($id);

        return $this->respond();
    }

    /**
     * PUT/ {id}
     *
     * @param string $id
     *
     * @return JsonResponse
     */
    #[Endpoint(
        title        : 'PUT /{id}',
        description  : 'Update a team.',
        authenticated: true
    )]
    #[Authenticated]
    #[BodyParam(
        name       : 'name',
        type       : 'string',
        description: 'The updated team name.',
        required   : false
    )]
    #[BodyParam(
        name       : 'country_id',
        type       : 'int',
        description: 'The updated team country_id.',
        required   : false
    )]
    public function update(string $id)
    {
        /**
         * The validation array.
         */
        $validationArray = [
            'name' => [
                'sometimes',
            ],
            'country_id' => [
                'sometimes',
                'integer',
                Rule::exists('countries', 'id'),
            ],
        ];

        $validator = Validator::make($this->request->all(), $validationArray);

        if ($validator->fails()) {
            $this->responseCode = 400;
            $this->message      = $validator->errors()->first();
        }
        else {

            $model = Team::find($id);

            if (!$model) {

                $this->responseCode = 404;
                $this->message      = ApiResponse::RESPONSE_NOT_FOUND->value;

            }
            else {
                try {

                    foreach ($validationArray as $key => $validationRule) {
                        $value = $this->request->get($key);
                        if ((isset($value))) {
                            $model->$key = $value;
                        }
                    }

                    $model->save();

                    $this->message = ApiResponse::RESPONSE_UPDATED->value;
                    $this->data    = $model;

                }
                catch (Exception $e) {

                    $this->responseCode = 500;
                    $this->message      = ApiResponse::RESPONSE_ERROR->value . ':' . $e->getMessage();

                }
            }
        }

        return $this->respond();

    }

    /**
     * DELETE / {id}
     *
     * @param string $id
     *
     * @return JsonResponse
     */
    #[Endpoint(
        title        : 'Delete /{id}',
        description  : 'Remove a team. WARNING: May have unintended side effects or cause data integrity issues.',
        authenticated: true
    )]
    public function destroy(string $id)
    {
        try {

            $model = Team::find($id);

            if (!$model) {

                $this->responseCode = 404;
                $this->message      = ApiResponse::RESPONSE_NOT_FOUND->value;

            }
            else {

                $model->delete();
                $this->message = ApiResponse::RESPONSE_DELETED->value;

            }

        }
        catch (Exception $e) {

            $this->responseCode = 500;
            $this->message      = ApiResponse::RESPONSE_ERROR->value . ':' . $e->getMessage();

        }

        return $this->respond();

    }
}
