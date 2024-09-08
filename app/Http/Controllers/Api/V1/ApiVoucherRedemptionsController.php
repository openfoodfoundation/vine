<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\ApiResponse;
use App\Http\Controllers\Api\HandlesAPIRequests;
use App\Http\Controllers\Controller;
use App\Models\Voucher;
use App\Models\VoucherRedemption;
use App\Models\VoucherSet;
use App\Models\VoucherSetMerchantTeam;
use App\Services\VoucherService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Knuckles\Scribe\Attributes\Authenticated;
use Knuckles\Scribe\Attributes\Endpoint;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\Response;
use Knuckles\Scribe\Attributes\Subgroup;

#[Group('App Endpoints')]
#[Subgroup('/voucher-redemptions', 'API for managing voucher redemptions')]
class ApiVoucherRedemptionsController extends Controller
{
    use HandlesAPIRequests;

    /**
     * Set the related data the GET request is allowed to ask for
     *
     * @var array
     */
    public array $availableRelations = [];

    public static array $searchableFields = [];

    /**
     * @hideFromAPIDocumentation
     * GET /
     */
    public function index(): JsonResponse
    {
        $this->responseCode = 403;
        $this->message      = ApiResponse::RESPONSE_METHOD_NOT_ALLOWED->value;

        return $this->respond();
    }

    /**
     * POST /
     */
    #[Endpoint(
        title: 'POST /',
        description: 'Create a new voucher redemption.',
        authenticated: true
    )]
    #[Authenticated]
    #[Response(
        content: '{"meta":{"responseCode":200,"limit":50,"offset":0,"message":"Saved"},"data":{"voucher_id":"ec70cf3b-f4ab-3ce0-9201-c10362aa2f07","voucher_set_id":"6f644113-b836-3e34-8ed7-27c40c10d2c1","redeemed_by_user_id":1,"redeemed_by_team_id":1,"redeemed_amount":1,"is_test":0,"updated_at":"2024-09-06T03:31:20.000000Z","created_at":"2024-09-06T03:31:20.000000Z","id":1}}',
        status: 200,
        description: '',
    )]
    public function store(): JsonResponse
    {
        $validationArray = [
            'voucher_id' => [
                'required',
                Rule::exists('vouchers', 'id'),
            ],
            'voucher_set_id' => [
                'required',
                Rule::exists('voucher_sets', 'id'),
            ],
            'amount' => [
                'integer',
                'min:1',
            ],
        ];

        $validator = Validator::make($this->request->all(), $validationArray);

        if ($validator->fails()) {
            $this->responseCode = 400;
            $this->message      = $validator->errors()->first();

            return $this->respond();
        }

        try {

            $voucherId    = $this->request->get('voucher_id');
            $voucherSetId = $this->request->get('voucher_set_id');
            $amount       = $this->request->get('amount');

            /**
             * Ensure the voucher exists with the set
             */
            $voucher = Voucher::where('voucher_set_id', $voucherSetId)->find($voucherId);

            if (!$voucher) {
                $this->responseCode = 404;
                $this->message      = ApiResponse::RESPONSE_NOT_FOUND->value;

                return $this->respond();
            }

            /**
             * Ensure the users current team is a merchant for the voucher set.
             */
            $voucherSetMerchantTeamIds = VoucherSetMerchantTeam::where('voucher_set_id', $voucherSetId)
                                                               ->pluck('merchant_team_id')
                                                               ->unique()
                                                               ->toArray();

            if (!in_array(Auth::user()->current_team_id, $voucherSetMerchantTeamIds)) {
                $this->responseCode = 400;
                $this->message      = ApiResponse::RESPONSE_INVALID_MERCHANT_TEAM->value;

                return $this->respond();
            }

            /**
             * Update the remaining amount, just in case
             */
            VoucherService::updateVoucherAmountRemaining($voucher);
            $voucher->refresh();

            if ($voucher->last_redemption_at > now()->subMinute()) {

                $this->responseCode = 429;
                $this->message      = ApiResponse::RESPONSE_REDEMPTION_FAILED_TOO_MANY_ATTEMPTS->value;

                return $this->respond();
            }

            if ($voucher->voucher_value_remaining <= 0) {
                $this->responseCode = 400;
                $this->message      = ApiResponse::RESPONSE_REDEMPTION_FAILED_VOUCHER_ALREADY_FULLY_REDEEMED->value;
                return $this->respond();
            }

            if ($amount > $voucher->voucher_value_remaining) {
                $this->responseCode = 400;
                $this->message      = ApiResponse::RESPONSE_REDEMPTION_FAILED_REQUESTED_AMOUNT_TOO_HIGH->value;
                return $this->respond();
            }

            $redemption                      = new VoucherRedemption();
            $redemption->voucher_id          = $voucherId;
            $redemption->voucher_set_id      = $voucherSetId;
            $redemption->redeemed_by_user_id = Auth::id();
            $redemption->redeemed_by_team_id = Auth::user()->current_team_id;
            $redemption->redeemed_amount     = $amount;
            $redemption->is_test             = $voucher->is_test;
            $redemption->save();

            $voucher->last_redemption_at = now();
            $voucher->save();

            VoucherService::updateVoucherAmountRemaining($voucher);

            $this->message = ApiResponse::RESPONSE_REDEMPTION_SUCCESSFUL->value;
            $this->data    = $redemption;

        }
        catch (Exception $e) {
            $this->responseCode = 500;
            $this->message      = ApiResponse::RESPONSE_ERROR->value . ':' . $e->getMessage();
        }

        return $this->respond();
    }

    /**
     * @hideFromAPIDocumentation
     * GET /{id}
     *
     * @param int $id
     *
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $this->responseCode = 403;
        $this->message      = ApiResponse::RESPONSE_METHOD_NOT_ALLOWED->value;

        return $this->respond();
    }

    /**
     * @hideFromAPIDocumentation
     * PUT /{id}
     *
     * @param int $id
     */
    public function update(int $id): JsonResponse
    {
        $this->responseCode = 403;
        $this->message      = ApiResponse::RESPONSE_METHOD_NOT_ALLOWED->value;

        return $this->respond();
    }

    /**
     * @hideFromAPIDocumentation
     * DELETE /{id}
     *
     * @param int $id
     *
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $this->responseCode = 403;
        $this->message      = ApiResponse::RESPONSE_METHOD_NOT_ALLOWED->value;

        return $this->respond();
    }
}
