<?php

/** @noinspection PhpUnusedParameterInspection */

/** @noinspection PhpUndefinedMethodInspection */

namespace App\Http\Controllers\Api\V1\Admin;

use App\Enums\ApiResponse;
use App\Enums\PersonalAccessTokenAbility;
use App\Events\PersonalAccessTokens\PersonalAccessTokenWasCreated;
use App\Exceptions\DisallowedApiFieldException;
use App\Http\Controllers\Api\HandlesAPIRequests;
use App\Http\Controllers\Controller;
use App\Models\PersonalAccessToken;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Crypt;
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
#[Subgroup('/admin/user-personal-access-tokens', 'API for managing a user personal access tokens')]
class ApiAdminUserPersonalAccessTokensController extends Controller
{
    use HandlesAPIRequests;

    /**
     * Set the related data the GET request is allowed to ask for
     */
    public array $availableRelations = [
        'user',
    ];

    public static array $searchableFields = [
        'id',
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
        description  : 'Retrieve personal access tokens (PATs).',
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
        $this->query = PersonalAccessToken::with($this->associatedData)
            ->select(
                [
                    'id',
                    'tokenable_type',
                    'tokenable_id',
                    'name',
                    'abilities',
                    'last_used_at',
                    'expires_at',
                    'created_at',
                ]
            );
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
        description  : 'Add a PAT.',
        authenticated: true
    )]
    #[Authenticated]
    #[BodyParam(
        name       : 'user_id',
        type       : 'int',
        description: 'The database users.id of the users to add the PAT to.',
        required   : true
    )]
    #[BodyParam(
        name       : 'name',
        type       : 'string',
        description: 'The PAT name.',
        required   : true
    )]
    #[BodyParam(
        name       : 'token_abilities',
        type       : 'array',
        description: 'An array of token abilities',
        required   : true
    )]
    public function store(): JsonResponse
    {
        /**
         * The validation array.
         */
        $validationArray = [
            'user_id' => [
                'required',
                Rule::exists('users', 'id'),
            ],
            'name' => [
                'required',
                'string',
            ],
            'token_abilities' => [
                'required',
                'array',
            ],
            'token_abilities.*' => [
                Rule::in(PersonalAccessTokenAbility::cases()),
            ],
        ];

        $messages = [
            'token_abilities' => 'Please ensure the token has at least 1 ability associated to it.',
        ];
        $validator = Validator::make($this->request->all(), $validationArray, $messages);

        if ($validator->fails()) {

            $this->responseCode = 400;
            $this->message      = $validator->errors()
                ->first();

        }
        else {

            try {

                $userId         = $this->request->get('user_id');
                $name           = $this->request->get('name');
                $tokenAbilities = $this->request->get('token_abilities');

                $user = User::find($userId);

                $token = $user->createToken($name, $tokenAbilities);

                $this->message = ApiResponse::RESPONSE_SAVED->value;
                $this->data    = [
                    'token'  => $token->plainTextToken,
                    'secret' => Crypt::decrypt($token->accessToken->secret),
                ];

                event(new PersonalAccessTokenWasCreated($token->accessToken));

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
        description  : 'Retrieve a single PAT by ID.',
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
        $this->query = PersonalAccessToken::with($this->associatedData)
            ->select(
                [
                    'id',
                    'tokenable_type',
                    'tokenable_id',
                    'name',
                    'abilities',
                    'last_used_at',
                    'expires_at',
                    'created_at',
                ]
            );
        $this->query = $this->updateReadQueryBasedOnUrl();
        $this->data  = $this->query->find($id);

        return $this->respond();
    }

    /**
     * PUT/ {id}
     *
     * @param string $id
     *
     * @hideFromAPIDocumentation
     *
     * @return JsonResponse
     */
    public function update(string $id)
    {
        $this->responseCode = 403;
        $this->message      = ApiResponse::RESPONSE_METHOD_NOT_ALLOWED->value;

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
        description  : 'Remove a PAT. The API access for the PAT will be revoked.',
        authenticated: true
    )]
    public function destroy(string $id)
    {
        try {

            $model = PersonalAccessToken::find($id);

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
