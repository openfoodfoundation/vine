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
        Schema::table('voucher_sets', function (Blueprint $table) {

            $table->unsignedBigInteger('num_vouchers_fully_redeemed')
                ->default(0)
                ->after('num_vouchers');

            $table->unsignedBigInteger('num_vouchers_partially_redeemed')
                ->default(0)
                ->after('num_vouchers_fully_redeemed');

            $table->unsignedBigInteger('num_vouchers_unredeemed')
                ->default(0)
                ->after('num_vouchers_partially_redeemed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('voucher_sets', function (Blueprint $table) {
            $table->dropColumn('num_vouchers_fully_redeemed');
            $table->dropColumn('num_vouchers_partially_redeemed');
            $table->dropColumn('num_vouchers_unredeemed');
        });
    }
};
