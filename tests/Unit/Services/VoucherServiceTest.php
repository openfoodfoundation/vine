<?php

namespace Tests\Unit\Services;

use App\Models\Voucher;
use App\Models\VoucherRedemption;
use App\Services\VoucherService;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class VoucherServiceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @see \App\Services\VoucherService::findUniqueShortCodeForVoucher()
     */
    #[Test]
    public function findUniqueShortCodeForVoucherReturnsCorrectlyFormattedShortCodes(): void
    {
        for ($iteration = 1; $iteration <= 100; $iteration++) {

            $shortCode = VoucherService::findUniqueShortCodeForVoucher();

            // Always 6 letters long
            self::assertSame(6, strlen($shortCode));

            // Only contains alphanumeric characters
            self::assertTrue(ctype_alnum($shortCode));

            // Format is two uppercase letters followed by four digits
            self::assertMatchesRegularExpression('/^[A-Z]{2}[0-9]{4}$/', $shortCode);
        }
    }

    /**
     * @see \App\Services\VoucherService::findUniqueShortCodeForVoucher()
     */
    #[Test]
    public function findUniqueShortCodeForVoucherReturnsUniqueCode(): void
    {
        $shortCode = VoucherService::findUniqueShortCodeForVoucher();

        // Verify it doesn't exist in database yet
        self::assertNull(Voucher::where('voucher_short_code', $shortCode)->first());
    }

    /**
     * @see \App\Services\VoucherService::findUniqueShortCodeForVoucher()
     */
    #[Test]
    public function findUniqueShortCodeForVoucherAvoidsExistingCodes(): void
    {
        // Create a voucher with a specific short code
        $existingVoucher = Voucher::factory()->create([
            'voucher_short_code' => 'AB1234',
        ]);

        // Generate a new short code
        $newShortCode = VoucherService::findUniqueShortCodeForVoucher();

        // The new code should not match the existing one
        self::assertNotEquals('AB1234', $newShortCode);
    }

    /**
     * @see \App\Services\VoucherService::findUniqueShortCodeForVoucher()
     */
    #[Test]
    public function findUniqueShortCodeForVoucherGeneratesMultipleUniqueCodes(): void
    {
        $shortCodes = [];

        // Generate 50 short codes
        for ($i = 0; $i < 50; $i++) {
            $shortCode    = VoucherService::findUniqueShortCodeForVoucher();
            $shortCodes[] = $shortCode;

            // Create a voucher with this code so the next iteration must generate a different one
            Voucher::factory()->create([
                'voucher_short_code' => $shortCode,
            ]);
        }

        // All codes should be unique
        self::assertEquals(50, count(array_unique($shortCodes)));
    }

    /**
     * @see \App\Services\VoucherService::calculateVoucherAmountRedeemed()
     */
    #[Test]
    public function calculateVoucherAmountRedeemedReturnsZeroWhenNoRedemptions(): void
    {
        $voucher = Voucher::factory()->create();

        $result = VoucherService::calculateVoucherAmountRedeemed($voucher);

        self::assertEquals(0, $result);
    }

    /**
     * @see \App\Services\VoucherService::calculateVoucherAmountRedeemed()
     */
    #[Test]
    public function calculateVoucherAmountRedeemedReturnsSumOfSingleRedemption(): void
    {
        $voucher = Voucher::factory()->create();

        VoucherRedemption::factory()->create([
            'voucher_id'      => $voucher->id,
            'redeemed_amount' => 100,
        ]);

        $result = VoucherService::calculateVoucherAmountRedeemed($voucher);

        self::assertEquals(100, $result);
    }

    /**
     * @see \App\Services\VoucherService::calculateVoucherAmountRedeemed()
     */
    #[Test]
    public function calculateVoucherAmountRedeemedReturnsSumOfMultipleRedemptions(): void
    {
        $voucher = Voucher::factory()->create();

        VoucherRedemption::factory()->create([
            'voucher_id'      => $voucher->id,
            'redeemed_amount' => 100,
        ]);

        VoucherRedemption::factory()->create([
            'voucher_id'      => $voucher->id,
            'redeemed_amount' => 250,
        ]);

        VoucherRedemption::factory()->create([
            'voucher_id'      => $voucher->id,
            'redeemed_amount' => 75,
        ]);

        $result = VoucherService::calculateVoucherAmountRedeemed($voucher);

        self::assertEquals(425, $result);
    }

    /**
     * @see \App\Services\VoucherService::calculateVoucherAmountRedeemed()
     */
    #[Test]
    public function calculateVoucherAmountRedeemedOnlyIncludesRedemptionsForSpecificVoucher(): void
    {
        $voucher1 = Voucher::factory()->create();
        $voucher2 = Voucher::factory()->create();

        VoucherRedemption::factory()->create([
            'voucher_id'      => $voucher1->id,
            'redeemed_amount' => 100,
        ]);

        VoucherRedemption::factory()->create([
            'voucher_id'      => $voucher1->id,
            'redeemed_amount' => 200,
        ]);

        VoucherRedemption::factory()->create([
            'voucher_id'      => $voucher2->id,
            'redeemed_amount' => 500,
        ]);

        $result = VoucherService::calculateVoucherAmountRedeemed($voucher1);

        self::assertEquals(300, $result);
    }

    /**
     * @see \App\Services\VoucherService::calculateVoucherAmountRemaining()
     */
    #[Test]
    public function calculateVoucherAmountRemainingReturnsFullValueWhenNoRedemptions(): void
    {
        $voucher = Voucher::factory()->create([
            'voucher_value_original' => 1000,
        ]);

        $result = VoucherService::calculateVoucherAmountRemaining($voucher);

        self::assertEquals(1000, $result);
    }

    /**
     * @see \App\Services\VoucherService::calculateVoucherAmountRemaining()
     */
    #[Test]
    public function calculateVoucherAmountRemainingReturnsCorrectAmountWithSingleRedemption(): void
    {
        $voucher = Voucher::factory()->create([
            'voucher_value_original' => 1000,
        ]);

        VoucherRedemption::factory()->create([
            'voucher_id'      => $voucher->id,
            'redeemed_amount' => 300,
        ]);

        $result = VoucherService::calculateVoucherAmountRemaining($voucher);

        self::assertEquals(700, $result);
    }

    /**
     * @see \App\Services\VoucherService::calculateVoucherAmountRemaining()
     */
    #[Test]
    public function calculateVoucherAmountRemainingReturnsCorrectAmountWithMultipleRedemptions(): void
    {
        $voucher = Voucher::factory()->create([
            'voucher_value_original' => 1000,
        ]);

        VoucherRedemption::factory()->create([
            'voucher_id'      => $voucher->id,
            'redeemed_amount' => 250,
        ]);

        VoucherRedemption::factory()->create([
            'voucher_id'      => $voucher->id,
            'redeemed_amount' => 100,
        ]);

        VoucherRedemption::factory()->create([
            'voucher_id'      => $voucher->id,
            'redeemed_amount' => 150,
        ]);

        $result = VoucherService::calculateVoucherAmountRemaining($voucher);

        self::assertEquals(500, $result);
    }

    /**
     * @see \App\Services\VoucherService::calculateVoucherAmountRemaining()
     */
    #[Test]
    public function calculateVoucherAmountRemainingReturnsZeroWhenFullyRedeemed(): void
    {
        $voucher = Voucher::factory()->create([
            'voucher_value_original' => 1000,
        ]);

        VoucherRedemption::factory()->create([
            'voucher_id'      => $voucher->id,
            'redeemed_amount' => 600,
        ]);

        VoucherRedemption::factory()->create([
            'voucher_id'      => $voucher->id,
            'redeemed_amount' => 400,
        ]);

        $result = VoucherService::calculateVoucherAmountRemaining($voucher);

        self::assertEquals(0, $result);
    }

    /**
     * @see \App\Services\VoucherService::calculateVoucherAmountRemaining()
     */
    #[Test]
    public function calculateVoucherAmountRemainingReturnsNegativeWhenOverRedeemed(): void
    {
        $voucher = Voucher::factory()->create([
            'voucher_value_original' => 1000,
        ]);

        // Disable events to prevent CollateVoucherAggregatesJob from throwing exception on over-redemption
        VoucherRedemption::withoutEvents(function () use ($voucher) {
            VoucherRedemption::factory()->create([
                'voucher_id'      => $voucher->id,
                'redeemed_amount' => 700,
            ]);

            VoucherRedemption::factory()->create([
                'voucher_id'      => $voucher->id,
                'redeemed_amount' => 500,
            ]);
        });

        $result = VoucherService::calculateVoucherAmountRemaining($voucher);

        self::assertEquals(-200, $result);
    }

    /**
     * @see \App\Services\VoucherService::updateVoucherAmountRemaining()
     */
    #[Test]
    public function updateVoucherAmountRemainingUpdatesVoucherSuccessfully(): void
    {
        $voucher = Voucher::factory()->create([
            'voucher_value_original'  => 1000,
            'voucher_value_remaining' => 1000,
        ]);

        VoucherRedemption::withoutEvents(function () use ($voucher) {
            VoucherRedemption::factory()->create([
                'voucher_id'      => $voucher->id,
                'redeemed_amount' => 300,
            ]);
        });

        VoucherService::updateVoucherAmountRemaining($voucher);

        $voucher->refresh();
        self::assertEquals(700, $voucher->voucher_value_remaining);
    }

    /**
     * @see \App\Services\VoucherService::updateVoucherAmountRemaining()
     */
    #[Test]
    public function updateVoucherAmountRemainingThrowsExceptionWhenOverRedeemed(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessageMatches('/has had too many redemptions/');

        $voucher = Voucher::factory()->create([
            'voucher_value_original'  => 1000,
            'voucher_value_remaining' => 1000,
        ]);

        VoucherRedemption::withoutEvents(function () use ($voucher) {
            VoucherRedemption::factory()->create([
                'voucher_id'      => $voucher->id,
                'redeemed_amount' => 1200,
            ]);
        });

        VoucherService::updateVoucherAmountRemaining($voucher);
    }

    /**
     * @see \App\Services\VoucherService::updateVoucherAmountRemaining()
     */
    #[Test]
    public function updateVoucherAmountRemainingDoesNotUpdateWhenNegative(): void
    {
        $voucher = Voucher::factory()->create([
            'voucher_value_original'  => 1000,
            'voucher_value_remaining' => 1000,
        ]);

        VoucherRedemption::withoutEvents(function () use ($voucher) {
            VoucherRedemption::factory()->create([
                'voucher_id'      => $voucher->id,
                'redeemed_amount' => 1500,
            ]);
        });

        try {
            VoucherService::updateVoucherAmountRemaining($voucher);
        }
        catch (Exception $e) {
            // Exception expected
        }

        $voucher->refresh();
        self::assertEquals(1000, $voucher->voucher_value_remaining);
    }

    /**
     * @see \App\Services\VoucherService::collateVoucherAggregates()
     */
    #[Test]
    public function collateVoucherAggregatesUpdatesAllFieldsCorrectly(): void
    {
        $voucher = Voucher::factory()->create([
            'voucher_value_original'  => 1000,
            'voucher_value_remaining' => 1000,
            'voucher_short_code'      => 'AB1234',
            'num_voucher_redemptions' => 0,
            'last_redemption_at'      => null,
        ]);

        VoucherRedemption::withoutEvents(function () use ($voucher) {
            VoucherRedemption::factory()->create([
                'voucher_id'      => $voucher->id,
                'redeemed_amount' => 300,
            ]);

            VoucherRedemption::factory()->create([
                'voucher_id'      => $voucher->id,
                'redeemed_amount' => 200,
            ]);
        });

        VoucherService::collateVoucherAggregates($voucher);

        $voucher->refresh();
        self::assertEquals(500, $voucher->voucher_value_remaining);
        self::assertEquals(2, $voucher->num_voucher_redemptions);
        self::assertNotNull($voucher->last_redemption_at);
        self::assertEquals('AB1234', $voucher->voucher_short_code);
    }

    /**
     * @see \App\Services\VoucherService::collateVoucherAggregates()
     */
    #[Test]
    public function collateVoucherAggregatesRemovesShortCodeWhenFullyRedeemed(): void
    {
        $voucher = Voucher::factory()->create([
            'voucher_value_original'  => 1000,
            'voucher_value_remaining' => 1000,
            'voucher_short_code'      => 'AB1234',
        ]);

        VoucherRedemption::withoutEvents(function () use ($voucher) {
            VoucherRedemption::factory()->create([
                'voucher_id'      => $voucher->id,
                'redeemed_amount' => 1000,
            ]);
        });

        VoucherService::collateVoucherAggregates($voucher);

        $voucher->refresh();
        self::assertEquals(0, $voucher->voucher_value_remaining);
        self::assertNull($voucher->voucher_short_code);
    }

    /**
     * @see \App\Services\VoucherService::collateVoucherAggregates()
     */
    #[Test]
    public function collateVoucherAggregatesDoesNotUpdateRedemptionFieldsWhenNoRedemptions(): void
    {
        $voucher = Voucher::factory()->create([
            'voucher_value_original'  => 1000,
            'voucher_value_remaining' => 1000,
            'num_voucher_redemptions' => 0,
            'last_redemption_at'      => null,
        ]);

        VoucherService::collateVoucherAggregates($voucher);

        $voucher->refresh();
        self::assertEquals(1000, $voucher->voucher_value_remaining);
        self::assertEquals(0, $voucher->num_voucher_redemptions);
        self::assertNull($voucher->last_redemption_at);
    }

    /**
     * @see \App\Services\VoucherService::collateVoucherAggregates()
     */
    #[Test]
    public function collateVoucherAggregatesThrowsExceptionWhenOverRedeemed(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessageMatches('/has had too many redemptions/');

        $voucher = Voucher::factory()->create([
            'voucher_value_original'  => 1000,
            'voucher_value_remaining' => 1000,
        ]);

        VoucherRedemption::withoutEvents(function () use ($voucher) {
            VoucherRedemption::factory()->create([
                'voucher_id'      => $voucher->id,
                'redeemed_amount' => 1500,
            ]);
        });

        VoucherService::collateVoucherAggregates($voucher);
    }
}
