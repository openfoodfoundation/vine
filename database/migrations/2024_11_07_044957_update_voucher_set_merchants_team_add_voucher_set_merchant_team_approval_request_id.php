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
        Schema::table('voucher_set_merchant_teams', function (Blueprint $table) {

            $table->unsignedBigInteger('voucher_set_merchant_team_approval_request_id')
                ->nullable()
                ->index('vsmt_vsmtari')
                ->after('merchant_team_id')
                ->comment('The DB ID of the approved merchant approval request');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropColumns('voucher_set_merchant_teams', [
            'voucher_set_merchant_team_approval_request_id',
        ]);
    }
};
