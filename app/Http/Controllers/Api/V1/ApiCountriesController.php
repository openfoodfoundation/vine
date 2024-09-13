<?php


namespace App\Http\Controllers\Api\V1;

use App\Enums\ApiResponse;
use App\Exceptions\DisallowedApiFieldException;
use App\Http\Controllers\Api\HandlesAPIRequests;
use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Team;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Knuckles\Scribe\Attributes\Authenticated;
use Knuckles\Scribe\Attributes\Endpoint;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\QueryParam;
use Knuckles\Scribe\Attributes\Response;
use Knuckles\Scribe\Attributes\Subgroup;

#[Group('App Endpoints')]
#[Subgroup('/countries', 'Retrieve the available countries list.')]
class ApiCountriesController extends Controller
{
    use HandlesAPIRequests;

    /**
     * Set the related data the GET request is allowed to ask for
     */
    public array $availableRelations = [
    ];

    public static array $searchableFields = [];

    /**
     * GET /
     *
     * @return JsonResponse
     *
     * @throws DisallowedApiFieldException
     */
    #[Endpoint(
        title        : 'GET /',
        description  : 'Retrieve the countries list.',
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
        name       : 'fields',
        type       : 'string',
        description: 'Comma-separated list of database fields to return within the object.',
        required   : false,
        example    : 'id,created_at'
    )]
    #[Response(
        content    : '{"meta":{"responseCode":200,"limit":50,"offset":0,"message":"","cached":true,"cached_at":"2024-09-13 05:05:29","availableRelations":[]},"data":{"current_page":1,"data":[{"id":1,"name":"Afghanistan","alpha2":"AF","alpha3":"AFG","numeric":"004","currency_code":"AFN","created_at":"2024-09-13T05:05:28.000000Z","updated_at":"2024-09-13T05:05:28.000000Z","deleted_at":null},{"id":2,"name":"\u00c5land Islands","alpha2":"AX","alpha3":"ALA","numeric":"248","currency_code":"EUR","created_at":"2024-09-13T05:05:28.000000Z","updated_at":"2024-09-13T05:05:28.000000Z","deleted_at":null},{"id":3,"name":"Albania","alpha2":"AL","alpha3":"ALB","numeric":"008","currency_code":"ALL","created_at":"2024-09-13T05:05:28.000000Z","updated_at":"2024-09-13T05:05:28.000000Z","deleted_at":null},{"id":4,"name":"Algeria","alpha2":"DZ","alpha3":"DZA","numeric":"012","currency_code":"DZD","created_at":"2024-09-13T05:05:28.000000Z","updated_at":"2024-09-13T05:05:28.000000Z","deleted_at":null},{"id":5,"name":"American Samoa","alpha2":"AS","alpha3":"ASM","numeric":"016","currency_code":"USD","created_at":"2024-09-13T05:05:28.000000Z","updated_at":"2024-09-13T05:05:28.000000Z","deleted_at":null},{"id":6,"name":"Andorra","alpha2":"AD","alpha3":"AND","numeric":"020","currency_code":"EUR","created_at":"2024-09-13T05:05:28.000000Z","updated_at":"2024-09-13T05:05:28.000000Z","deleted_at":null},{"id":7,"name":"Angola","alpha2":"AO","alpha3":"AGO","numeric":"024","currency_code":"AOA","created_at":"2024-09-13T05:05:28.000000Z","updated_at":"2024-09-13T05:05:28.000000Z","deleted_at":null},{"id":8,"name":"Anguilla","alpha2":"AI","alpha3":"AIA","numeric":"660","currency_code":"XCD","created_at":"2024-09-13T05:05:29.000000Z","updated_at":"2024-09-13T05:05:29.000000Z","deleted_at":null},{"id":9,"name":"Antarctica","alpha2":"AQ","alpha3":"ATA","numeric":"010","currency_code":"ARS","created_at":"2024-09-13T05:05:29.000000Z","updated_at":"2024-09-13T05:05:29.000000Z","deleted_at":null},{"id":10,"name":"Antigua And Barbuda","alpha2":"AG","alpha3":"ATG","numeric":"028","currency_code":"XCD","created_at":"2024-09-13T05:05:29.000000Z","updated_at":"2024-09-13T05:05:29.000000Z","deleted_at":null},{"id":11,"name":"Argentina","alpha2":"AR","alpha3":"ARG","numeric":"032","currency_code":"ARS","created_at":"2024-09-13T05:05:29.000000Z","updated_at":"2024-09-13T05:05:29.000000Z","deleted_at":null},{"id":12,"name":"Armenia","alpha2":"AM","alpha3":"ARM","numeric":"051","currency_code":"AMD","created_at":"2024-09-13T05:05:29.000000Z","updated_at":"2024-09-13T05:05:29.000000Z","deleted_at":null},{"id":13,"name":"Aruba","alpha2":"AW","alpha3":"ABW","numeric":"533","currency_code":"AWG","created_at":"2024-09-13T05:05:29.000000Z","updated_at":"2024-09-13T05:05:29.000000Z","deleted_at":null},{"id":14,"name":"Australia","alpha2":"AU","alpha3":"AUS","numeric":"036","currency_code":"AUD","created_at":"2024-09-13T05:05:29.000000Z","updated_at":"2024-09-13T05:05:29.000000Z","deleted_at":null},{"id":15,"name":"Austria","alpha2":"AT","alpha3":"AUT","numeric":"040","currency_code":"EUR","created_at":"2024-09-13T05:05:29.000000Z","updated_at":"2024-09-13T05:05:29.000000Z","deleted_at":null},{"id":16,"name":"Azerbaijan","alpha2":"AZ","alpha3":"AZE","numeric":"031","currency_code":"AZN","created_at":"2024-09-13T05:05:29.000000Z","updated_at":"2024-09-13T05:05:29.000000Z","deleted_at":null},{"id":17,"name":"Bahamas","alpha2":"BS","alpha3":"BHS","numeric":"044","currency_code":"BSD","created_at":"2024-09-13T05:05:29.000000Z","updated_at":"2024-09-13T05:05:29.000000Z","deleted_at":null},{"id":18,"name":"Bahrain","alpha2":"BH","alpha3":"BHR","numeric":"048","currency_code":"BHD","created_at":"2024-09-13T05:05:29.000000Z","updated_at":"2024-09-13T05:05:29.000000Z","deleted_at":null},{"id":19,"name":"Bangladesh","alpha2":"BD","alpha3":"BGD","numeric":"050","currency_code":"BDT","created_at":"2024-09-13T05:05:29.000000Z","updated_at":"2024-09-13T05:05:29.000000Z","deleted_at":null},{"id":20,"name":"Barbados","alpha2":"BB","alpha3":"BRB","numeric":"052","currency_code":"BBD","created_at":"2024-09-13T05:05:29.000000Z","updated_at":"2024-09-13T05:05:29.000000Z","deleted_at":null},{"id":21,"name":"Belarus","alpha2":"BY","alpha3":"BLR","numeric":"112","currency_code":"BYN","created_at":"2024-09-13T05:05:29.000000Z","updated_at":"2024-09-13T05:05:29.000000Z","deleted_at":null},{"id":22,"name":"Belgium","alpha2":"BE","alpha3":"BEL","numeric":"056","currency_code":"EUR","created_at":"2024-09-13T05:05:29.000000Z","updated_at":"2024-09-13T05:05:29.000000Z","deleted_at":null},{"id":23,"name":"Belize","alpha2":"BZ","alpha3":"BLZ","numeric":"084","currency_code":"BZD","created_at":"2024-09-13T05:05:29.000000Z","updated_at":"2024-09-13T05:05:29.000000Z","deleted_at":null},{"id":24,"name":"Benin","alpha2":"BJ","alpha3":"BEN","numeric":"204","currency_code":"XOF","created_at":"2024-09-13T05:05:29.000000Z","updated_at":"2024-09-13T05:05:29.000000Z","deleted_at":null},{"id":25,"name":"Bermuda","alpha2":"BM","alpha3":"BMU","numeric":"060","currency_code":"BMD","created_at":"2024-09-13T05:05:29.000000Z","updated_at":"2024-09-13T05:05:29.000000Z","deleted_at":null},{"id":26,"name":"Bhutan","alpha2":"BT","alpha3":"BTN","numeric":"064","currency_code":"BTN","created_at":"2024-09-13T05:05:29.000000Z","updated_at":"2024-09-13T05:05:29.000000Z","deleted_at":null},{"id":27,"name":"Bolivia (plurinational State Of)","alpha2":"BO","alpha3":"BOL","numeric":"068","currency_code":"BOB","created_at":"2024-09-13T05:05:29.000000Z","updated_at":"2024-09-13T05:05:29.000000Z","deleted_at":null},{"id":28,"name":"Bonaire, Sint Eustatius And Saba","alpha2":"BQ","alpha3":"BES","numeric":"535","currency_code":"USD","created_at":"2024-09-13T05:05:29.000000Z","updated_at":"2024-09-13T05:05:29.000000Z","deleted_at":null},{"id":29,"name":"Bosnia And Herzegovina","alpha2":"BA","alpha3":"BIH","numeric":"070","currency_code":"BAM","created_at":"2024-09-13T05:05:29.000000Z","updated_at":"2024-09-13T05:05:29.000000Z","deleted_at":null},{"id":30,"name":"Botswana","alpha2":"BW","alpha3":"BWA","numeric":"072","currency_code":"BWP","created_at":"2024-09-13T05:05:29.000000Z","updated_at":"2024-09-13T05:05:29.000000Z","deleted_at":null},{"id":31,"name":"Bouvet Island","alpha2":"BV","alpha3":"BVT","numeric":"074","currency_code":"NOK","created_at":"2024-09-13T05:05:29.000000Z","updated_at":"2024-09-13T05:05:29.000000Z","deleted_at":null},{"id":32,"name":"Brazil","alpha2":"BR","alpha3":"BRA","numeric":"076","currency_code":"BRL","created_at":"2024-09-13T05:05:29.000000Z","updated_at":"2024-09-13T05:05:29.000000Z","deleted_at":null},{"id":33,"name":"British Indian Ocean Territory","alpha2":"IO","alpha3":"IOT","numeric":"086","currency_code":"GBP","created_at":"2024-09-13T05:05:29.000000Z","updated_at":"2024-09-13T05:05:29.000000Z","deleted_at":null},{"id":34,"name":"Brunei Darussalam","alpha2":"BN","alpha3":"BRN","numeric":"096","currency_code":"BND","created_at":"2024-09-13T05:05:29.000000Z","updated_at":"2024-09-13T05:05:29.000000Z","deleted_at":null},{"id":35,"name":"Bulgaria","alpha2":"BG","alpha3":"BGR","numeric":"100","currency_code":"BGN","created_at":"2024-09-13T05:05:29.000000Z","updated_at":"2024-09-13T05:05:29.000000Z","deleted_at":null},{"id":36,"name":"Burkina Faso","alpha2":"BF","alpha3":"BFA","numeric":"854","currency_code":"XOF","created_at":"2024-09-13T05:05:29.000000Z","updated_at":"2024-09-13T05:05:29.000000Z","deleted_at":null},{"id":37,"name":"Burundi","alpha2":"BI","alpha3":"BDI","numeric":"108","currency_code":"BIF","created_at":"2024-09-13T05:05:29.000000Z","updated_at":"2024-09-13T05:05:29.000000Z","deleted_at":null},{"id":38,"name":"Cabo Verde","alpha2":"CV","alpha3":"CPV","numeric":"132","currency_code":"CVE","created_at":"2024-09-13T05:05:29.000000Z","updated_at":"2024-09-13T05:05:29.000000Z","deleted_at":null},{"id":39,"name":"Cambodia","alpha2":"KH","alpha3":"KHM","numeric":"116","currency_code":"KHR","created_at":"2024-09-13T05:05:29.000000Z","updated_at":"2024-09-13T05:05:29.000000Z","deleted_at":null},{"id":40,"name":"Cameroon","alpha2":"CM","alpha3":"CMR","numeric":"120","currency_code":"XAF","created_at":"2024-09-13T05:05:29.000000Z","updated_at":"2024-09-13T05:05:29.000000Z","deleted_at":null},{"id":41,"name":"Canada","alpha2":"CA","alpha3":"CAN","numeric":"124","currency_code":"CAD","created_at":"2024-09-13T05:05:29.000000Z","updated_at":"2024-09-13T05:05:29.000000Z","deleted_at":null},{"id":42,"name":"Cayman Islands","alpha2":"KY","alpha3":"CYM","numeric":"136","currency_code":"KYD","created_at":"2024-09-13T05:05:29.000000Z","updated_at":"2024-09-13T05:05:29.000000Z","deleted_at":null},{"id":43,"name":"Central African Republic","alpha2":"CF","alpha3":"CAF","numeric":"140","currency_code":"XAF","created_at":"2024-09-13T05:05:29.000000Z","updated_at":"2024-09-13T05:05:29.000000Z","deleted_at":null},{"id":44,"name":"Chad","alpha2":"TD","alpha3":"TCD","numeric":"148","currency_code":"XAF","created_at":"2024-09-13T05:05:29.000000Z","updated_at":"2024-09-13T05:05:29.000000Z","deleted_at":null},{"id":45,"name":"Chile","alpha2":"CL","alpha3":"CHL","numeric":"152","currency_code":"CLP","created_at":"2024-09-13T05:05:29.000000Z","updated_at":"2024-09-13T05:05:29.000000Z","deleted_at":null},{"id":46,"name":"China","alpha2":"CN","alpha3":"CHN","numeric":"156","currency_code":"CNY","created_at":"2024-09-13T05:05:29.000000Z","updated_at":"2024-09-13T05:05:29.000000Z","deleted_at":null},{"id":47,"name":"Christmas Island","alpha2":"CX","alpha3":"CXR","numeric":"162","currency_code":"AUD","created_at":"2024-09-13T05:05:29.000000Z","updated_at":"2024-09-13T05:05:29.000000Z","deleted_at":null},{"id":48,"name":"Cocos (keeling) Islands","alpha2":"CC","alpha3":"CCK","numeric":"166","currency_code":"AUD","created_at":"2024-09-13T05:05:29.000000Z","updated_at":"2024-09-13T05:05:29.000000Z","deleted_at":null},{"id":49,"name":"Colombia","alpha2":"CO","alpha3":"COL","numeric":"170","currency_code":"COP","created_at":"2024-09-13T05:05:29.000000Z","updated_at":"2024-09-13T05:05:29.000000Z","deleted_at":null},{"id":50,"name":"Comoros","alpha2":"KM","alpha3":"COM","numeric":"174","currency_code":"KMF","created_at":"2024-09-13T05:05:29.000000Z","updated_at":"2024-09-13T05:05:29.000000Z","deleted_at":null}],"first_page_url":"https:\/\/vine.test\/api\/v1\/countries?page=1","from":1,"last_page":5,"last_page_url":"https:\/\/vine.test\/api\/v1\/countries?page=5","links":[{"url":null,"label":"&laquo; Previous","active":false},{"url":"https:\/\/vine.test\/api\/v1\/countries?page=1","label":"1","active":true},{"url":"https:\/\/vine.test\/api\/v1\/countries?page=2","label":"2","active":false},{"url":"https:\/\/vine.test\/api\/v1\/countries?page=3","label":"3","active":false},{"url":"https:\/\/vine.test\/api\/v1\/countries?page=4","label":"4","active":false},{"url":"https:\/\/vine.test\/api\/v1\/countries?page=5","label":"5","active":false},{"url":"https:\/\/vine.test\/api\/v1\/countries?page=2","label":"Next &raquo;","active":false}],"next_page_url":"https:\/\/vine.test\/api\/v1\/countries?page=2","path":"https:\/\/vine.test\/api\/v1\/countries","per_page":50,"prev_page_url":null,"to":50,"total":249}}',
        status     : 200,
        description: ''
    )]
    public function index(): JsonResponse
    {
        $this->query = Country::with($this->associatedData);
        $this->query = $this->updateReadQueryBasedOnUrl();
        $this->data  = $this->query->paginate($this->limit);

        return $this->respond();
    }

    /**
     * POST /
     *
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
     * GET / {id}
     *
     * @hideFromAPIDocumentation
     *
     * @param string $id
     *
     * @return JsonResponse
     */
    public function show(string $id)
    {
        $this->responseCode = 403;
        $this->message      = ApiResponse::RESPONSE_METHOD_NOT_ALLOWED->value;

        return $this->respond();
    }

    /**
     * PUT / {id}
     *
     * @hideFromAPIDocumentation
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
     * @hideFromAPIDocumentation
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
