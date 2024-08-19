<?php

/** @noinspection PhpUnusedParameterInspection */

namespace App\Http\Controllers\Api\V1;

use App\Enums\ApiResponse;
use App\Enums\PersonalAccessTokenAbility;
use App\Http\Controllers\Api\HandlesAPIRequests;
use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\TeamUser;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Knuckles\Scribe\Attributes\Authenticated;
use Knuckles\Scribe\Attributes\Endpoint;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\Response;

#[Group('/shops', 'API for managing shops')]
class ApiShopsController extends Controller
{
    use HandlesAPIRequests;

    /**
     * Set the related data the GET request is allowed to ask for
     */
    public array $availableRelations = [];

    public static array $searchableFields = [
    ];

    #[Endpoint(
        title: 'GET /',
        authenticated: true
    )]
    #[Authenticated]
    #[Response(
        status: 403,
        description: 'Method Not Allowed',
    )]
    public function index(): JsonResponse
    {
        $this->responseCode = 403;
        $this->message      = ApiResponse::RESPONSE_METHOD_NOT_ALLOWED->value;

        return $this->respond();
    }

    #[Endpoint(
        title: 'POST /',
        description: 'Create a new shop.',
        authenticated: true
    )]
    #[Authenticated]
    #[Response(
        content: '{"meta":{"responseCode":200,"limit":50,"offset":0,"message":"Saved. Here is the API Token for the user linked to this new team. It will only be displayed ONCE, so please store it in a secure manner.","cached":false,"availableRelations":[]},"data":"{TOKEN}"',
        status: 200,
        description: '',
    )]
    public function store(): JsonResponse
    {
        /**
         * The validation array.
         */
        $validationArray = [
            'shop_name'  => [
                'required',
                'string',
            ],
            'user_email' => [
                'required',
                'email',
            ],
            'user_name'  => [
                'required',
                'string',
            ],
        ];

        $validator = Validator::make($this->request->all(), $validationArray);

        if ($validator->fails()) {

            $this->responseCode = 400;
            $this->message      = $validator->errors()->first();

            return $this->respond();
        }

        try {

            /**
             * Create a Team for the shop if one does not exist
             */
            $shopName = $this->request->get('shop_name');

            $shopTeam = Team::where('name', $shopName)->first();

            if (is_null($shopTeam)) {
                $shopTeam       = new Team();
                $shopTeam->name = $shopName;
                $shopTeam->save();
            }

            /**
             * Create a User for the shop if one does not exist
             */
            $userEmail = $this->request->get('user_email');

            $shopUser = User::where('email', $userEmail)->first();

            if (is_null($shopUser)) {
                $shopUser                  = new User();
                $shopUser->email           = $userEmail;
                $shopUser->password        = $userEmail;
                $shopUser->name            = $this->request->get('user_name');
                $shopUser->current_team_id = $shopTeam->id;
                $shopUser->save();
            }

            /**
             * Create a TeamUser for the shop if one does not exist
             */
            $shopTeamUser = TeamUser::where('team_id', $shopTeam->id)->where('user_id', $shopUser->id)->first();

            if (is_null($shopTeamUser)) {
                $shopTeamUser          = new TeamUser();
                $shopTeamUser->user_id = $shopUser->id;
                $shopTeamUser->team_id = $shopTeam->id;
                $shopTeamUser->save();
            }

            /**
             * Create a PAT for the shop that has redemption capabilities
             */
            $token = $shopUser->createToken(
                name: $shopTeam->name,
                abilities: PersonalAccessTokenAbility::redemptionAppTokenAbilities(),
            );

            $this->message = ApiResponse::RESPONSE_SAVED->value . '. Here is the API Token for the user linked to this new team. It will only be displayed ONCE, so please store it in a secure manner.';
            $this->data    = $token->plainTextToken;

        } catch (Exception $e) {

            $this->responseCode = 500;
            $this->message      = ApiResponse::RESPONSE_ERROR->value . ': "' . $e->getMessage() . '".';

        }

        return $this->respond();
    }

    #[Endpoint(
        title: 'GET /{id}',
        description: 'Get shop with ID {id}',
        authenticated: true
    )]
    #[Authenticated]
    #[Response(
        status: 403,
        description: 'Method Not Allowed',
    )]
    public function show(int $id)
    {
        $this->responseCode = 403;
        $this->message      = ApiResponse::RESPONSE_METHOD_NOT_ALLOWED->value;

        return $this->respond();
    }

    #[Endpoint(
        title: 'PUT /{id}',
        description: 'Update shop with ID {id}.',
        authenticated: true
    )]
    #[Authenticated]
    #[Response(
        status: 403,
        description: 'Method Not Allowed',
    )]
    public function update(string $id)
    {
        $this->responseCode = 403;
        $this->message      = ApiResponse::RESPONSE_METHOD_NOT_ALLOWED->value;

        return $this->respond();
    }

    #[Endpoint(
        title: 'DELETE /',
        description: 'DELETE shop with ID {id}.',
        authenticated: true
    )]
    #[Authenticated]
    #[Response(
        status: 403,
        description: 'Method Not Allowed',
    )]
    public function destroy(string $id)
    {
        $this->responseCode = 403;
        $this->message      = ApiResponse::RESPONSE_METHOD_NOT_ALLOWED->value;

        return $this->respond();
    }
}
