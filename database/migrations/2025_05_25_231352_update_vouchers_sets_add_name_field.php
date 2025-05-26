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

            $table->string('name')
                ->nullable()
                ->after('id')
                ->index('vs_n');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropColumns('voucher_sets', ['name']);
    }
};
