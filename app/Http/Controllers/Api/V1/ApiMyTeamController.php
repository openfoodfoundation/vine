<?php

/** @noinspection PhpUndefinedFieldInspection */

/** @noinspection PhpUnusedParameterInspection */

namespace App\Http\Controllers\Api\V1;

use App\Enums\ApiResponse;
use App\Exceptions\DisallowedApiFieldException;
use App\Http\Controllers\Api\HandlesAPIRequests;
use App\Http\Controllers\Controller;
use App\Models\Team;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Knuckles\Scribe\Attributes\Authenticated;
use Knuckles\Scribe\Attributes\Endpoint;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\QueryParam;
use Knuckles\Scribe\Attributes\Response;

#[Group('/my-team', 'Retrieve your team details.')]
class ApiMyTeamController extends Controller
{
    use HandlesAPIRequests;

    /**
     * Set the related data the GET request is allowed to ask for
     */
    public array $availableRelations = [
        'teamUsers.user',
    ];

    public static array $searchableFields = [];

    /**
     * GET /
     *
     * @return JsonResponse
     *
     * @throws DisallowedApiFieldException
     */
    #[Endpoint(
        title        : 'GET /',
        description  : 'Retrieve your team. Automatically filtered to your current team.',
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
        name       : 'fields',
        type       : 'string',
        description: 'Comma-separated list of database fields to return within the object.',
        required   : false,
        example    : 'id,created_at'
    )]
    #[Response(
        content    : '{"meta": {"responseCode": 200, "limit": 50, "offset": 0, "message": "", "cached": false, "availableRelations": []}, "data": {"current_page": 1, "data": {"id": 1, "name": "Team A", "created_at": "2024-08-16T06:54:28.000000Z", "updated_at": "2024-08-16T06:54:28.000000Z", "deleted_at": null}], "first_page_url": "https:\/\/vine.openfoodnetwork.org.au\/api\/v1\/my-teams?page=1", "from": 1, "last_page": 1, "last_page_url": "https:\/\/vine.openfoodnetwork.org.au\/api\/v1\/my-teams?page=1", "links": [{"url": null, "label": "&laquo; Previous", "active": false}, {"url": "https:\/\/vine.openfoodnetwork.org.au\/api\/v1\/my-teams?page=1", "label": "1", "active": true}, {"url": null, "label": "Next &raquo;", "active": false}], "next_page_url": null, "path": "https:\/\/vine.openfoodnetwork.org.au\/api\/v1\/my-teams", "per_page": 50, "prev_page_url": null, "to": 2, "total": 2}}',
        status     : 200,
        description: ''
    )]
    public function index(): JsonResponse
    {
        $this->query = Team::with($this->associatedData);
        $this->query = $this->updateReadQueryBasedOnUrl();
        $this->data  = $this->query->find(Auth::user()->current_team_id);

        return $this->respond();
    }

    /**
     * POST /
     * @hideFromAPIDocumentation
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
     * @hideFromAPIDocumentation
     * @param string $id
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
     * @hideFromAPIDocumentation
     * @param string $id
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
     * @hideFromAPIDocumentation
     * @param string $id
     * @return JsonResponse
     */
    public function destroy(string $id)
    {
        $this->responseCode = 403;
        $this->message      = ApiResponse::RESPONSE_METHOD_NOT_ALLOWED->value;

        return $this->respond();
    }
}
