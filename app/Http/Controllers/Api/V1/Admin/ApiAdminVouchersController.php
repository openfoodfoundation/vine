<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Enums\ApiResponse;
use App\Exceptions\DisallowedApiFieldException;
use App\Http\Controllers\Api\HandlesAPIRequests;
use App\Http\Controllers\Controller;
use App\Models\Voucher;
use Exception;
use Illuminate\Http\JsonResponse;
use Knuckles\Scribe\Attributes\Authenticated;
use Knuckles\Scribe\Attributes\Endpoint;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\QueryParam;
use Knuckles\Scribe\Attributes\Response;
use Knuckles\Scribe\Attributes\Subgroup;
use Knuckles\Scribe\Attributes\UrlParam;

#[Group('App Endpoints')]
#[Subgroup('/admin/vouchers', 'Admin endpoint for unrestricted management of vouchers')]
class ApiAdminVouchersController extends Controller
{
    use HandlesAPIRequests;

    /**
     * Set the related data the GET request is allowed to ask for
     */
    public array $availableRelations = [];

    public static array $searchableFields = [];

    /**
     * @throws DisallowedApiFieldException
     */
    #[Endpoint(
        title: 'GET /',
        description: 'Retrieve voucher sets.',
        authenticated: true
    )]
    #[Authenticated]
    #[QueryParam(
        name: 'cached',
        type: 'bool',
        description: 'Request the response to be cached. Default: `true`.',
        required: false,
        example: true
    )]
    #[QueryParam(
        name: 'page',
        type: 'int',
        description: 'The pagination page number.',
        required: false,
        example: 1
    )]
    #[QueryParam(
        name: 'limit',
        type: 'int',
        description: 'The number of entries returned per pagination page.',
        required: false,
        example: 50
    )]
    #[QueryParam(
        name: 'fields',
        type: 'string',
        description: 'Comma-separated list of database fields to return within the object.',
        required: false,
        example: 'id,created_at'
    )]
    #[QueryParam(
        name: 'orderBy',
        type: 'comma-separated',
        description: 'Order the data by a given field. Comma-separated string.',
        required: false,
        example: 'orderBy=id,desc'
    )]
    #[QueryParam(
        name: 'orderBy[]',
        type: 'comma-separated',
        description: 'Compound `orderBy`. Order the data by a given field. Comma-separated string. Can not be used in conjunction as standard `orderBy`.',
        required: false,
        example: 'orderBy[]=id,desc&orderBy[]=created_at,asc'
    )]
    #[QueryParam(
        name: 'where',
        type: 'comma-separated',
        description: 'Filter the request on a single field. Key-Value or Key-Operator-Value comma-separated string.',
        required: false,
        example: 'where=id,like,*550e*'
    )]
    #[QueryParam(
        name: 'where[]',
        type: 'comma-separated',
        description: 'Compound `where`. Use when you need to filter on multiple `where`\'s. Note only AND is possible; ORWHERE is not available.',
        required: false,
        example: 'where[]=id,like,*550e*&where[]=created_at,>=,2024-01-01'
    )]
    #[Response(
        content: '',
        status: 200,
        description: ''
    )]
    public function index(): JsonResponse
    {

        $this->query = Voucher::with($this->associatedData);
        $this->query = $this->updateReadQueryBasedOnUrl();
        $this->data  = $this->query->paginate($this->limit);

        return $this->respond();
    }

    /**
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
     * @param string $id
     *
     * @return JsonResponse
     *
     * @throws DisallowedApiFieldException
     */
    #[Endpoint(
        title: 'GET /{id}',
        description: 'Retrieve a single voucher set.',
        authenticated: true,
    )]
    #[Authenticated]
    #[UrlParam(
        name: 'id',
        type: 'uuid',
        description: 'Voucher Set ID.',
        example: '550e8400-e29b-41d4-a716-446655440000'
    )]
    #[QueryParam(
        name: 'cached',
        type: 'bool',
        description: 'Request the response to be cached. Default: `true`.',
        required: false,
        example: 1
    )]
    #[QueryParam(
        name: 'fields',
        type: 'string',
        description: 'Comma-separated list of database fields to return within the object.',
        required: false,
        example: 'id,created_at'
    )]
    #[Response(
        content: '{
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
        status: 200,
        description: ''
    )]
    public function show(string $id)
    {

        $this->query = Voucher::with($this->associatedData);
        $this->query = $this->updateReadQueryBasedOnUrl();
        $this->data  = $this->query->find($id);

        return $this->respond();
    }

    /**
     * PUT/ {id}
     *
     * @param string $id
     *
     * @return JsonResponse
     *
     * @hideFromAPIDocumentation
     */
    public function update(string $id)
    {
        $this->responseCode = 403;
        $this->message      = ApiResponse::RESPONSE_METHOD_NOT_ALLOWED->value;

        return $this->respond();
    }

    /**
     * DELETE /{id}
     *
     * @param string $id
     *
     * @return JsonResponse
     */
    public function destroy(string $id)
    {
        try {

            $model = Voucher::find($id);

            if (!$model) {

                $this->responseCode = 404;
                $this->message      = ApiResponse::RESPONSE_NOT_FOUND->value;

                return $this->respond();
            }

            $model->delete();
            $this->message = ApiResponse::RESPONSE_DELETED->value;

        }
        catch (Exception $e) {

            $this->responseCode = 500;
            $this->message      = ApiResponse::RESPONSE_ERROR->value . ':' . $e->getMessage();

        }

        return $this->respond();
    }
}
