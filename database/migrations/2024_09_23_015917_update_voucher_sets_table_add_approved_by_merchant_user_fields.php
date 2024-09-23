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

            $table->unsignedBigInteger('merchant_approval_request_id')
                ->nullable()
                ->index('vs_mari')
                ->after('is_denomination_valid')
                ->comment('ID of the first approval request that has been approved.');

            $table->index(
                [
                    'is_denomination_valid',
                    'voucher_generation_started_at',
                    'voucher_generation_finished_at',
                    'merchant_approval_request_id',
                ],
                'vs_idvvgsavgfamari'
            );

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('voucher_sets', function (Blueprint $table) {
            $table->dropColumn(['merchant_approval_request_id']);
            $table->dropIndex('vs_idvvgsavgfamari');
        });
    }
};
