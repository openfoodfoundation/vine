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
        Schema::create('voucher_sets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('created_by_team_id')->index('vs_cbti');
            $table->unsignedBigInteger('allocated_to_service_team_id')->index('vs_atsti');
            $table->unsignedBigInteger('created_by_user_id')->index('vs_cbui');
            $table->boolean('is_test')->default(0)->index('vs_it');
            $table->unsignedBigInteger('total_set_value')->default(0)->index('vs_tsv');
            $table->unsignedBigInteger('total_set_value_remaining')->default(0)->index('vs_tsvr');
            $table->unsignedBigInteger('num_vouchers')->default(0)->index('vs_nv');
            $table->unsignedBigInteger('num_voucher_redemptions')->default(0)->index('vs_nvr');
            $table->json('denomination_json')->nullable();
            $table->boolean('is_denomination_valid')->default(0);
            $table->dateTime('voucher_generation_started_at')->nullable();
            $table->dateTime('voucher_generation_finished_at')->nullable();
            $table->dateTime('last_redemption_at')->nullable()->index('vs_lra');
            $table->dateTime('expires_at')->nullable()->index('vs_ea');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('voucher_sets');
    }
};
