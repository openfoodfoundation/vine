<?php

use App\Enums\VoucherSetMerchantTeamApprovalRequestStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('voucher_set_merchant_team_approval_requests', function (Blueprint $table) {
            $table->id();
            $table->uuid('voucher_set_id')->index('vsmtar_vsi');
            $table->unsignedBigInteger('merchant_user_id')->index('vsmtar_mui');
            $table->string('approval_status')->default(VoucherSetMerchantTeamApprovalRequestStatus::READY->value);
            $table->dateTime('approval_status_last_updated_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('voucher_set_merchant_team_approval_requests');
    }
};
