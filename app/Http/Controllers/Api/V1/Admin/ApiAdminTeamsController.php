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

class ApiAdminTeamsController extends Controller
{
    use HandlesAPIRequests;

    /**
     * Set the related data the GET request is allowed to ask for
     */
    public array $availableRelations = [];

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
    public function update(string $id)
    {
        /**
         * The validation array.
         */
        $validationArray = [
            'name' => [
                'sometimes',
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
