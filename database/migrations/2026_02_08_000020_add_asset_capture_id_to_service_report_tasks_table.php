<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_report_tasks', function (Blueprint $table) {
            if (!Schema::hasColumn('service_report_tasks', 'asset_capture_id')) {
                $table->integer('asset_capture_id')->nullable()->after('implementation_completion_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('service_report_tasks', function (Blueprint $table) {
            if (Schema::hasColumn('service_report_tasks', 'asset_capture_id')) {
                $table->dropColumn('asset_capture_id');
            }
        });
    }
};
