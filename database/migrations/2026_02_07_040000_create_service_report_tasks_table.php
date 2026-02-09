<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_report_tasks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('implementation_completion_id')->nullable();
            $table->integer('plant_id');
            $table->integer('engineer_user_id');
            $table->unsignedBigInteger('service_report_id')->nullable();
            $table->string('status', 20)->default('missing'); // missing, draft, submitted
            $table->timestamps();

            $table->foreign('implementation_completion_id')
                ->references('id')
                ->on('implementation_completions')
                ->nullOnDelete();
            $table->foreign('plant_id')->references('id')->on('plants')->cascadeOnDelete();
            $table->foreign('engineer_user_id')->references('id')->on('app_users');
            $table->foreign('service_report_id')->references('id')->on('service_report_forms')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_report_tasks');
    }
};
