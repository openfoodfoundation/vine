<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Enums\ApiResponse;
use App\Http\Controllers\Api\HandlesAPIRequests;
use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\User;
use App\Models\Voucher;
use App\Models\VoucherSet;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class ApiAdminSearchController extends Controller
{
    use HandlesAPIRequests;

    /**
     * Set the related data the GET request is allowed to ask for
     */
    public array $availableRelations = [];

    public static array $searchableFields = [
        'id',
    ];

    /**
     * GET /
     *
     * @return JsonResponse
     */
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

        $searchTerm          = $this->request->get('query');
        $search              = '%' . $searchTerm . '%';
        $this->data['users'] = User::where('name', 'LIKE', $search)
            ->orWhere('email', 'LIKE', $search)
            ->get();
        $this->data['teams'] = Team::where('name', 'LIKE', $search)
            ->get();
        $this->data['vouchers'] = Voucher::where('id', 'LIKE', $search)
            ->get();
        $this->data['voucherSets'] = VoucherSet::where('id', 'LIKE', $search)
            ->get();

        return $this->respond();
    }

    /**
     * POST /
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
     * @return JsonResponse
     */
    public function destroy(string $id)
    {
        $this->responseCode = 403;
        $this->message      = ApiResponse::RESPONSE_METHOD_NOT_ALLOWED->value;

        return $this->respond();
    }
}
