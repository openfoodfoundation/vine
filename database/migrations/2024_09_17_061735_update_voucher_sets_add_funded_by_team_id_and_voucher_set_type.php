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

            $table->unsignedBigInteger('funded_by_team_id')
                ->nullable()
                ->index('vs_fbti')
                ->after('allocated_to_service_team_id');

            $table->string('voucher_set_type')
                ->index('vs_vst')
                ->after('is_test');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('voucher_sets', function (Blueprint $table) {
            $table->dropColumn(['funded_by_team_id', 'voucher_set_type']);
        });
    }
};
