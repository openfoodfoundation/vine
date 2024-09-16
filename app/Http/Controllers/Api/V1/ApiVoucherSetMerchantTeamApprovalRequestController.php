<?php

/** @noinspection PhpUndefinedMethodInspection */

/** @noinspection PhpUnusedParameterInspection */

namespace App\Http\Controllers\Api\V1;

use App\Enums\ApiResponse;
use App\Enums\VoucherSetMerchantTeamApprovalRequestStatus;
use App\Events\VoucherSetMerchantTeamApprovalRequest\VoucherSetMerchantTeamApprovalRequestWasApproved;
use App\Events\VoucherSetMerchantTeamApprovalRequest\VoucherSetMerchantTeamApprovalRequestWasRejected;
use App\Http\Controllers\Api\HandlesAPIRequests;
use App\Http\Controllers\Controller;
use App\Models\VoucherSetMerchantTeamApprovalRequest;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\Subgroup;

#[Group('App Endpoints')]
#[Subgroup('/voucher-set-merchant-team-approval-request', 'Retrieve voucher set merchant team approval request details.')]
class ApiVoucherSetMerchantTeamApprovalRequestController extends Controller
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
     * @hideFromAPIDocumentation
     *
     * @return JsonResponse
     */
    public function store(): JsonResponse
    {
        $this->responseCode = 403;
        $this->message      = ApiResponse::RESPONSE_METHOD_NOT_ALLOWED->value;

        return $this->respond();
    }

    /**
     * GET / {id}
     *
     * @hideFromAPIDocumentation
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
     * PUT / {id}
     *
     * @hideFromAPIDocumentation
     *
     * @param string $id
     *
     * @return JsonResponse
     */
    public function update(string $id)
    {
        $validationArray = [
            'approval_status' => [
                'required',
                'string',
                Rule::in([VoucherSetMerchantTeamApprovalRequestStatus::APPROVED->value, VoucherSetMerchantTeamApprovalRequestStatus::REJECTED->value]),
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

                $model = VoucherSetMerchantTeamApprovalRequest::find($id);

                if (!$model) {

                    $this->responseCode = 404;
                    $this->message      = ApiResponse::RESPONSE_NOT_FOUND->value;

                }
                else {

                    foreach ($validationArray as $key => $validationRule) {
                        $value = $this->request->get($key);
                        if ((isset($value))) {
                            $model->$key = $value;
                        }
                    }

                    $model->approval_status_last_updated_at = now();
                    $model->save();

                    $answer = $this->request->get('approval_status');

                    if ($answer == VoucherSetMerchantTeamApprovalRequestStatus::APPROVED->value) {
                        event(new VoucherSetMerchantTeamApprovalRequestWasApproved($model));
                    }

                    if ($answer == VoucherSetMerchantTeamApprovalRequestStatus::REJECTED->value) {
                        event(new VoucherSetMerchantTeamApprovalRequestWasRejected($model));
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
     * DELETE / {id}
     *
     * @hideFromAPIDocumentation
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
