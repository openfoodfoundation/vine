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
        Schema::create('team_merchant_teams', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('team_id')->index('tmt_ti');
            $table->unsignedBigInteger('merchant_team_id')->index('tmt_mti');
            $table->timestamps();
            $table->softDeletes();
            $table->index(['team_id', 'merchant_team_id'], 'tst_timti');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('team_merchant_teams');
    }
};
