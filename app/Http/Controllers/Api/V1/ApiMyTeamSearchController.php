<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\ApiResponse;
use App\Http\Controllers\Api\HandlesAPIRequests;
use App\Http\Controllers\Controller;
use App\Models\TeamUser;
use App\Models\User;
use App\Models\Voucher;
use App\Models\VoucherSet;
use Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Knuckles\Scribe\Attributes\Endpoint;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\Subgroup;

#[Group('App Endpoints')]
#[Subgroup('/my-team-search', 'Search for system objects your team owns or is associated to.')]
class ApiMyTeamSearchController extends Controller
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
    #[Endpoint(
        title        : 'GET /',
        description  : 'Perform a search.',
        authenticated: true
    )]
    public function index(): JsonResponse
    {
        $validationArray = [
            'query' => [
                'required',
                'string',
                'min:3',
            ],
        ];

        $validator = Validator::make($this->request->all(), $validationArray);

        if ($validator->fails()) {
            $this->responseCode = 400;
            $this->message      = $validator->errors()->first();

            return $this->respond();
        }

        $searchTerm = $this->request->get('query');
        $search     = '%' . $searchTerm . '%';

        $userIds = TeamUser::where('team_id', Auth::user()->current_team_id)->pluck('user_id')->toArray();

        $this->data['users'] = User::whereIn('id', $userIds)
            ->where(function ($query) use ($search) {
                $query->where('name', 'LIKE', $search)
                    ->orWhere('email', 'LIKE', $search);
            })->get();

        $this->data['vouchers'] = Voucher::where(function ($query) {
            $query->where('allocated_to_service_team_id', Auth::user()->current_team_id)
                ->orWhere('created_by_team_id', Auth::user()->current_team_id);
        })->where(function ($query) use ($search) {
            $query->where('id', 'LIKE', $search)
                ->orWhere('voucher_short_code', 'LIKE', $search);
        })->get();

        $this->data['voucherSets'] = VoucherSet::where(function ($query) {
            $query->where('allocated_to_service_team_id', Auth::user()->current_team_id)
                ->orWhere('created_by_team_id', Auth::user()->current_team_id);
        })->where('id', 'LIKE', $search)
            ->get();

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
     * GET /{id}
     *
     * @param int $id
     *
     * @hideFromAPIDocumentation
     *
     * @return JsonResponse
     */
    public function show(int $id)
    {
        $this->responseCode = 403;
        $this->message      = ApiResponse::RESPONSE_METHOD_NOT_ALLOWED->value;

        return $this->respond();
    }

    /**
     * PUT /{id}
     *
     * @param string $id
     *
     * @hideFromAPIDocumentation
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
     * @param string $id
     *
     * @hideFromAPIDocumentation
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
