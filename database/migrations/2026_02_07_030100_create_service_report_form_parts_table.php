<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('service_report_form_parts')) {
            return;
        }

        Schema::create('service_report_form_parts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('service_report_id');
            $table->string('part_description', 255)->nullable();
            $table->string('stock_code', 50)->nullable();
            $table->unsignedSmallInteger('quantity')->nullable();
            $table->decimal('price_each', 10, 2)->nullable();
            $table->decimal('total_price', 10, 2)->nullable();
            $table->timestamps();

            $table->foreign('service_report_id')->references('id')->on('service_report_forms')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_report_form_parts');
    }
};
