<?php

/** @noinspection PhpUndefinedMethodInspection */

/** @noinspection PhpUnusedParameterInspection */

namespace App\Http\Controllers\Api\V1\Admin;

use App\Enums\ApiResponse;
use App\Exceptions\DisallowedApiFieldException;
use App\Http\Controllers\Api\HandlesAPIRequests;
use App\Http\Controllers\Controller;
use App\Jobs\TeamUsers\SendTeamUserInvitationEmail;
use App\Models\TeamUser;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ApiAdminTeamUsersController extends Controller
{
    use HandlesAPIRequests;

    /**
     * Set the related data the GET request is allowed to ask for
     */
    public array $availableRelations = [
        'team',
        'user',
    ];

    public static array $searchableFields = [
        'id',
        'team_id',
        'user_id',
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
        $this->query = TeamUser::with($this->associatedData);
        $this->query = $this->updateReadQueryBasedOnUrl();
        $this->data  = $this->query->paginate($this->limit);

        return $this->respond();
    }

    /**
     * @return JsonResponse
     *                      POST /
     */
    public function store(): JsonResponse
    {
        /**
         * The validation array.
         */
        $validationArray = [
            'team_id' => [
                'required',
                Rule::exists('teams', 'id'),
            ],
            'user_id' => [
                'required',
                Rule::exists('users', 'id'),
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

                /**
                 * If they already exist, do not create the team user
                 */
                $teamId = $this->request->get('team_id');
                $userId = $this->request->get('user_id');
                $model = TeamUser::where('team_id', $teamId)
                                 ->where('user_id', $userId)
                                 ->first();

                if(!$model)
                {
                    $model = new TeamUser();

                    foreach ($validationArray as $key => $validationRule) {
                        $value = $this->request->get($key);
                        if ((isset($value))) {
                            $model->$key = $value;
                        }
                    }

                    $model->save();
                }


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
     * @param string $id
     *
     * @return JsonResponse
     *                      GET / {id}
     */
    public function show(string $id)
    {
        $this->responseCode = 403;
        $this->message      = ApiResponse::RESPONSE_METHOD_NOT_ALLOWED->value;

        return $this->respond();
    }

    /**
     * @param string $id
     *
     * @return JsonResponse
     *                      PUT/ {id}
     */
    public function update(string $id)
    {
        $validationArray = [
            'send_invite_email' => [
                'sometimes',
                'boolean',
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

                $model = TeamUser::find($id);

                if (!$model) {

                    $this->responseCode = 404;
                    $this->message      = ApiResponse::RESPONSE_NOT_FOUND->value;

                }
                else {

                    if ($this->request->has('send_invite_email')) {
                        $sendInviteEmail = $this->request->get('send_invite_email');

                        if ($sendInviteEmail) {
                            $model->invitation_sent_at = now();
                            $model->save();

                            dispatch(new SendTeamUserInvitationEmail($model));
                        }
                    }

                    $this->message = ApiResponse::RESPONSE_UPDATED->value;
                    $this->data    = $model;
                }

            }
            catch (Exception $e) {

                $this->responseCode = 500;
                $this->message      = ApiResponse::RESPONSE_ERROR->value . ': "' . $e->getMessage() . '".';

            }
        }

        return $this->respond();
    }

    /**
     * DELETE /{id}
     *
     * @param string $id
     *
     * @return JsonResponse
     */
    public function destroy(string $id)
    {
        try {

            $model = TeamUser::find($id);

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
