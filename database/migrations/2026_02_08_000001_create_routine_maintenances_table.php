<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('routine_maintenances', function (Blueprint $table) {
            $table->id();
            $table->integer('plant_id');
            $table->unsignedInteger('routine_year');
            $table->date('performed_at');
            $table->integer('performed_by');
            $table->string('asset_capture_status', 20)->default('pending'); // pending, complete
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['plant_id', 'routine_year']);
            $table->foreign('plant_id')->references('id')->on('plants')->cascadeOnDelete();
            $table->foreign('performed_by')->references('id')->on('app_users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('routine_maintenances');
    }
};
