<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\ApiResponse;
use App\Exceptions\DisallowedApiFieldException;
use App\Http\Controllers\Api\HandlesAPIRequests;
use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\TeamUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Knuckles\Scribe\Attributes\Authenticated;
use Knuckles\Scribe\Attributes\Endpoint;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\QueryParam;
use Knuckles\Scribe\Attributes\Response;

#[Group('/my-teams', 'Teams you are a member of.')]
class ApiMyTeamsController extends Controller
{
    use HandlesAPIRequests;

    /**
     * Set the related data the GET request is allowed to ask for
     */
    public array $availableRelations = [

    ];

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
    #[Endpoint(
        title        : 'GET /',
        description  : 'Retrieve your teams. Automatically filtered to your profile.',
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
        content    : '{"meta": {"responseCode": 200, "limit": 50, "offset": 0, "message": "", "cached": false, "availableRelations": []}, "data": {"current_page": 1, "data": [{"id": 1, "name": "Team A", "created_at": "2024-08-16T06:54:28.000000Z", "updated_at": "2024-08-16T06:54:28.000000Z", "deleted_at": null}, {"id": 2, "name": "Team B", "created_at": "2024-08-16T06:54:29.000000Z", "updated_at": "2024-08-16T06:54:29.000000Z", "deleted_at": null}], "first_page_url": "https:\/\/vine.openfoodnetwork.org.au\/api\/v1\/my-teams?page=1", "from": 1, "last_page": 1, "last_page_url": "https:\/\/vine.openfoodnetwork.org.au\/api\/v1\/my-teams?page=1", "links": [{"url": null, "label": "&laquo; Previous", "active": false}, {"url": "https:\/\/vine.openfoodnetwork.org.au\/api\/v1\/my-teams?page=1", "label": "1", "active": true}, {"url": null, "label": "Next &raquo;", "active": false}], "next_page_url": null, "path": "https:\/\/vine.openfoodnetwork.org.au\/api\/v1\/my-teams", "per_page": 50, "prev_page_url": null, "to": 2, "total": 2}}',
        status     : 200,
        description: ''
    )]
    public function index(): JsonResponse
    {
        $myTeamIds = TeamUser::where('user_id', Auth::id())->pluck('team_id')->toArray();

        $this->query = Team::with($this->associatedData)->whereIn('id', $myTeamIds);
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
        $this->responseCode = 403;
        $this->message      = ApiResponse::RESPONSE_METHOD_NOT_ALLOWED->value;

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
