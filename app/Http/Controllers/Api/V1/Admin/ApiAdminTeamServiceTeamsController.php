<?php

/** @noinspection PhpUndefinedMethodInspection */
/** @noinspection PhpUnusedParameterInspection */

namespace App\Http\Controllers\Api\V1\Admin;

use App\Enums\ApiResponse;
use App\Http\Controllers\Api\HandlesAPIRequests;
use App\Http\Controllers\Controller;
use App\Models\TeamServiceTeam;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ApiAdminTeamServiceTeamsController extends Controller
{
    use HandlesAPIRequests;

    /**
     * Set the related data the GET request is allowed to ask for
     */
    public array $availableRelations = [];

    public static array $searchableFields = [];

    /**
     * @return JsonResponse
     *                      GET /
     */
    public function index(): JsonResponse
    {
        $this->query = TeamServiceTeam::with($this->associatedData);
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
            'service_team_id' => [
                'required',
                Rule::exists('teams', 'id'),
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

                $model = new TeamServiceTeam();

                foreach ($validationArray as $key => $validationRule) {
                    $value = $this->request->get($key);
                    if ((isset($value))) {
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
        $this->responseCode = 403;
        $this->message      = ApiResponse::RESPONSE_METHOD_NOT_ALLOWED->value;

        return $this->respond();
    }

    /**
     * @param string $id
     *
     * @return JsonResponse
     *                      DELETE / {id}
     */
    public function destroy(string $id)
    {
        try {

            $model = TeamServiceTeam::find($id);

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
