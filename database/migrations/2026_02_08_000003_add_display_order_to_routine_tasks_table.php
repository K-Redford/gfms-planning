<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('routine_tasks', 'display_order')) {
            Schema::table('routine_tasks', function (Blueprint $table) {
                $table->unsignedInteger('display_order')->default(0)->after('id');
            });

            DB::table('routine_tasks')->update([
                'display_order' => DB::raw('id'),
            ]);
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('routine_tasks', 'display_order')) {
            Schema::table('routine_tasks', function (Blueprint $table) {
                $table->dropColumn('display_order');
            });
        }
    }
};
