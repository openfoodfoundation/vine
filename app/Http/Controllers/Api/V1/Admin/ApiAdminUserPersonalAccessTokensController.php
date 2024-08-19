<?php

/** @noinspection PhpUnusedParameterInspection */

/** @noinspection PhpUndefinedMethodInspection */

namespace App\Http\Controllers\Api\V1\Admin;

use App\Enums\ApiResponse;
use App\Enums\PersonalAccessTokenAbility;
use App\Exceptions\DisallowedApiFieldException;
use App\Http\Controllers\Api\HandlesAPIRequests;
use App\Http\Controllers\Controller;
use App\Models\PersonalAccessToken;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

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
    public function index(): JsonResponse
    {
        $this->query = PersonalAccessToken::with($this->associatedData)->select(['id', 'tokenable_id', 'name', 'last_used_at', 'expires_at']);
        $this->query = $this->updateReadQueryBasedOnUrl();
        $this->data  = $this->query->paginate($this->limit);

        return $this->respond();
    }

    /**
     * POST /
     *
     * @return JsonResponse
     */
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
                $this->data    = ['token' => $token->plainTextToken];

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
    public function show(string $id)
    {
        $this->query = PersonalAccessToken::with($this->associatedData)->select(['id', 'tokenable_type', 'tokenable_id', 'name', 'abilities', 'last_used_at', 'expires_at']);
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
