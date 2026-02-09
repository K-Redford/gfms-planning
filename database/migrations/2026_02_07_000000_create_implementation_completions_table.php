<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('implementation_completions', function (Blueprint $table) {
            $table->id();
            $table->integer('plant_id')->unique();
            $table->integer('implemented_by');
            $table->date('implemented_at');
            $table->timestamps();

            $table->foreign('plant_id')->references('id')->on('plants')->cascadeOnDelete();
            $table->foreign('implemented_by')->references('id')->on('app_users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('implementation_completions');
    }
};
