<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vouchers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('voucher_short_code')->nullable()->index('v_vsc');
            $table->uuid('voucher_set_id')->index('v_vsi');
            $table->unsignedBigInteger('created_by_team_id')->index('v_ti');
            $table->unsignedBigInteger('allocated_to_service_team_id')->index('v_atsti');
            $table->boolean('is_test')->default(0)->index('vs_it');
            $table->unsignedBigInteger('voucher_value_original')->index('v_vvo');
            $table->unsignedBigInteger('voucher_value_remaining')->index('v_vvr');
            $table->integer('num_voucher_redemptions')->default(0)->index('v_nvr');
            $table->dateTime('last_redemption_at')->nullable('v_lra');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};
