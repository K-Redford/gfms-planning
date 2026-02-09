<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_reports', function (Blueprint $table) {
            if (!Schema::hasColumn('service_reports', 'serial_number')) {
                $table->unsignedInteger('serial_number')->nullable()->unique();
            }
        });

        if (Schema::hasColumn('service_reports', 'serial_number')) {
            DB::statement('UPDATE service_reports SET serial_number = id + 39999 WHERE serial_number IS NULL');
            DB::statement('ALTER TABLE service_reports MODIFY serial_number INT UNSIGNED NOT NULL');
        }
    }

    public function down(): void
    {
        Schema::table('service_reports', function (Blueprint $table) {
            if (Schema::hasColumn('service_reports', 'serial_number')) {
                $table->dropUnique(['serial_number']);
                $table->dropColumn('serial_number');
            }
        });
    }
};
