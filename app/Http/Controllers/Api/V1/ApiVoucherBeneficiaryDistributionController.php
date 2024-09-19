<?php

/** @noinspection PhpUnusedParameterInspection */

namespace App\Http\Controllers\Api\V1;

use App\Enums\ApiResponse;
use App\Http\Controllers\Api\HandlesAPIRequests;
use App\Http\Controllers\Controller;
use App\Models\Voucher;
use App\Models\VoucherBeneficiaryDistribution;
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
        description: 'The UUID of the voucher to be distributed.',
        required: true
    )]
    #[BodyParam(
        name: 'beneficiary_email',
        type: 'email',
        description: 'The email for the voucher beneficiary.',
        required: true
    )]
    #[Response(
        content: '{"meta":{"responseCode":200,"limit":50,"offset":0,"message":"Saved. Here is the API Token for the user linked to this new team. It will only be displayed ONCE, so please store it in a secure manner.","cached":false,"availableRelations":[]},"data":{"token": "123|kjfhsgiufsghkjsfghkfgsjh"}}',
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
                'required',
                'string',
                Rule::exists('vouchers', 'id'),
            ],
            'beneficiary_email' => [
                'required',
                'email',
            ],
        ];

        $validator = Validator::make($this->request->all(), $validationArray);

        if ($validator->fails()) {

            $this->responseCode = 400;
            $this->message      = $validator->errors()->first();

            return $this->respond();
        }

        try {

            $voucherId            = $this->request->get('voucher_id');
            $existingDistribution = VoucherBeneficiaryDistribution::where('voucher_id', $voucherId)->first();

            if ($existingDistribution) {
                $this->responseCode = 400;
                $this->message      = 'Voucher has already been distributed to beneficiary.';

                return $this->respond();
            }

            $voucher          = Voucher::find($voucherId);
            $beneficiaryEmail = $this->request->get('beneficiary_email');

            $model                     = new VoucherBeneficiaryDistribution();
            $model->voucher_id         = $voucher->id;
            $model->voucher_set_id     = $voucher->voucher_set_id;
            $model->beneficiary_email  = Crypt::encrypt($beneficiaryEmail);
            $model->created_by_user_id = Auth::id();
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
