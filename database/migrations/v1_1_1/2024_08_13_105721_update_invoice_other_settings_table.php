<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     *  
     * Run the migrations.
     */ 
    public function up(): void
    {
        Schema::table('invoice_other_settings', function (Blueprint $table) {
            // The "invoice_other_settings" table exists and hasnot an "invoice_number" column...
            if (!Schema::hasColumn('invoice_other_settings', 'invoice_number')) {
                $table->integer('invoice_number')->default(0)->after('gst');
            }
            // The "invoice_other_settings" table exists and hasnot an "invoice_date" column...
            if (!Schema::hasColumn('invoice_other_settings', 'invoice_date')) {
                $table->integer('invoice_date')->default(0)->after('invoice_number');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoice_other_settings', function (Blueprint $table) {
            if (Schema::hasColumn('invoice_other_settings', 'invoice_number')) {
                $table->dropColumn('invoice_number');
            }
            if (Schema::hasColumn('invoice_other_settings', 'invoice_date')) {
                $table->dropColumn('invoice_date');
            }
        });
    }
};
