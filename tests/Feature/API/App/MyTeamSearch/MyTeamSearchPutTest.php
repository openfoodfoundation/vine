<?php

namespace Tests\Feature\API\App\MyTeamSearch;

use App\Enums\PersonalAccessTokenAbility;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\API\BaseAPITestCase;

class MyTeamSearchPutTest extends BaseAPITestCase
{
    use WithFaker;

    private string $endPoint = '/my-team-search';

    #[Test]
    public function itFailsIfNotAuthenticated()
    {
        $this->user = $this->createUser();

        $response = $this->put($this->apiRoot . $this->endPoint . '/1', []);
        $response->assertStatus(302);
    }

    #[Test]
    public function itFailsToUpdateEveryTime()
    {
        $this->user = $this->createUser();

        Sanctum::actingAs(
            $this->user,
            abilities: [
                PersonalAccessTokenAbility::MY_TEAM_SEARCH_UPDATE->value,
            ]
        );

        $response = $this->put($this->apiRoot . $this->endPoint . '/1', []);
        $response->assertStatus(403);
    }
}
