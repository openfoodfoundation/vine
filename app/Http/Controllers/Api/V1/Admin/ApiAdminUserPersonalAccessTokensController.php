<?php

/** @noinspection PhpUnusedParameterInspection */

/** @noinspection PhpUndefinedMethodInspection */

namespace App\Http\Controllers\Api\V1\Admin;

use App\Enums\ApiResponse;
use App\Enums\PersonalAccessTokenAbility;
use App\Http\Controllers\Api\HandlesAPIRequests;
use App\Http\Controllers\Controller;
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
    public array $availableRelations = [];

    public static array $searchableFields = [];

    /**
     * GET /
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $this->responseCode = 403;
        $this->message      = ApiResponse::RESPONSE_METHOD_NOT_ALLOWED->value;

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

        $validator = Validator::make($this->request->all(), $validationArray);

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

                $token = $user->createToken(
                    name: $name,
                    abilities: $tokenAbilities,
                    teamId: $user->current_team_id
                );

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
     */
    public function show(string $id)
    {
        $this->responseCode = 403;
        $this->message      = ApiResponse::RESPONSE_METHOD_NOT_ALLOWED->value;

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
        $this->responseCode = 403;
        $this->message      = ApiResponse::RESPONSE_METHOD_NOT_ALLOWED->value;

        return $this->respond();
    }
}
