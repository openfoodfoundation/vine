<?php

namespace Tests\Unit\Models;

use App\Models\Team;
use App\Models\VoucherSet;
use App\Models\VoucherSetMerchantTeam;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class VoucherSetTest extends TestCase
{
    #[Test]
    public function testCreatedByTeamRelation(): void
    {
        $team = Team::factory()->create();

        $voucherSet = VoucherSet::factory()->create([
            'created_by_team_id' => $team->id,
        ]);

        $voucherSetWithCreatedByTeam = VoucherSet::with('createdByTeam')->find($voucherSet->id);

        $this->assertInstanceOf(VoucherSet::class, $voucherSetWithCreatedByTeam);
        $this->assertInstanceOf(Team::class, $voucherSetWithCreatedByTeam->createdByTeam);
        $this->assertSame($voucherSet->id, $voucherSetWithCreatedByTeam->id);
        $this->assertSame($team->id, $voucherSetWithCreatedByTeam->createdByTeam->id);
    }

    #[Test]
    public function testAllocatedToServiceTeamRelation(): void
    {
        $team = Team::factory()->create();

        $voucherSet = VoucherSet::factory()->create([
            'allocated_to_service_team_id' => $team->id,
        ]);

        $voucherSetWithServiceTeam = VoucherSet::with('allocatedToServiceTeam')->find($voucherSet->id);

        $this->assertInstanceOf(VoucherSet::class, $voucherSetWithServiceTeam);
        $this->assertInstanceOf(Team::class, $voucherSetWithServiceTeam->allocatedToServiceTeam);
        $this->assertSame($voucherSet->id, $voucherSetWithServiceTeam->id);
        $this->assertSame($team->id, $voucherSetWithServiceTeam->allocatedToServiceTeam->id);
    }

    #[Test]
    public function testVoucherSetMerchantTeamRelation(): void
    {
        $team = Team::factory()->create();

        $voucherSet = VoucherSet::factory()->create();

        $voucherSetMerchantTeam = VoucherSetMerchantTeam::factory()->create([
            'voucher_set_id'   => $voucherSet->id,
            'merchant_team_id' => $team->id,
        ]);

        $voucherSetWithMerchantTeam = VoucherSet::with('voucherSetMerchantTeam.merchantTeam')->find($voucherSet->id);

        $this->assertInstanceOf(VoucherSet::class, $voucherSetWithMerchantTeam);
        $this->assertInstanceOf(VoucherSetMerchantTeam::class, $voucherSetWithMerchantTeam->voucherSetMerchantTeam);
        $this->assertInstanceOf(Team::class, $voucherSetWithMerchantTeam->voucherSetMerchantTeam->merchantTeam);
        $this->assertSame($voucherSet->id, $voucherSetWithMerchantTeam->id);
        $this->assertSame($voucherSetMerchantTeam->id, $voucherSetWithMerchantTeam->voucherSetMerchantTeam->id);
        $this->assertSame($team->id, $voucherSetWithMerchantTeam->voucherSetMerchantTeam->merchantTeam->id);
    }
}
