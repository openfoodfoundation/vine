<?php

namespace Tests\Unit\Middleware;

use App\Enums\ApiResponse;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CheckAdminStatusMiddlewareTest extends TestCase
{
    #[Test]
    public function adminCanSeeAdminPages(): void
    {
        $adminUser = User::factory()->create([
            'is_admin' => 1,
        ]);

        Sanctum::actingAs($adminUser);

        $this->get(route('admin.home'))->assertStatus(200);

    }

    #[Test]
    public function itRedirectsIfNotAdmin(): void
    {
        $nonAdminUser = User::factory()->create([
            'is_admin' => 0,
        ]);

        Sanctum::actingAs($nonAdminUser);

        $this->get(route('admin.home'))->assertRedirect();
    }

    #[Test]
    public function itReturnsJsonMessageIfNotAdmin(): void
    {
        $nonAdminUser = User::factory()->create([
            'is_admin' => 0,
        ]);

        Sanctum::actingAs($nonAdminUser);

        $response = $this->getJson(route('admin.home'))
            ->assertStatus(401)
            ->assertJson(
                [
                    'meta' => [
                        'message' => ApiResponse::RESPONSE_TOKEN_NOT_ALLOWED_TO_DO_THIS->value,
                    ],
                ]
            );
    }
}
