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
         Schema::table('invoice_other_settings', function (Blueprint $table) {
            $table->double('igst', 15, 2)->after('cgst')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         Schema::table('invoice_other_settings', function (Blueprint $table) {
            $table->dropColumn('igst');
        });
    }
};
