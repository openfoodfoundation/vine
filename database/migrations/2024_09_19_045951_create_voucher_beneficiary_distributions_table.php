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
        Schema::create('voucher_beneficiary_distributions', function (Blueprint $table) {
            $table->id();
            $table->uuid('voucher_id')->index('vbd_vi');
            $table->uuid('voucher_set_id')->index('vbd_vsi');
            $table->unsignedBigInteger('created_by_user_id');
            $table->text('beneficiary_email_encrypted');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('voucher_beneficiary_distributions');
    }
};
