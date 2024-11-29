<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Enums\ApiResponse;
use App\Exceptions\DisallowedApiFieldException;
use App\Http\Controllers\Api\HandlesAPIRequests;
use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\TeamMerchantTeam;
use App\Models\VoucherSet;
use App\Models\VoucherSetMerchantTeam;
use App\Models\VoucherSetMerchantTeamApprovalRequest;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Knuckles\Scribe\Attributes\Authenticated;
use Knuckles\Scribe\Attributes\BodyParam;
use Knuckles\Scribe\Attributes\Endpoint;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\QueryParam;
use Knuckles\Scribe\Attributes\Response;
use Knuckles\Scribe\Attributes\Subgroup;
use Knuckles\Scribe\Attributes\UrlParam;

#[Group('Admin Endpoints')]
#[Subgroup('/admin/voucher-set-merchant-teams', 'Admin endpoint for management of voucher set merchant team associations')]
class ApiAdminVoucherSetMerchantTeamsController extends Controller
{
    use HandlesAPIRequests;

    /**
     * Set the related data the GET request is allowed to ask for
     */
    public array $availableRelations = [
        'merchantTeam',
        'voucherSet',
    ];

    public static array $searchableFields = [

    ];

    /**
     * @throws DisallowedApiFieldException
     */
    #[Endpoint(
        title        : 'GET /',
        description  : 'Retrieve voucher set merchant teams.',
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
        content    : '',
        status     : 200,
        description: ''
    )]
    public function index(): JsonResponse
    {

        $this->query = VoucherSetMerchantTeam::with($this->associatedData);
        $this->query = $this->updateReadQueryBasedOnUrl();
        $this->data  = $this->query->paginate($this->limit);

        return $this->respond();
    }

    /**
     * @return JsonResponse
     */
    #[Endpoint(
        title        : 'POST /',
        description  : 'Create a new voucher set merchant team. Sends an email to team members to request involvement.',
        authenticated: true
    )]
    #[Authenticated]
    #[BodyParam(
        name       : 'voucher_set_id',
        type       : 'uuid',
        description: 'The ID of the voucher set being associated with the merchant team',
        required   : true
    )]
    #[BodyParam(
        name       : 'merchant_team_id',
        type       : 'integer',
        description: 'The IDs of the merchant team(s) being associated to the voucher set',
        required   : true
    )]
    #[Response(
        content    : '',
        status     : 200,
        description: '',
    )]
    public function store(): JsonResponse
    {
        $validationArray = [
            'voucher_set_id'   => [
                'required',
                'string',
                Rule::exists('voucher_sets', 'id'),
            ],
            'merchant_team_id' => [
                'required',
                'integer',
                Rule::exists('teams', 'id'),
            ],
        ];

        $validator = Validator::make($this->request->all(), $validationArray);

        if ($validator->fails()) {

            $this->responseCode = 400;
            $this->message      = $validator->errors()
                                            ->first();

        } else {

            $voucherSetId          = $this->request->get('voucher_set_id');
            $merchantTeamId        = $this->request->get('merchant_team_id');
            $voucherSet            = VoucherSet::find($voucherSetId);
            $voucherSetServiceTeam = Team::find($voucherSet->allocated_to_service_team_id);
            $merchantRelationship  = TeamMerchantTeam::where('team_id', $voucherSetServiceTeam->id)
                                                     ->where('merchant_team_id', $merchantTeamId)->first();

            /**
             * Ensure that the merchant team is a merchant for the service team
             */
            if (!$merchantRelationship) {
                $this->responseCode = 400;
                $this->message      = ApiResponse::RESPONSE_INVALID_MERCHANT_TEAM_FOR_SERVICE_TEAM->value;
                return $this->respond();
            }

            $model = VoucherSetMerchantTeam::where(
                'voucher_set_id',
                $voucherSetId
            )->where(
                'merchant_team_id',
                $merchantTeamId
            )->first();

            if (!$model) {
                $model = new VoucherSetMerchantTeam();

                foreach ($validationArray as $key => $validationRule) {
                    $value = $this->request->get($key);
                    if (isset($value)) {
                        $model->$key = $value;
                    }
                }

                $model->save();
            }

            $this->message = ApiResponse::RESPONSE_SAVED->value;
            $this->data    = $model;
            try {
            } catch (Exception $e) {

                $this->responseCode = 500;
                $this->message      = ApiResponse::RESPONSE_ERROR->value . ': "' . $e->getMessage() . '".';

            }
        }

        return $this->respond();
    }

    /**
     * GET /{id}
     *
     * @param string $id
     *
     * @return JsonResponse
     *
     * @throws DisallowedApiFieldException
     */
    #[Endpoint(
        title        : 'GET /{id}',
        description  : 'Retrieve a single resource.',
        authenticated: true,
    )]
    #[Authenticated]
    #[UrlParam(
        name       : 'id',
        type       : 'integer',
        description: 'ID of the resource.',
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
    #[Response(
        content    : '{
  "meta": {
    "responseCode": 200,
    "limit": 50,
    "offset": 0,
    "message": "",
    "cached": true,
    "cached_at": "2024-08-13 08:58:19",
    "availableRelations": []
  },
  "data": {"id": 1234, "created_at": "2024-01-01 00:00:00"}
}',
        status     : 200,
        description: ''
    )]
    public function show(string $id)
    {

        $this->query = VoucherSetMerchantTeam::with($this->associatedData);
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
     *
     * @hideFromAPIDocumentation
     */
    public function update(string $id)
    {
        $this->responseCode = 403;
        $this->message      = ApiResponse::RESPONSE_METHOD_NOT_ALLOWED->value;

        return $this->respond();
    }

    /**
     * DELETE /{id}
     *
     * @param int $id
     *
     * @return JsonResponse
     */
    #[Endpoint(
        title        : 'Delete /{id}',
        description  : 'Remove a voucher set team merchant association. {id} is the DB resource for the association.',
        authenticated: true
    )]
    public function destroy(string $id)
    {
        try {

            $model = VoucherSetMerchantTeam::find($id);

            if (!$model) {

                $this->responseCode = 404;
                $this->message      = ApiResponse::RESPONSE_NOT_FOUND->value;

                return $this->respond();
            }

            /**
             * Delete any requests
             */
            VoucherSetMerchantTeamApprovalRequest::where('merchant_team_id', $model->merchant_team_id)->delete();

            /**
             * Delete the model
             */
            $model->delete();
            $this->message = ApiResponse::RESPONSE_DELETED->value;

        } catch (Exception $e) {

            $this->responseCode = 500;
            $this->message      = ApiResponse::RESPONSE_ERROR->value . ':' . $e->getMessage();

        }

        return $this->respond();

    }
}
