<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('voucher_sets', function (Blueprint $table) {

            $table->unsignedBigInteger('voucher_template_id')
                ->nullable()
                ->index('vs_vti')
                ->after('created_by_user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        Schema::table('voucher_sets', function (Blueprint $table) {

            $table->dropColumn('voucher_template_id');

        });
    }
};
