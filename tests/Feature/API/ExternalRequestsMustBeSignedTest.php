<?php

namespace Tests\Feature\API;


use App\Enums\ApiResponse;
use App\Enums\PersonalAccessTokenAbility;
use App\Models\PersonalAccessToken;
use App\Services\PersonalAccessTokenService;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;

class ExternalRequestsMustBeSignedTest extends BaseAPITestCase
{
    #[Test]
    public function externalApiRequestsFailIfNotSigned(): void
    {
        /**
         * Remove the Referer header to indicate a request from an external source
         */
        $this->withoutHeader('Referer');

        $this->user = $this->createUser();

        Sanctum::actingAs($this->user, abilities: [
            PersonalAccessTokenAbility::SYSTEM_STATISTICS_READ->value,
        ]);

        $response = $this->getJson($this->apiRoot . '/system-statistics');

        $responseObject = $response->json();

        $this->assertEquals(
            ApiResponse::RESPONSE_AUTHORIZATION_SIGNATURE_INCORRECT->value . ' ' . ApiResponse::RESPONSE_AUTHORIZATION_SIGNATURE_INCORRECT_JWT_HEADER_REQUIRED->value,
            $responseObject['meta']['message']
        );
        $response->assertStatus(401);
    }

    #[Test]
    public function externalApiRequestsPassIfSigned(): void
    {
        /**
         * Remove the Referer header to indicate a request from an external source
         */
        $this->withoutHeader('Referer');

        /**
         * Create a user
         */
        $this->user = $this->createUser();

        /**
         * Give them an access token
         */
        $newAccessToken = $this->user->createToken('PJGToken', abilities: [
            PersonalAccessTokenAbility::SYSTEM_STATISTICS_READ->value,
        ]);

        /**
         * Generate the JWT
         */
        $jwt = PersonalAccessTokenService::generateJwtForPersonalAccessToken($newAccessToken->accessToken);


        /**
         * Pass it in as X-Authorization
         */
        $this->withHeader('X-Authorization', 'JWT ' . $jwt);

        /**
         * Pass in the API token as usual
         */
        $this->withHeader('Authorization', 'Bearer ' . $newAccessToken->plainTextToken);

        /**
         * Make the call
         */
        $response = $this->getJson($this->apiRoot . '/system-statistics');

        $response->assertStatus(200);
    }

}
