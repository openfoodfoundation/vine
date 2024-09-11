<?php

namespace Tests\Unit\Services;

use App\Services\VoucherService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class VoucherServiceTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function isReturnsCorrectlyFormattedShortCodes(): void
    {
        for ($iteration = 1; $iteration <= 100; $iteration++) {

            $shortCode = VoucherService::generateRandomShortCode();

            // Always 6 letters long
            self::assertSame(6, strlen($shortCode));

            // Only contains alphanumeric characters
            self::assertTrue(ctype_alnum($shortCode));

            // Format is two uppercase letters followed by four digits
            self::assertMatchesRegularExpression('/^[A-Z]{2}[0-9]{4}$/', $shortCode);
        }
    }
}
