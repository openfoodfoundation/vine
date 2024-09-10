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
            ],
        ];

        $validator = Validator::make($this->request->all(), $validationArray);

        if ($validator->fails()) {
            $this->responseCode = 400;
            $this->message      = $validator->errors()->first();

            return $this->respond();
        }

        try {

            /**
             * Recreate the expected HMAC signature
             */
            $data = $this->request->getContent();

            $expectedSignature = hash_hmac('sha256', $data, 'Secret');

            /**
             * Test the provided HMAC signature against the expected one
             */
            $providedSignature = $this->request->header(self::$hmacSignatureHeader, default: '');

            if (!hash_equals($expectedSignature, $providedSignature)) {
                $this->responseCode = 401;
                $this->message      = ApiResponse::RESPONSE_HMAC_SIGNATURE_INCORRECT->value;

                return $this->respond();
            }

            /**
             * Retrieve the voucher, if it exists, using the provided identifier
             */
            $identifierType    = $this->request->get('type');
            $voucherIdentifier = $this->request->get('value');

            $voucher = match ($identifierType) {
                'voucher_id'   => Voucher::find($voucherIdentifier),
                'voucher_code' => Voucher::where('voucher_short_code', $voucherIdentifier)->first(),
            };

            if (!$voucher) {
                $this->responseCode = 404;
                $this->message      = ApiResponse::RESPONSE_NOT_FOUND->value;

                return $this->respond();
            }

            /**
             * At this point everything checks out, we can return the voucher details
             */
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
