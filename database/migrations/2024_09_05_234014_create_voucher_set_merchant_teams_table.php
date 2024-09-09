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
        Schema::create('voucher_set_merchant_teams', function (Blueprint $table) {
            $table->id();
            $table->uuid('voucher_set_id')->index('vsmt_vsi');
            $table->unsignedBigInteger('merchant_team_id')->index('vsmt_mti');
            $table->timestamps();
            $table->softDeletes();

            $table->index(
                [
                    'voucher_set_id',
                    'merchant_team_id',
                ],
                'vsmt_vsimti'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('voucher_set_merchant_teams');
    }
};
