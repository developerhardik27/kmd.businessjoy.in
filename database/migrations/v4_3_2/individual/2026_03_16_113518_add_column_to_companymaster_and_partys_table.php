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
        // Add columns to companymasters
        Schema::table('companymasters', function (Blueprint $table) {
            $table->string('tmco')->nullable()->default(null);
        });

        // Add columns to partys
        Schema::table('partys', function (Blueprint $table) {
            $table->string('code')->nullable()->default(null);
            $table->string('tmco')->nullable()->default(null);
            $table->string('c')->nullable()->default('0'); // string default 0
            $table->string('bill_to')->nullable()->default(null); // string default 0
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop columns from companymasters
        Schema::table('companymasters', function (Blueprint $table) {
            $table->dropColumn('tmco');
        });

        // Drop columns from partys
        Schema::table('partys', function (Blueprint $table) {
            $table->dropColumn(['code', 'tmco', 'c','bill_to']);
        });
    }
};