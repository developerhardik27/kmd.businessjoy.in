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
        Schema::table('logistic_settings', function (Blueprint $table) {
            $table->string('download_copy')->default('always ask')->after('authorized_signatory');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('logistic_settings', function (Blueprint $table) {
            $table->dropColumn('download_copy');
        });
    }
};
