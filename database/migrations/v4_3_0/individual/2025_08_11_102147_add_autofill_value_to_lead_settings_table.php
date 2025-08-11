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
        Schema::table('lead_settings', function (Blueprint $table) {
            $table->string('autofill_value')->nullable()->after('city_default_value');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lead_settings', function (Blueprint $table) {
            $table->dropColumn('autofill_value');
        });
    }
};
