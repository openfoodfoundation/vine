<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVoucherRedemptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('voucher_redemptions', function (Blueprint $table) {
            $table->id();
            $table->uuid('voucher_id')->index('vr_si');
            $table->uuid('voucher_set_id')->index('vr_vsi');
            $table->unsignedBigInteger('redeemed_amount')->index('vr_ra');
            $table->unsignedBigInteger('redeemed_by_user_id')->index('vr_rbui');
            $table->unsignedBigInteger('redeemed_by_team_id')->index('vr_rbti');
            $table->boolean('is_test')->default(0)->index('vr_it');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('voucher_redemptions');
    }
}
