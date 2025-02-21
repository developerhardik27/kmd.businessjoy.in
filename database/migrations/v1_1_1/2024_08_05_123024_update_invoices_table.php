<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {

            if (!Schema::hasColumn('invoices', 'gstsettings')) {
                $table->mediumText('gstsettings')->nullable()->after('show_col')->comment('store gst settings for edit and view invoice details');
            }
            if (!Schema::hasColumn('invoices', 'inv_number_type')) {
                $table->string('inv_number_type')->default('a')->after('gstsettings')->comment('flag invoice number (auto-a , manual-m)');
            }

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {

            if (Schema::hasColumn('invoices', 'gstsettings')) {
                $table->dropColumn('gstsettings');
            }
            if (Schema::hasColumn('invoices', 'inv_number_type')) {
                $table->dropColumn('inv_number_type');
            }
        });
    }
};
