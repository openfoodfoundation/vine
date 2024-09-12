<?php

namespace Tests\Feature\API\App\MyTeamSearch;

use App\Enums\PersonalAccessTokenAbility;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\API\BaseAPITestCase;

class MyTeamSearchPostTest extends BaseAPITestCase
{
    use WithFaker;

    private string $endPoint = '/my-team-search';

    #[Test]
    public function itFailsIfNotAuthenticated()
    {
        $this->user = $this->createUser();

        $response = $this->post($this->apiRoot . $this->endPoint, []);
        $response->assertStatus(302);
    }

    #[Test]
    public function itFailsToCreateEveryTime()
    {
        $this->user = $this->createUser();

        Sanctum::actingAs(
            $this->user,
            abilities: [
                PersonalAccessTokenAbility::MY_TEAM_SEARCH_CREATE->value,
            ]
        );

        $response = $this->post($this->apiRoot . $this->endPoint, []);
        $response->assertStatus(403);
    }
}
