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
use App\Models\VoucherSet;
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
use Knuckles\Scribe\Attributes\Response;
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
        'voucherSet',
        'voucherSet.createdByTeam',
        'voucherSet.allocatedToServiceTeam',
        'voucherSet.voucherSetMerchantTeamApprovalActionedRecord.merchantUser',
        'merchantTeam',
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

    /**
     * @throws DisallowedApiFieldException
     */
    #[Endpoint(
        title        : 'GET /',
        description  : 'Retrieve my voucher set merchant approval requests. Automatically filtered to you and your current team.',
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
        content    : '{"meta":{"responseCode":200,"limit":50,"offset":0,"message":"","cached":false,"availableRelations":[]},"data":{"current_page":1,"data":[{"id": "2e9978b3-130a-3291-bd8f-246215c6d04d","created_by_team_id": 1, "created_at": "2024-01-01 00:00:00"}],"first_page_url": "https:\/\/vine.test\/api\/v1\/my-team-voucher-sets?page=1","from":null,"last_page":1,"last_page_url": "https:\/\/vine.test\/api\/v1\/my-team-voucher-sets?page=1","links":[{"url":null,"label":"&laquo; Previous","active":false},{"https:\/\/vine.test\/api\/v1\/my-team-voucher-sets?page=1","label":"1","active":true},{"url":null,"label":"Next &raquo;","active":false}],"next_page_url":null,"path": "https:\/\/vine.test\/api\/v1\/my-team-voucher-sets","per_page":1,"prev_page_url":null,"to":null,"total":0}}',
        status     : 200,
        description: ''
    )]
    public function index(): JsonResponse
    {
        $this->query = VoucherSetMerchantTeamApprovalRequest::where(function ($query) {
            $query->where('merchant_user_id', Auth::id())
                ->where('merchant_team_id', Auth::user()->current_team_id);
        })->with($this->associatedData);

        $this->query = $this->updateReadQueryBasedOnUrl();
        $this->data  = $this->query->paginate($this->limit);

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

                    $voucherSet = VoucherSet::find($model->voucherSet->id);

                    if ($answer == VoucherSetMerchantTeamApprovalRequestStatus::APPROVED->value) {

                        if ($voucherSet) {
                            $voucherSet->merchant_approval_request_id = $model->id;
                            $voucherSet->save();
                        }

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
