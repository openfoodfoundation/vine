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
        Schema::create('team_service_teams', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('team_id')->index('tst_ti');
            $table->unsignedBigInteger('service_team_id')->index('tst_sti');
            $table->timestamps();
            $table->softDeletes();
            $table->index(['team_id', 'service_team_id'], 'tst_tisti');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('team_service_teams');
    }
};
