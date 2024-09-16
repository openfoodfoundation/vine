<?php

namespace Tests\Unit\Services;

use App\Models\AuditItem;
use App\Models\Team;
use App\Models\User;
use App\Models\Voucher;
use App\Models\VoucherSet;
use App\Models\VoucherSetMerchantTeamApprovalRequest;
use App\Services\AuditItemService;
use App\Services\BounceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Event;
use PHPUnit\Framework\Attributes\Test;
use Str;
use Tests\TestCase;

class BounceServiceTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    #[Test]
    public function testGenerateSignedUrlForUser(): void
    {
        Event::fake();

        $user = User::factory()->create();

        $expiry = now()->addDays($this->faker->numberBetween(2, 5));

        $voucherSetMerchantTeamApprovalRequest = VoucherSetMerchantTeamApprovalRequest::factory()->create();

        $redirectPath = '/' . $this->faker->word() . '/' . $voucherSetMerchantTeamApprovalRequest->id;

        $result = BounceService::generateSignedUrlForUser($user, $expiry, $redirectPath);

        $this->assertStringContainsString('expires=', $result);
        $this->assertStringContainsString('redirectPath=', $result);
        $this->assertStringContainsString('signature=', $result);
        $this->assertIsString($result);
    }
}
