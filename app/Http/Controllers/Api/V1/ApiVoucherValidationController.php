<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\ApiResponse;
use App\Http\Controllers\Api\HandlesAPIRequests;
use App\Http\Controllers\Controller;
use App\Models\Voucher;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Knuckles\Scribe\Attributes\Authenticated;
use Knuckles\Scribe\Attributes\Endpoint;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\Response;
use Knuckles\Scribe\Attributes\Subgroup;

#[Group('App Endpoints')]
#[Subgroup('/voucher-validation', 'API for validating a voucher')]
class ApiVoucherValidationController extends Controller
{
    use HandlesAPIRequests;

    /**
     * Set the related data the GET request is allowed to ask for
     *
     * @var array
     */
    public array $availableRelations = [];

    public static array $searchableFields      = [];
    private static string $hmacSignatureHeader = 'X-HMAC-Signature';

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
        description: 'Verify the validity of a voucher',
        authenticated: true
    )]
    #[Authenticated]
    #[Response(
        content: '',
        status: 200,
        description: '',
    )]
    public function store(): JsonResponse
    {
        $validationArray = [
            'type' => [
                'required',
                'string',
                Rule::in([
                    'voucher_id',
                    'voucher_code',
                ]),
            ],
            'value' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {

                    $type = $this->request->input('type');

                    if ($type === 'voucher_id') {
                        if (!Voucher::where('id', $value)->exists()) {
                            $fail('The selected voucher ID is invalid.');
                        }
                    }
                    elseif ($type === 'voucher_short_code') {
                        if (!Voucher::where('voucher_short_code', $value)->exists()) {
                            $fail('The selected voucher code is invalid.');
                        }
                    }
                },
            ],
        ];

        $validator = Validator::make($this->request->all(), $validationArray);

        if ($validator->fails()) {
            $this->responseCode = 400;
            $this->message      = $validator->errors()->first();

            return $this->respond();
        }

        try {

            $voucherIdentifier = $this->request->get('value');

            $voucher = match ($this->request->get('type')) {
                'voucher_id'   => Voucher::find($voucherIdentifier),
                'voucher_code' => Voucher::where('voucher_short_code', $voucherIdentifier)->first(),
            };

            if (!$voucher) {
                $this->responseCode = 404;
                $this->message      = ApiResponse::RESPONSE_NOT_FOUND->value;

                return $this->respond();
            }

            $requestSignature = $this->request->header(self::$hmacSignatureHeader, default: '');

            $data = $this->request->getContent();

            $verificationSignature = hash_hmac('sha256', $data, 'Secret');

            if (!hash_equals($verificationSignature, $requestSignature)) {
                $this->responseCode = 401;
                $this->message      = ApiResponse::RESPONSE_HMAC_SIGNATURE_INCORRECT->value;

                return $this->respond();
            }

            $this->data = $voucher;

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
     *
     * @return JsonResponse
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
