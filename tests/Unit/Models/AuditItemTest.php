<?php

namespace Tests\Unit\Models;

use App\Models\AuditItem;
use App\Models\Team;
use App\Models\User;
use App\Models\Voucher;
use App\Models\VoucherSet;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AuditItemTest extends TestCase
{
    #[Test]
    public function testUserRelation(): void
    {
        $user = User::factory()->create();

        $auditItem = AuditItem::factory()->create([
            'auditable_type' => get_class($user),
            'auditable_id'   => $user->id,
        ]);

        $auditItemWithUser = AuditItem::find($auditItem->id);

        $this->assertInstanceOf(AuditItem::class, $auditItemWithUser);
        $this->assertInstanceOf(User::class, $auditItemWithUser->auditable);
        $this->assertSame(User::class, $auditItemWithUser->auditable_type);
        $this->assertTrue($auditItemWithUser->auditable->is($user));
    }

    #[Test]
    public function testTeamRelation(): void
    {
        $team = Team::factory()->create();

        $auditItem = AuditItem::factory()->create([
            'auditable_type' => get_class($team),
            'auditable_id'   => $team->id,
        ]);

        $auditItemWithTeam = AuditItem::find($auditItem->id);

        $this->assertInstanceOf(AuditItem::class, $auditItemWithTeam);
        $this->assertInstanceOf(Team::class, $auditItemWithTeam->auditable);
        $this->assertSame(Team::class, $auditItemWithTeam->auditable_type);
        $this->assertTrue($auditItemWithTeam->auditable->is($team));
    }

    #[Test]
    public function testVoucherRelation(): void
    {
        $voucher = Voucher::factory()->create();

        $auditItem = AuditItem::factory()->create([
            'auditable_type' => get_class($voucher),
            'auditable_id'   => $voucher->id,
        ]);

        $auditItemWithVoucher = AuditItem::find($auditItem->id);

        $this->assertInstanceOf(AuditItem::class, $auditItemWithVoucher);
        $this->assertInstanceOf(Voucher::class, $auditItemWithVoucher->auditable);
        $this->assertSame(Voucher::class, $auditItemWithVoucher->auditable_type);
        $this->assertTrue($auditItemWithVoucher->auditable->is($voucher));
    }

    #[Test]
    public function testVoucherSetRelation(): void
    {
        $voucherSet = VoucherSet::factory()->create();

        $auditItem = AuditItem::factory()->create([
            'auditable_type' => get_class($voucherSet),
            'auditable_id'   => $voucherSet->id,
        ]);

        $auditItemWithVoucherSet = AuditItem::find($auditItem->id);

        $this->assertInstanceOf(AuditItem::class, $auditItemWithVoucherSet);
        $this->assertInstanceOf(VoucherSet::class, $auditItemWithVoucherSet->auditable);
        $this->assertSame(VoucherSet::class, $auditItemWithVoucherSet->auditable_type);
        $this->assertTrue($auditItemWithVoucherSet->auditable->is($voucherSet));
    }
}
