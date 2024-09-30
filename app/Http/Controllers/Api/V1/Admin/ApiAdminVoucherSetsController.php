<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Enums\ApiResponse;
use App\Exceptions\DisallowedApiFieldException;
use App\Http\Controllers\Api\HandlesAPIRequests;
use App\Http\Controllers\Controller;
use App\Models\TeamMerchantTeam;
use App\Models\VoucherSet;
use App\Models\VoucherSetMerchantTeam;
use App\Models\VoucherTemplate;
use Auth;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
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
#[Subgroup('/admin/voucher-sets', 'Admin endpoint for unrestricted management of voucher sets')]
class ApiAdminVoucherSetsController extends Controller
{
    use HandlesAPIRequests;

    /**
     * Set the related data the GET request is allowed to ask for
     */
    public array $availableRelations = [
        'createdByTeam',
        'allocatedToServiceTeam',
    ];

    public static array $searchableFields = [
        'created_at',
        'allocated_to_service_team_id',
        'created_by_team_id',
    ];

    /**
     * @throws DisallowedApiFieldException
     */
    #[Endpoint(
        title        : 'GET /',
        description  : 'Retrieve vouchers.',
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

        $this->query = VoucherSet::with($this->associatedData);
        $this->query = $this->updateReadQueryBasedOnUrl();
        $this->data  = $this->query->paginate($this->limit);

