<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('task_schedule_list', function (Blueprint $table) {
            $table->timestamp('last_run_time')->nullable()->after('schedule');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('task_schedule_list', function (Blueprint $table) {
            $table->dropColumn('last_run_time');
        });
    }
};
