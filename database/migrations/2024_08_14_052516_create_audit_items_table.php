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
        Schema::create('audit_items', function (Blueprint $table) {
            $table->id();
            $table->string('auditable_type')->index('ai_at');
            $table->string('auditable_id');
            $table->string('auditable_text');
            $table->unsignedBigInteger('team_id')->index('ai_ti');
            $table->timestamps();
            $table->softDeletes();

            $table->index(columns: ['auditable_type', 'auditable_id', 'auditable_text'], name: 'ai_ataiat');
            $table->index(columns: ['auditable_type', 'auditable_id'], name: 'ai_atai');
            $table->index(columns: ['auditable_text', 'team_id'], name: 'ai_atti');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_items');
    }
};
