<?php

/** @noinspection PhpUnusedParameterInspection */

namespace App\Http\Controllers\Api\V1;

use App\Enums\ApiResponse;
use App\Http\Controllers\Api\HandlesAPIRequests;
use App\Http\Controllers\Controller;
use App\Models\Voucher;
use App\Models\VoucherBeneficiaryDistribution;
use App\Models\VoucherSet;
use Crypt;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Knuckles\Scribe\Attributes\Authenticated;
use Knuckles\Scribe\Attributes\BodyParam;
use Knuckles\Scribe\Attributes\Endpoint;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\Response;
use Knuckles\Scribe\Attributes\Subgroup;
use Str;

#[Group('App Endpoints')]
#[Subgroup('/voucher-beneficiary-distribution', 'API for create voucher beneficiary distributions')]
class ApiVoucherBeneficiaryDistributionController extends Controller
{
    use HandlesAPIRequests;

    /**
     * Set the related data the GET request is allowed to ask for
     */
    public array $availableRelations = [];

    public static array $searchableFields = [
    ];

    /**
     * @hideFromAPIDocumentation
     */
    public function index(): JsonResponse
    {
        $this->responseCode = 403;
        $this->message      = ApiResponse::RESPONSE_METHOD_NOT_ALLOWED->value;

        return $this->respond();
    }

    #[Endpoint(
        title: 'POST /',
        description: 'Create a new beneficiary distribution.',
        authenticated: true
    )]
    #[Authenticated]
    #[BodyParam(
        name: 'voucher_id',
        type: 'uuid',
        description: 'The UUID of the voucher to be distributed. required_without:resend_beneficiary_distribution_id',
        required: false
    )]
    #[BodyParam(
        name: 'beneficiary_email',
        type: 'email',
        description: 'The email for the voucher beneficiary. required_without:resend_beneficiary_distribution_id',
        required: false
    )]
    #[BodyParam(
        name: 'resend_beneficiary_distribution_id',
        type: 'integer',
        description: 'The database ID for a beneficiary distribution that you would like to resend. Must belong to your team. required_without:beneficiary_email,voucher_id',
        required: false
    )]
    #[Response(
        content: '{"meta":{"responseCode":200,"limit":50,"offset":0,"message":"Saved.","cached":false,"availableRelations":[]},"data":{"id": "1234"}}',
        status: 200,
        description: '',
    )]
    public function store(): JsonResponse
    {
        /**
         * The validation array.
         */
        $validationArray = [
            'voucher_id' => [
                'required_without:resend_beneficiary_distribution_id',
                'string',
                Rule::exists('vouchers', 'id'),
            ],
            'beneficiary_email' => [
                'required_without:resend_beneficiary_distribution_id',
                'email',
            ],
            'resend_beneficiary_distribution_id' => [
                'sometimes',
                Rule::exists('voucher_beneficiary_distributions', 'id'),
            ],
        ];

        $validator = Validator::make($this->request->all(), $validationArray);

        if ($validator->fails()) {

            $this->responseCode = 400;
            $this->message      = $validator->errors()->first();

            return $this->respond();
        }

        try {

            $voucherId        = $this->request->get('voucher_id');
            $beneficiaryEmail = $this->request->get('beneficiary_email');

            /**
             * The client is re-sending an existing distribution.
             */
            if ($this->request->has('resend_beneficiary_distribution_id')) {

                $distributionId = $this->request->get('resend_beneficiary_distribution_id');

                $voucherBeneficiaryDistribution = VoucherBeneficiaryDistribution::find($distributionId);

                if (!$voucherBeneficiaryDistribution) {
                    $this->responseCode = 404;
                    $this->message      = ApiResponse::RESPONSE_NOT_FOUND->value;

                    return $this->respond();
                }

                /**
                 * Update values based on prior distribution as they will have been null
                 */
                $voucherId        = $voucherBeneficiaryDistribution->voucher_id;
                $beneficiaryEmail = Crypt::decrypt($voucherBeneficiaryDistribution->beneficiary_email_encrypted);
            }

            /**
             * Ensure the authenticated user team owns the voucher
             */
            $voucher = Voucher::where(function ($query) {
                $query->where('created_by_team_id', Auth::user()->current_team_id)
                    ->orWhere('allocated_to_service_team_id', Auth::user()->current_team_id);
            })
                ->find($voucherId);

            if (!$voucher) {
                $this->responseCode = 404;
                $this->message      = ApiResponse::RESPONSE_NOT_FOUND->value;

                return $this->respond();
            }

            /**
             * Ensure the voucher set belongs to the user's current team
             */
            $voucherSet = VoucherSet::where(function ($query) {
                $query->where('created_by_team_id', Auth::user()->current_team_id)
                    ->orWhere('allocated_to_service_team_id', Auth::user()->current_team_id);
            })
                ->find($voucher->voucher_set_id);

            if (!$voucherSet) {
                $this->responseCode = 404;
                $this->message      = ApiResponse::RESPONSE_NOT_FOUND->value;

                return $this->respond();
            }

            /**
             * Ensure this voucher has not been sent to someone else.
             */
            $priorDistribution = VoucherBeneficiaryDistribution::where('voucher_id', $voucherId)
                ->first();

            if (
                $priorDistribution &&
                (Crypt::decrypt($priorDistribution->beneficiary_email_encrypted) != Str::trim($beneficiaryEmail))
            ) {
                $this->responseCode = 400;
                $this->message      = ApiResponse::RESPONSE_ALREADY_EXISTS->value;

                return $this->respond();
            }

            /**
             * Create a new instance
             */
            $model                              = new VoucherBeneficiaryDistribution();
            $model->voucher_id                  = $voucher->id;
            $model->voucher_set_id              = $voucher->voucher_set_id;
            $model->beneficiary_email_encrypted = Crypt::encrypt($beneficiaryEmail);
            $model->created_by_user_id          = Auth::id();
            $model->save();

            $this->data = $model;

        }
        catch (Exception $e) {

            $this->responseCode = 500;
            $this->message      = ApiResponse::RESPONSE_ERROR->value . ': "' . $e->getMessage() . '".';

        }

        return $this->respond();
    }

    /**
     * @hideFromAPIDocumentation
     *
     * @param int $id
     */
    public function show(int $id)
    {
        $this->responseCode = 403;
        $this->message      = ApiResponse::RESPONSE_METHOD_NOT_ALLOWED->value;

        return $this->respond();
    }

    /**
     * @hideFromAPIDocumentation
     *
     * @param string $id
     */
    public function update(string $id)
    {
        $this->responseCode = 403;
        $this->message      = ApiResponse::RESPONSE_METHOD_NOT_ALLOWED->value;

        return $this->respond();
    }

    /**
     * @hideFromAPIDocumentation
     *
     * @param string $id
     */
    public function destroy(string $id)
    {
        $this->responseCode = 403;
        $this->message      = ApiResponse::RESPONSE_METHOD_NOT_ALLOWED->value;

        return $this->respond();
    }
}
