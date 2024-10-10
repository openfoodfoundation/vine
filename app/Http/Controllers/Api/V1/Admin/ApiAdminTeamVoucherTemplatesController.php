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
use Knuckles\Scribe\Attributes\Authenticated;
use Knuckles\Scribe\Attributes\BodyParam;
use Knuckles\Scribe\Attributes\Endpoint;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\QueryParam;
use Knuckles\Scribe\Attributes\Response;
use Knuckles\Scribe\Attributes\Subgroup;

#[Group('Admin Endpoints')]
#[Subgroup('/admin/team-voucher-templates', 'API for managing a team\'s voucher templates')]
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
        'archived_at',
    ];

    /**
     * GET /
     *
     * @throws DisallowedApiFieldException
     */
    #[Endpoint(
        title        : 'GET /',
        description  : 'Retrieve team voucher templates',
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
        content    : '{"meta":{"responseCode":200,"limit":50,"offset":0,"message":"","cached":false,"availableRelations":[]},"data":{"current_page":1,"data":[{"id": "550e8400-e29b-41d4-a716-446655440000", "created_at": "2024-01-01 00:00:00"}],"first_page_url":"https:\/\/open-food-network-vouchers.test\/api\/v1\/admin\/system-statistics?page=1","from":null,"last_page":1,"last_page_url":"https:\/\/open-food-network-vouchers.test\/api\/v1\/admin\/system-statistics?page=1","links":[{"url":null,"label":"&laquo; Previous","active":false},{"url":"https:\/\/open-food-network-vouchers.test\/api\/v1\/admin\/system-statistics?page=1","label":"1","active":true},{"url":null,"label":"Next &raquo;","active":false}],"next_page_url":null,"path":"https:\/\/open-food-network-vouchers.test\/api\/v1\/admin\/system-statistics","per_page":1,"prev_page_url":null,"to":null,"total":0}}',
        status     : 200,
        description: ''
    )]
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
    #[Endpoint(
        title        : 'POST /',
        description  : 'Add a team voucher template.',
        authenticated: true
    )]
    #[Authenticated]
    #[BodyParam(
        name       : 'team_id',
        type       : 'int',
        description: 'The database teams.id of the team to add the voucher template to.',
        required   : true
    )]
    #[BodyParam(
        name       : 'voucher_template_path',
        type       : 'string',
        description: 'The path on the cloud storage system to the voucher template',
        required   : true
    )]
    #[BodyParam(
        name       : 'voucher_qr_size_px',
        type       : 'int',
        description: 'The size of the QR code on the template',
        required   : true
    )]
    #[BodyParam(
        name       : 'voucher_qr_x',
        type       : 'int',
        description: 'The X coordinate of the QR code',
        required   : true
    )]
    #[BodyParam(
        name       : 'voucher_qr_y',
        type       : 'int',
        description: 'The Y coordinate of the QR code',
        required   : true
    )]
    #[BodyParam(
        name       : 'voucher_code_size_px',
        type       : 'int',
        description: 'The size of the voucher short code on the template',
        required   : true
    )]
    #[BodyParam(
        name       : 'voucher_code_x',
        type       : 'int',
        description: 'The X coordinate of the voucher short code',
        required   : true
    )]
    #[BodyParam(
        name       : 'voucher_code_y',
        type       : 'int',
        description: 'The Y coordinate of the voucher short code',
        required   : true
    )]
    #[BodyParam(
        name       : 'voucher_expiry_size_px',
        type       : 'int',
        description: 'The size of the voucher expiry text on the template',
        required   : true
    )]
    #[BodyParam(
        name       : 'voucher_expiry_x',
        type       : 'int',
        description: 'The X coordinate of the voucher expiry text',
        required   : true
    )]
    #[BodyParam(
        name       : 'voucher_expiry_y',
        type       : 'int',
        description: 'The Y coordinate of the voucher expiry text',
        required   : true
    )]
    #[BodyParam(
        name       : 'voucher_value_size_px',
        type       : 'int',
        description: 'The size of the voucher value text on the template',
        required   : true
    )]
    #[BodyParam(
        name       : 'voucher_value_x',
        type       : 'int',
        description: 'The X coordinate of the voucher value text',
        required   : true
    )]
    #[BodyParam(
        name       : 'voucher_value_y',
        type       : 'int',
        description: 'The Y coordinate of the voucher value text',
        required   : true
    )]
    #[BodyParam(
        name       : 'overlay_font_path',
        type       : 'string',
        description: 'The path to the font being used in the template',
        required   : true
    )]
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
    #[Endpoint(
        title        : 'GET /{id}',
        description  : 'Retrieve a single voucher template by ID.',
        authenticated: true,
    )]
    #[Authenticated]
    #[QueryParam(
        name       : 'cached',
        type       : 'bool',
        description: 'Request the response to be cached. Default: `true`.',
        required   : false,
        example    : 1
    )]
    #[QueryParam(
        name       : 'fields',
        type       : 'string',
        description: 'Comma-separated list of database fields to return within the object.',
        required   : false,
        example    : 'id,created_at'
    )]
    #[Response(
        content    : '{
  "meta": {
    "responseCode": 200,
    "limit": 50,
    "offset": 0,
    "message": "",
    "cached": true,
    "cached_at": "2024-08-13 08:58:19",
    "availableRelations": []
  },
  "data": {"id": 1234, "created_at": "2024-01-01 00:00:00"}
}',
        status     : 200,
        description: ''
    )]
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
    #[Endpoint(
        title        : 'PUT /{id}',
        description  : 'Update a team voucher template.',
        authenticated: true
    )]
    #[Authenticated]
    #[BodyParam(
        name       : 'voucher_template_path',
        type       : 'string',
        description: 'The path on the cloud storage system to the voucher template',
        required   : false
    )]
    #[BodyParam(
        name       : 'voucher_qr_size_px',
        type       : 'int',
        description: 'The size of the QR code on the template',
        required   : false
    )]
    #[BodyParam(
        name       : 'voucher_qr_x',
        type       : 'int',
        description: 'The X coordinate of the QR code',
        required   : false
    )]
    #[BodyParam(
        name       : 'voucher_qr_y',
        type       : 'int',
        description: 'The Y coordinate of the QR code',
        required   : false
    )]

    #[BodyParam(
        name       : 'voucher_code_prefix',
        type       : 'string',
        description: 'String to prefix the voucher code by on the template, eg "#"',
        required   : false
    )]
    #[BodyParam(
        name       : 'voucher_code_size_px',
        type       : 'int',
        description: 'The size of the voucher short code on the template',
        required   : false
    )]
    #[BodyParam(
        name       : 'voucher_code_x',
        type       : 'int',
        description: 'The X coordinate of the voucher short code',
        required   : false
    )]
    #[BodyParam(
        name       : 'voucher_code_y',
        type       : 'int',
        description: 'The Y coordinate of the voucher short code',
        required   : false
    )]
    #[BodyParam(
        name       : 'voucher_expiry_prefix',
        type       : 'string',
        description: 'String to prefix the voucher expiry by on the template, eg "@"',
        required   : false
    )]
    #[BodyParam(
        name       : 'voucher_expiry_size_px',
        type       : 'int',
        description: 'The size of the voucher expiry text on the template',
        required   : false
    )]
    #[BodyParam(
        name       : 'voucher_expiry_x',
        type       : 'int',
        description: 'The X coordinate of the voucher expiry text',
        required   : false
    )]
    #[BodyParam(
        name       : 'voucher_expiry_y',
        type       : 'int',
        description: 'The Y coordinate of the voucher expiry text',
        required   : false
    )]
    #[BodyParam(
        name       : 'voucher_value_prefix',
        type       : 'string',
        description: 'String to prefix the voucher value by on the template, eg "$"',
        required   : false
    )]
    #[BodyParam(
        name       : 'voucher_value_size_px',
        type       : 'int',
        description: 'The size of the voucher value text on the template',
        required   : false
    )]
    #[BodyParam(
        name       : 'voucher_value_x',
        type       : 'int',
        description: 'The X coordinate of the voucher value text',
        required   : false
    )]
    #[BodyParam(
        name       : 'voucher_value_y',
        type       : 'int',
        description: 'The Y coordinate of the voucher value text',
        required   : false
    )]
    #[BodyParam(
        name       : 'overlay_font_path',
        type       : 'string',
        description: 'The path to the font being used in the template',
        required   : false
    )]
    #[BodyParam(
        name       : 'archive',
        type       : 'boolean',
        description: 'TArchive the template from future selection in a voucher set.',
        required   : false
    )]
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
    #[Endpoint(
        title        : 'Delete /{id}',
        description  : 'Remove a team voucher template.',
        authenticated: true
    )]
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
