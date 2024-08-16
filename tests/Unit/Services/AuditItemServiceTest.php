<?php

namespace Tests\Unit\Services;

use App\Models\AuditItem;
use App\Models\Team;
use App\Models\User;
use App\Models\Voucher;
use App\Models\VoucherSet;
use App\Services\AuditItemService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Str;
use Tests\TestCase;

class AuditItemServiceTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function testCreateAuditItemForEvent(): void
    {
        $actioningUser = User::factory()->createQuietly();

        $num = rand(0, 3);

        // If $num == 0, stays as user
        $auditableModel = User::factory()->createQuietly();

        if ($num === 1) {
            $auditableModel = Team::factory()->createQuietly();
        }

        if ($num === 2) {
            $auditableModel = Voucher::factory()->createQuietly();
        }

        if ($num === 3) {
            $auditableModel = VoucherSet::factory()->createQuietly();
        }

        $eventText = Str::random($num * $num);

        $auditItem = AuditItemService::createAuditItemForEvent(
            actioningUser: $actioningUser,
            model: $auditableModel,
            eventText: $eventText,
        );

        self::assertInstanceOf(AuditItem::class, $auditItem);
        self::assertSame(get_class($auditableModel), $auditItem->auditable_type);
        self::assertSame($auditableModel->id, $auditItem->auditable_id);
        self::assertSame($eventText, $auditItem->auditable_text);
    }
}
