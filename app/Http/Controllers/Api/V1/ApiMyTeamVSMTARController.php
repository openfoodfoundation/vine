<?php

/** @noinspection PhpUndefinedMethodInspection */

/** @noinspection PhpUnusedParameterInspection */

namespace App\Http\Controllers\Api\V1;

use App\Enums\ApiResponse;
use App\Enums\VoucherSetMerchantTeamApprovalRequestStatus;
use App\Events\VoucherSetMerchantTeamApprovalRequest\VoucherSetMerchantTeamApprovalRequestWasApproved;
use App\Events\VoucherSetMerchantTeamApprovalRequest\VoucherSetMerchantTeamApprovalRequestWasRejected;
use App\Exceptions\DisallowedApiFieldException;
use App\Http\Controllers\Api\HandlesAPIRequests;
use App\Http\Controllers\Controller;
use App\Models\VoucherSetMerchantTeamApprovalRequest;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Knuckles\Scribe\Attributes\Authenticated;
use Knuckles\Scribe\Attributes\Endpoint;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\QueryParam;
use Knuckles\Scribe\Attributes\Subgroup;
use Knuckles\Scribe\Attributes\UrlParam;

#[Group('App Endpoints')]
#[Subgroup('/voucher-set-merchant-team-approval-request', 'Retrieve voucher set merchant team approval request details.')]
class ApiMyTeamVSMTARController extends Controller
{
    use HandlesAPIRequests;

    /**
     * Set the related data the GET request is allowed to ask for
     */
    public array $availableRelations = [
        'voucherSet.createdByTeam',
        'voucherSet.allocatedToServiceTeam',
        'merchantTeam',
    ];

    public static array $searchableFields = [];

    /**
     * GET /
     *
     * @hideFromAPIDocumentation
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
     *
     * @throws DisallowedApiFieldException
     */
    #[Endpoint(
        title        : 'GET /{id}',
        description  : 'Retrieve a single voucher set merchant team approval request for user.',
        authenticated: true,
    )]
    #[Authenticated]
    #[UrlParam(
        name       : 'id',
        type       : 'int',
        description: 'ID.',
        example    : '1234'
    )]
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
    public function show(string $id)
    {
        $this->query = VoucherSetMerchantTeamApprovalRequest::where('merchant_user_id', Auth::id())
            ->with($this->associatedData);
        $this->query = $this->updateReadQueryBasedOnUrl();
        $this->data  = $this->query->find($id);

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
                Rule::in(
                    [
                        VoucherSetMerchantTeamApprovalRequestStatus::APPROVED->value,
                        VoucherSetMerchantTeamApprovalRequestStatus::REJECTED->value,
                    ]
                ),
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

                $model = VoucherSetMerchantTeamApprovalRequest::where('merchant_user_id', Auth::id())->find($id);

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
