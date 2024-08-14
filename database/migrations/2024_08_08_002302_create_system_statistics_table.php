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
        Schema::create('system_statistics', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('num_users')->default(0);
            $table->unsignedBigInteger('num_teams')->default(0);
            $table->unsignedBigInteger('num_voucher_sets')->default(0);
            $table->unsignedBigInteger('num_vouchers')->default(0);
            $table->unsignedBigInteger('num_voucher_redemptions')->default(0);
            $table->unsignedBigInteger('sum_voucher_value_total')->default(0);
            $table->unsignedBigInteger('sum_voucher_value_redeemed')->default(0);
            $table->unsignedBigInteger('sum_voucher_value_remaining')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_statistics');
    }
};
