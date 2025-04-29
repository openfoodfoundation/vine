<?php

/** @noinspection PhpUndefinedFieldInspection */

/** @noinspection PhpUnusedParameterInspection */

namespace App\Http\Controllers\Api\V1\Admin;

use App\Enums\ApiResponse;
use App\Http\Controllers\Api\HandlesAPIRequests;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Knuckles\Scribe\Attributes\Authenticated;
use Knuckles\Scribe\Attributes\BodyParam;
use Knuckles\Scribe\Attributes\Endpoint;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\Subgroup;

#[Group('Admin Endpoints')]
#[Subgroup('/admin/file-uploads', 'Perform file uploads to the configured cloud storage system.')]
/**
 * @hideFromAPIDocumentation
 */
class ApiAdminFileUploadsController extends Controller
{
    /**
     * Use the HandlesAPIRequests trait to keep the controller lean
     */
    use HandlesAPIRequests;

    /**
     * Set the related data the GET request is allowed to ask for
     *
     * @var array
     */
    public array $availableRelations = [];

    /**
     * GET /
     *
     * @hideFromAPIDocumentation
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
        title        : 'POST /',
        description  : 'Create a new voucher set.',
        authenticated: true
    )]
    #[Authenticated]
    #[BodyParam(
        name       : 'acceptedFileTypes',
        type       : 'string',
        description: 'Comma-separated list of allowed file types.',
        required   : true
    )]
    #[BodyParam(
        name       : 'folder',
        type       : 'string',
        description: 'The folder location where the file(s) will be uploaded to',
        required   : true
    )]
    #[BodyParam(
        name       : 'visibility',
        type       : 'string',
        description: 'The file cloud system visibility (public or private)',
        required   : false
    )]
    #[BodyParam(
        name       : 'returnFilePathOnly',
        type       : 'true',
        description: 'Whether to return the cloud system file path, or the full cloud URL of the upload.',
        required   : false
    )]
    public function store(): JsonResponse
    {
        $validationArray = [
            'acceptedFileTypes' => [
                'required',
                'string',
            ],
            'folder' => [
                'required',
                'string',
            ],
            'files' => [
                'required',
                'array',
            ],
            'files.*' => [
                'max:6000',
            ],
            'visibility'         => [],
            'returnFilePathOnly' => [],
        ];

        /**
         * The messages array
         */
        $messages = [
            'max' => 'Uploaded files must be less than 5MB.',
        ];

        /**
         * Validate
         */
        $validator = Validator::make($this->request->all(), $validationArray, $messages);

        if ($validator->fails()) {
            $this->responseCode = 400;
            $this->message      = $validator->errors()
                ->first();
        }
        else {

            /**
             * Check accepted file types. If any incoming files
             * are not of the correct type, block the upload
             */
            $acceptedFileTypes = strtolower($this->request->get('acceptedFileTypes'));

            $acceptedFileTypesArray = explode(',', $acceptedFileTypes);

            foreach ($this->request->file('files') as $file) {
                $ext = strtolower($file->getClientMimeType());

                if (!in_array($ext, $acceptedFileTypesArray)) {
                    $this->responseCode = 400;
                    $this->message      = ApiResponse::RESPONSE_FILE_TYPE_NOT_ALLOWED->value . ': ' . $ext;
                    $this->data         = [];

                    return $this->respond();
                }
            }

            $files      = $this->request->file('files');
            $visibility = $this->request->get('visibility');

            /**
             * They're all good - upload
             */
            foreach ($files as $file) {

                /**
                 * Pop the file up
                 */
                $path = Storage::putFileAs(
                    $this->request->get('folder'),
                    $file,
                    date('YmdHis') . '_' . Str::random(4) . '.' . $file->getClientOriginalExtension()
                );

                if (!isset($visibility) || ($visibility != 'public')) {
                    // Don't make it public
                }
                else {
                    /**
                     * Set the file as public
                     */
                    Storage::setVisibility($path, 'public');
                }

                $returnFilePathOnly = $this->request->get('returnFilePathOnly');

                $this->data[] = (isset($returnFilePathOnly) && ($returnFilePathOnly != null)) ? $path : Storage::url($path);
                //                $this->data = $path;
            }

            $this->message = ApiResponse::RESPONSE_SAVED->value;
        }

        /**
         * Respond
         */
        return $this->respond();
    }

    /**
     * GET /{id}
     *
     * @hideFromAPIDocumentation
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
     * PUT /{id}
     *
     * @hideFromAPIDocumentation
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
     * DELETE /{id}
     *
     * @hideFromAPIDocumentation
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
