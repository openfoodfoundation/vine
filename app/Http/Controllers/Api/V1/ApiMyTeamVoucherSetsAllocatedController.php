<?php

/** @noinspection PhpUnusedParameterInspection */

namespace App\Http\Controllers\Api\V1;

use App\Enums\ApiResponse;
use App\Exceptions\DisallowedApiFieldException;
use App\Http\Controllers\Api\HandlesAPIRequests;
use App\Http\Controllers\Controller;
use App\Models\VoucherSet;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Knuckles\Scribe\Attributes\Authenticated;
use Knuckles\Scribe\Attributes\Endpoint;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\QueryParam;
use Knuckles\Scribe\Attributes\Response;
use Knuckles\Scribe\Attributes\Subgroup;

#[Group('App Endpoints')]
#[Subgroup('/my-team-voucher-sets', 'Manage your team\'s voucher sets. Returns voucher sets allocated to your team.')]
class ApiMyTeamVoucherSetsAllocatedController extends Controller
{
    use HandlesAPIRequests;

    /**
     * Set the related data the GET request is allowed to ask for
     */
    public array $availableRelations = [
        'createdByTeam',
        'allocatedToServiceTeam',
        'voucherSetMerchantTeams.merchantTeam',
    ];

    public static array $searchableFields = [
        'created_at'
    ];

    /**
     * @throws DisallowedApiFieldException
     */
    #[Endpoint(
        title: 'GET /',
        description: 'Retrieve voucher sets. Automatically filtered to your current team.',
        authenticated: true
    )]
    #[Authenticated]
    #[QueryParam(
        name: 'cached',
        type: 'bool',
        description: 'Request the response to be cached. Default: `true`.',
        required: false,
        example: true
    )]
    #[QueryParam(
        name: 'page',
        type: 'int',
        description: 'The pagination page number.',
        required: false,
        example: 1
    )]
    #[QueryParam(
        name: 'limit',
        type: 'int',
        description: 'The number of entries returned per pagination page.',
        required: false,
        example: 50
    )]
    #[QueryParam(
        name: 'fields',
        type: 'string',
        description: 'Comma-separated list of database fields to return within the object.',
        required: false,
        example: 'id,created_at'
    )]
    #[QueryParam(
        name: 'orderBy',
        type: 'comma-separated',
        description: 'Order the data by a given field. Comma-separated string.',
        required: false,
        example: 'orderBy=id,desc'
    )]
    #[QueryParam(
        name: 'orderBy[]',
        type: 'comma-separated',
        description: 'Compound `orderBy`. Order the data by a given field. Comma-separated string. Can not be used in conjunction as standard `orderBy`.',
        required: false,
        example: 'orderBy[]=id,desc&orderBy[]=created_at,asc'
    )]
    #[QueryParam(
        name: 'where',
        type: 'comma-separated',
        description: 'Filter the request on a single field. Key-Value or Key-Operator-Value comma-separated string.',
        required: false,
        example: 'where=id,like,*550e*'
    )]
    #[QueryParam(
        name: 'where[]',
        type: 'comma-separated',
        description: 'Compound `where`. Use when you need to filter on multiple `where`\'s. Note only AND is possible; ORWHERE is not available.',
        required: false,
        example: 'where[]=id,like,*550e*&where[]=created_at,>=,2024-01-01'
    )]
    #[Response(
        content: '{"meta":{"responseCode":200,"limit":50,"offset":0,"message":"","cached":false,"availableRelations":[]},"data":{"current_page":1,"data":[{"id": "2e9978b3-130a-3291-bd8f-246215c6d04d","created_by_team_id": 1, "created_at": "2024-01-01 00:00:00"}],"first_page_url": "https:\/\/vine.test\/api\/v1\/my-team-voucher-sets?page=1","from":null,"last_page":1,"last_page_url": "https:\/\/vine.test\/api\/v1\/my-team-voucher-sets?page=1","links":[{"url":null,"label":"&laquo; Previous","active":false},{"https:\/\/vine.test\/api\/v1\/my-team-voucher-sets?page=1","label":"1","active":true},{"url":null,"label":"Next &raquo;","active":false}],"next_page_url":null,"path": "https:\/\/vine.test\/api\/v1\/my-team-voucher-sets","per_page":1,"prev_page_url":null,"to":null,"total":0}}',
        status: 200,
        description: ''
    )]
    public function index(): JsonResponse
    {
        $this->query = VoucherSet::where('allocated_to_service_team_id', Auth::user()->current_team_id)
            ->with($this->associatedData);

        $this->query = $this->updateReadQueryBasedOnUrl();

        $this->data = $this->query->paginate($this->limit);

        return $this->respond();
    }

    /**
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
     * GET /{id}
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
     * @param string $id
     *
     * @return JsonResponse
     *
     * @hideFromAPIDocumentation
     */
    public function destroy(string $id)
    {
        $this->responseCode = 403;
        $this->message      = ApiResponse::RESPONSE_METHOD_NOT_ALLOWED->value;

        return $this->respond();
    }
}
