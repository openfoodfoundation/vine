<?php

namespace Tests\Feature\API;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class BaseAPITestCase extends TestCase
{
    use DatabaseTransactions;

    public string $apiRoot = '/api/v1';
    public User|Model $user;

    public function createUser(): Collection|Model|User
    {
        $this->user = User::factory()->create();

        return $this->user;
    }

    public function createAdminUser(): Collection|Model|User
    {
        $this->user = User::factory()->create([
            'is_admin' => 1,
        ]);

        return $this->user;
    }
}
