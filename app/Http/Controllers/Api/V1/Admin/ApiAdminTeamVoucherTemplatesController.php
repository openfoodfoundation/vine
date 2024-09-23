<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Enums\ApiResponse;
use App\Exceptions\DisallowedApiFieldException;
use App\Http\Controllers\Api\HandlesAPIRequests;
use App\Http\Controllers\Controller;
use App\Models\VoucherTemplate;
use App\Services\VoucherTemplateService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ApiAdminTeamVoucherTemplatesController extends Controller
{
    use HandlesAPIRequests;

    /**
     * Set the related data the GET request is allowed to ask for
     *
     * @var array
     */
    public array $availableRelations = [
        'team',
    ];

    public static array $searchableFields = [
        'team_id',
        'archived_at'
    ];

    /**
     * GET /
     *
     * @throws DisallowedApiFieldException
     */
    public function index(): JsonResponse
    {
        $this->query = VoucherTemplate::with($this->associatedData);
        $this->query = $this->updateReadQueryBasedOnUrl();
        $this->data  = $this->query->paginate($this->limit);

        return $this->respond();
    }

    /**
     * POST /
     */
    public function store(): JsonResponse
    {
        $validationArray = [
            'team_id' => [
                'required',
                Rule::exists('teams', 'id'),
            ],
            'voucher_template_path' => [
                'required',
                'string',
            ],
            'voucher_qr_size_px' => [
                'required',
                'integer',
            ],
            'voucher_qr_x' => [
                'required',
                'integer',
            ],
            'voucher_qr_y' => [
                'required',
                'integer',
            ],
            'voucher_code_size_px' => [
                'required',
                'integer',
            ],
            'voucher_code_x' => [
                'required',
                'integer',
            ],
            'voucher_code_y' => [
                'required',
                'integer',
            ],
            'voucher_expiry_size_px' => [
                'required',
                'integer',
            ],
            'voucher_expiry_x' => [
                'required',
                'integer',
            ],
            'voucher_expiry_y' => [
                'required',
                'integer',
            ],
            'voucher_value_size_px' => [
                'required',
                'integer',
            ],
            'voucher_value_x' => [
                'required',
                'integer',
            ],
            'voucher_value_y' => [
                'required',
                'integer',
            ],
            'overlay_font_path' => [
                'required',
                'string',
            ],
        ];

        $validator = Validator::make($this->request->all(), $validationArray);

        if ($validator->fails()) {
            $this->responseCode = 400;
            $this->message      = $validator->errors()
                ->first();
        }
        else {
            try {
                $model = new VoucherTemplate();
                foreach ($validationArray as $key => $validationRule) {
                    $value = $this->request->get($key);
                    if (isset($value)) {
                        $model->$key = $value;
                    }
                }
                $model->created_by_user_id = Auth::id();
                $model->save();

                $templateExists = Storage::exists($model->voucher_template_path);
                if ($templateExists) {
                    $fileBits           = explode('.', $model->voucher_template_path);
                    $ext                = end($fileBits);
                    $templateCopyName   = $model->voucher_template_path . '.example.' . $ext;
                    $templateCopyExists = Storage::exists($templateCopyName);

                    if (!$templateCopyExists) {
                        Storage::copy($model->voucher_template_path, $templateCopyName);
                    }

                    $model->voucher_example_template_path = $templateCopyName;
                    $model->saveQuietly();

                    VoucherTemplateService::generateWorkingVoucherTemplate($model);
                }

                $this->data    = $model;
                $this->message = ApiResponse::RESPONSE_SAVED->value;

            }
            catch (Exception $exception) {
                $this->responseCode = 400;
                $this->message      = ApiResponse::RESPONSE_ERROR->value . ': ' . $exception->getMessage();
            }
        }

        /**
         * Respond
         */
        return $this->respond();
    }

    /**
     * GET /{id}
     *
     * @param int $id
     *
     * @throws DisallowedApiFieldException
     */
    public function show(int $id): JsonResponse
    {
        $this->query = VoucherTemplate::with($this->associatedData);
        $this->query = $this->updateReadQueryBasedOnUrl();
        $this->data  = $this->query->find($id);

        return $this->respond();
    }

    /**
     * PUT /{id}
     *
     * @param int $id
     */
    public function update(int $id): JsonResponse
    {
        $validationArray = [
            'voucher_template_path' => [
                'required',
                'string',
            ],
            'voucher_qr_size_px' => [
                'sometimes',
                'integer',
            ],
            'voucher_qr_x' => [
                'sometimes',
                'integer',
            ],
            'voucher_qr_y' => [
                'sometimes',
                'integer',
            ],
            'voucher_code_size_px' => [
                'sometimes',
                'integer',
            ],
            'voucher_code_x' => [
                'sometimes',
                'integer',
            ],
            'voucher_code_y' => [
                'sometimes',
                'integer',
            ],
            'voucher_code_prefix' => [
                'sometimes',
                'nullable',
            ],
            'voucher_expiry_size_px' => [
                'sometimes',
                'integer',
            ],
            'voucher_expiry_x' => [
                'sometimes',
                'integer',
            ],
            'voucher_expiry_y' => [
                'sometimes',
                'nullable',
                'integer',
            ],
            'voucher_expiry_prefix' => [
                'sometimes',
                'nullable',
            ],
            'voucher_value_size_px' => [
                'sometimes',
                'integer',
            ],
            'voucher_value_x' => [
                'sometimes',
                'integer',
            ],
            'voucher_value_y' => [
                'sometimes',
                'integer',
            ],
            'voucher_value_prefix' => [
                'sometimes',
                'nullable',
            ],
            'overlay_font_path' => [
                'sometimes',
                'string',
            ],
            'archive' => [
                'sometimes',
                'boolean',
            ],
        ];

        $validator = Validator::make($this->request->all(), $validationArray);

        if ($validator->fails()) {
            $this->responseCode = 400;
            $this->message      = $validator->errors()
                ->first();
        }
        else {
            $model = VoucherTemplate::find($id);

            if (!$model) {
                $this->responseCode = 404;
                $this->message      = ApiResponse::RESPONSE_NOT_FOUND->value;
            }
            else {

                $ignoreKeys = [
                    'archive',
                ];

                foreach ($validationArray as $key => $validationRule) {

                    if (in_array($key, $ignoreKeys)) {
                        continue;
                    }

                    $value = $this->request->get($key);
                    if (isset($value)) {
                        $model->$key = $value;
                    }
                }

                if ($this->request->has('archive')) {
                    $archiveRequest = $this->request->get('archive');

                    $model->archived_at = ($archiveRequest) ? now() : null;

                }

                $model->save();

                $templateExists = Storage::exists($model->voucher_template_path);
                if ($templateExists) {
                    $fileBits           = explode('.', $model->voucher_template_path);
                    $ext                = end($fileBits);
                    $templateCopyName   = $model->voucher_template_path . '.example.' . $ext;
                    $templateCopyExists = Storage::exists($templateCopyName);

                    if (!$templateCopyExists) {
                        Storage::copy($model->voucher_template_path, $templateCopyName);
                    }

                    $model->voucher_example_template_path = $templateCopyName;
                    $model->saveQuietly();

                    VoucherTemplateService::generateWorkingVoucherTemplate($model);
                }

                $this->message = 'hello?';
                $this->data    = $model;
                try {
                }
                catch (Exception $e) {
                    $this->responseCode = 500;
                    $this->message      = ApiResponse::RESPONSE_ERROR->value . ':' . $e->getMessage();
                }
            }
        }

        return $this->respond();
    }

    /**
     * DELETE /{id}
     *
     * @param int $id
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $model = VoucherTemplate::where('team_id', Auth::user()->current_team_id)
                ->find($id);

            if (!$model) {
                $this->responseCode = 404;
                $this->message      = ApiResponse::RESPONSE_NOT_FOUND->value;
            }
            else {
                $model->delete();

                $this->message = ApiResponse::RESPONSE_DELETED->value;
            }
        }
        catch (Exception $e) {
            $this->responseCode = 500;
            $this->message      = ApiResponse::RESPONSE_ERROR->value . ':' . $e->getMessage();
        }

        return $this->respond();
    }
}
