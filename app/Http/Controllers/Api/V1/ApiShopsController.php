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
use Knuckles\Scribe\Attributes\BodyParam;
use Knuckles\Scribe\Attributes\Endpoint;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\Response;
use Knuckles\Scribe\Attributes\Subgroup;

#[Group('App Endpoints')]
#[Subgroup('/shops', 'API for managing shops')]
class ApiShopsController extends Controller
{
    use HandlesAPIRequests;

    /**
     * Set the related data the GET request is allowed to ask for
     */
    public array $availableRelations = [];

    public static array $searchableFields = [
    ];

    /**
     * @hideFromAPIDocumentation
     */
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
    #[BodyParam(
        name       : 'shop_name',
        type       : 'string',
        description: 'The name of the shop.',
        required   : true
    )]
    #[BodyParam(
        name       : 'user_email',
        type       : 'email',
        description: 'The email for the shop merchant user.',
        required   : true
    )]
    #[BodyParam(
        name       : 'user_name',
        type       : 'string',
        description: 'The name of the user.',
        required   : true
    )]
    #[Response(
        content: '{"meta":{"responseCode":200,"limit":50,"offset":0,"message":"Saved. Here is the API Token for the user linked to this new team. It will only be displayed ONCE, so please store it in a secure manner.","cached":false,"availableRelations":[]},"data":{"token": "123|kjfhsgiufsghkjsfghkfgsjh"}}',
        status: 200,
        description: '',
    )]
    public function store(): JsonResponse
    {
        /**
         * The validation array.
         */
        $validationArray = [
            'shop_name' => [
                'required',
                'string',
            ],
            'user_email' => [
                'required',
                'email',
            ],
            'user_name' => [
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

            if (!$shopTeam) {
                $shopTeam       = new Team();
                $shopTeam->name = $shopName;
                $shopTeam->save();
            }

            /**
             * Create a User for the shop if one does not exist
             */
            $userEmail = $this->request->get('user_email');

            $shopUser = User::where('email', $userEmail)->first();

            if (!$shopUser) {
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

            if (!$shopTeamUser) {
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
            $this->data    = [
                'token' => $token->plainTextToken,
            ];

        }
        catch (Exception $e) {

            $this->responseCode = 500;
            $this->message      = ApiResponse::RESPONSE_ERROR->value . ': "' . $e->getMessage() . '".';

        }

        return $this->respond();
    }

    /**
     * @hideFromAPIDocumentation
     *
     * @param int $id
     */
    public function show(int $id)
    {
        $this->responseCode = 403;
        $this->message      = ApiResponse::RESPONSE_METHOD_NOT_ALLOWED->value;

        return $this->respond();
    }

    /**
     * @hideFromAPIDocumentation
     *
     * @param string $id
     */
    public function update(string $id)
    {
        $this->responseCode = 403;
        $this->message      = ApiResponse::RESPONSE_METHOD_NOT_ALLOWED->value;

        return $this->respond();
    }

    /**
     * @hideFromAPIDocumentation
     *
     * @param string $id
     */
    public function destroy(string $id)
    {
        $this->responseCode = 403;
        $this->message      = ApiResponse::RESPONSE_METHOD_NOT_ALLOWED->value;

        return $this->respond();
    }
}
