<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('service_report_forms')) {
            return;
        }

        Schema::create('service_report_forms', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('serial_number')->unique();
            $table->unsignedInteger('plant_id');
            $table->string('company_name', 255);
            $table->string('site_address', 255)->nullable();
            $table->date('date_of_visit');
            $table->time('time_on_site')->nullable();
            $table->time('time_off_site')->nullable();
            $table->unsignedSmallInteger('site_time_minutes')->nullable();
            $table->unsignedSmallInteger('travel_time_hours')->nullable();
            $table->unsignedInteger('mileage')->nullable();
            $table->string('order_number', 50)->nullable();
            $table->string('transflo_ref', 10)->nullable();
            $table->string('charge_type', 30);
            $table->string('equipment_type', 30);
            $table->string('reported_fault', 255)->nullable();
            $table->text('report_details')->nullable();
            $table->json('departure_statuses')->nullable();
            $table->string('software_changes', 255)->nullable();
            $table->string('engineer_signature_path', 255)->nullable();
            $table->string('customer_signature_path', 255)->nullable();
            $table->string('customer_print_name', 255)->nullable();
            $table->string('customer_rank_civ', 255)->nullable();
            $table->string('customer_email', 255)->nullable();
            $table->text('notes')->nullable();
            $table->unsignedInteger('engineer_user_id');
            $table->timestamps();

            $table->foreign('plant_id')->references('id')->on('plants')->cascadeOnDelete();
            $table->foreign('engineer_user_id')->references('id')->on('app_users');
        });

        DB::statement('ALTER TABLE service_report_forms AUTO_INCREMENT = 40000');
    }

    public function down(): void
    {
        Schema::dropIfExists('service_report_forms');
    }
};
