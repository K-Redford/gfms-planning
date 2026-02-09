<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('routine_maintenance_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('routine_maintenance_id');
            $table->integer('routine_task_id');
            $table->boolean('completed')->default(false);
            $table->timestamps();

            $table->foreign('routine_maintenance_id')
                ->references('id')
                ->on('routine_maintenances')
                ->cascadeOnDelete();
            $table->foreign('routine_task_id')
                ->references('id')
                ->on('routine_tasks')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('routine_maintenance_items');
    }
};
