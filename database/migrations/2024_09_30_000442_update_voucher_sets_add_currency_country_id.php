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

            $table->unsignedBigInteger('currency_country_id')
                ->nullable()
                ->index('vs_cci')
                ->after('voucher_template_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        Schema::table('voucher_sets', function (Blueprint $table) {
            $table->dropColumn('currency_country_id');
        });
    }
};
