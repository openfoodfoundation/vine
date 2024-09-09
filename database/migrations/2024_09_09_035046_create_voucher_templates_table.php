<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('voucher_templates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('team_id')->index('vt_ti');
            $table->unsignedBigInteger('created_by_user_id')->index('vt_cbui');
            $table->string('voucher_template_path')->index('vt_vtp');
            $table->string('voucher_example_template_path')->nullable();
            $table->string('overlay_font_path')->default('fonts/OpenSans_Condensed-Bold.ttf');
            $table->unsignedSmallInteger('voucher_qr_size_px')->default(300);
            $table->unsignedSmallInteger('voucher_qr_x')->default(100);
            $table->unsignedSmallInteger('voucher_qr_y')->default(200);
            $table->unsignedSmallInteger('voucher_code_size_px')->default(32);
            $table->unsignedSmallInteger('voucher_code_x')->default(100);
            $table->unsignedSmallInteger('voucher_code_y')->default(140);
            $table->string('voucher_code_prefix')->nullable()->default('Code:');
            $table->unsignedSmallInteger('voucher_expiry_size_px')->default(32);
            $table->unsignedSmallInteger('voucher_expiry_x')->default(100);
            $table->unsignedSmallInteger('voucher_expiry_y')->default(180);
            $table->string('voucher_expiry_prefix')->nullable()->default('Expires:');
            $table->unsignedSmallInteger('voucher_value_size_px')->default(32);
            $table->unsignedSmallInteger('voucher_value_x')->default(100);
            $table->unsignedSmallInteger('voucher_value_y')->default(220);
            $table->string('voucher_value_prefix')->nullable()->default('$');
            $table->dateTime('archived_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('voucher_templates');
    }
};
