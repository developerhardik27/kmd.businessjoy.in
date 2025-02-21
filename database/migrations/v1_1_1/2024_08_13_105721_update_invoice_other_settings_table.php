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
            // The "invoice_other_settings" table exists and hasnot an "sgst" column...
            if (!Schema::hasColumn('invoice_other_settings', 'sgst')) {
                $table->double('sgst')->nullable()->after('year_start');
            }
            // The "invoice_other_settings" table exists and hasnot an "cgst" column...
            if (!Schema::hasColumn('invoice_other_settings', 'cgst')) {
                $table->double('cgst')->nullable()->after('sgst');
            }
            // The "invoice_other_settings" table exists and hasnot an "gst" column...
            if (!Schema::hasColumn('invoice_other_settings', 'gst')) {
                $table->integer('gst')->nullable()->default(0)->after('sgst');
            }
            // The "invoice_other_settings" table exists and hasnot an "customer_id" column...
            if (!Schema::hasColumn('invoice_other_settings', 'customer_id')) {
                $table->integer('customer_id')->default(1)->after('gst');
            }
            // The "invoice_other_settings" table exists and hasnot an "current_customer_id" column...
            if (!Schema::hasColumn('invoice_other_settings', 'current_customer_id')) {
                $table->integer('current_customer_id')->default(1)->after('customer_id');
            }
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
            if (Schema::hasColumn('invoice_other_settings', 'sgst')) {
                $table->dropColumn('sgst');
            }
            if (Schema::hasColumn('invoice_other_settings', 'cgst')) {
                $table->dropColumn('cgst');
            }
            if (Schema::hasColumn('invoice_other_settings', 'gst')) {
                $table->dropColumn('gst');
            }
            if (Schema::hasColumn('invoice_other_settings', 'customer_id')) {
                $table->dropColumn('customer_id');
            }
            if (Schema::hasColumn('invoice_other_settings', 'current_customer_id')) {
                $table->dropColumn('current_customer_id');
            }
            if (Schema::hasColumn('invoice_other_settings', 'invoice_number')) {
                $table->dropColumn('invoice_number');
            }
            if (Schema::hasColumn('invoice_other_settings', 'invoice_date')) {
                $table->dropColumn('invoice_date');
            }
        });
    }
};