        return $this->respond();
    }

    /**
     * @return JsonResponse
     */
    #[Endpoint(
        title        : 'POST /',
        description  : 'Create a new voucher set.',
        authenticated: true
    )]
    #[Authenticated]
    #[BodyParam(
        name       : 'is_test',
        type       : 'boolean',
        description: 'Whether the voucher set is for testing purposes',
        required   : true
    )]
    #[BodyParam(
        name       : 'allocated_to_service_team_id',
        type       : 'integer',
        description: 'The ID of the service team being allocated to',
        required   : true
    )]
    #[BodyParam(
        name       : 'merchant_team_ids',
        type       : 'array',
        description: 'The IDs of the merchant team(s) being assigned to',
        required   : true
    )]
    #[BodyParam(
        name       : 'funded_by_team_id',
        type       : 'integer',
        description: 'The ID of the funding team associated with the voucher set',
        required   : false
    )]
    #[BodyParam(
        name       : 'voucher_template_id',
        type       : 'integer',
        description: 'The ID of the voucher template to be used for the voucher set',
        required   : true
    )]
    #[BodyParam(
        name       : 'total_set_value',
        type       : 'integer',
        description: 'The total value of the voucher set',
        required   : true
    )]
    #[BodyParam(
        name       : 'denominations',
        type       : 'array',
        description: 'An array describing the voucher denominations by number and value',
        required   : true
    )]
    #[BodyParam(
        name       : 'expires_at',
        type       : 'string',
        description: 'The expiration date of the voucher set, if one exists',
        required   : false
    )]
    #[BodyParam(
        name       : 'voucher_set_type',
        type       : 'string',
        description: 'The type of the voucher set, must be one of: "food equity" or "promotion"',
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
            'is_test'                      => [
                'required',
                'boolean',
            ],
            'allocated_to_service_team_id' => [
                'required',
                'integer',
                Rule::exists('teams', 'id'),
            ],
            'merchant_team_ids'            => [
                'required',
                'array',
            ],
            'merchant_team_ids.*'          => [
                'integer',
                Rule::exists('teams', 'id'),
            ],
            'funded_by_team_id'            => [
                'sometimes',
                'nullable',
                'integer',
                Rule::exists('teams', 'id'),
            ],
            'voucher_template_id'          => [
                'required',
                'integer',
                Rule::exists('voucher_templates', 'id'),
            ],
            'total_set_value'              => [
                'required',
                'integer',
            ],
            'denominations'                => [
                'required',
                'array',
            ],
            'expires_at'                   => [
                'sometimes',
                'string',
                'nullable',
            ],
            'voucher_set_type'             => [
                'required',
                'string',
            ],
        ];

        $validator = Validator::make($this->request->all(), $validationArray);

        if ($validator->fails()) {

            $this->responseCode = 400;
            $this->message      = $validator->errors()
                                            ->first();

            return $this->respond();

        }

        try {

            /**
             * Ensure the API user has a country against their current team.
             */
            if (!isset(Auth::user()->currentTeam->country_id)) {
                $this->message      = ApiResponse::RESPONSE_INVALID_TEAM->value;
                $this->responseCode = 400;

                return $this->respond();
            }


            DB::beginTransaction();

            $merchantTeamIds = $this->request->get('merchant_team_ids');
            $serviceTeamId   = $this->request->get('allocated_to_service_team_id');

            /**
             * Get the service team's merchants list as an array of IDs
             */
            $teamMerchantTeams = TeamMerchantTeam::where('team_id', $serviceTeamId)
                                                 ->pluck('merchant_team_id')
                                                 ->toArray();

            /**
             * Validate that the merchant ID are all merchants for the service team.
             */
            foreach ($merchantTeamIds as $merchantTeamId) {
                if (!in_array($merchantTeamId, $teamMerchantTeams)) {
                    $this->message      = ApiResponse::RESPONSE_INVALID_MERCHANT_TEAM_FOR_SERVICE_TEAM->value;
                    $this->responseCode = 400;

                    return $this->respond();
                }
            }

            /**
             * Validate that the current user's team owns the template, if provided
             */
            $templateId = $this->request->get('voucher_template_id');
            if ($templateId) {
                $template = VoucherTemplate::where('team_id', Auth::user()->current_team_id)->find($templateId);

                if (!$template) {
                    $this->message      = ApiResponse::RESPONSE_INVALID_VOUCHER_TEMPLATE_FOR_TEAM->value;
                    $this->responseCode = 400;

                    return $this->respond();
                }
            }

            $model = new VoucherSet();
            foreach ($validationArray as $key => $validationRule) {
                $value = $this->request->get($key);
                if (isset($value)) {
                    if ($key == 'denominations') {
                        $model->denomination_json = json_encode($value);

                        continue;
                    }

                    /**
                     * Merchant team ids are used
                     * down below via a linking model
                     */
                    if ($key == 'merchant_team_ids') {
                        continue;
                    }

                    $model->$key = $value;

                }
            }

            $model->created_by_user_id  = Auth::id();
            $model->created_by_team_id  = Auth::user()->current_team_id;
            $model->currency_country_id = Auth::user()->currentTeam->country_id;
            $model->save();

            foreach ($merchantTeamIds as $merchantTeamId) {
                $voucherSetMerchantTeam                   = new VoucherSetMerchantTeam();
                $voucherSetMerchantTeam->voucher_set_id   = $model->id;
                $voucherSetMerchantTeam->merchant_team_id = $merchantTeamId;
                $voucherSetMerchantTeam->save();
            }

            $this->message = ApiResponse::RESPONSE_SAVED->value;
            $this->data    = $model;

            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();

            $this->responseCode = 500;
            $this->message      = ApiResponse::RESPONSE_ERROR->value . ': "' . $e->getMessage() . '".';

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
        description  : 'Retrieve a single voucher.',
        authenticated: true,
    )]
    #[Authenticated]
    #[UrlParam(
        name       : 'id',
        type       : 'uuid',
        description: 'Voucher Set ID.',
        example    : '550e8400-e29b-41d4-a716-446655440000'
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

        $this->query = VoucherSet::with($this->associatedData);
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
     * @hideFromAPIDocumentation
     *
     * @param string $id
     *
     * @return JsonResponse
     */
    public function destroy(string $id)
    {
        try {

            $model = VoucherSet::find($id);

            if (!$model) {

                $this->responseCode = 404;
                $this->message      = ApiResponse::RESPONSE_NOT_FOUND->value;

                return $this->respond();
            }

            $model->delete();
            $this->message = ApiResponse::RESPONSE_DELETED->value;

        } catch (Exception $e) {

            $this->responseCode = 500;
            $this->message      = ApiResponse::RESPONSE_ERROR->value . ':' . $e->getMessage();

        }

        return $this->respond();

    }
}
