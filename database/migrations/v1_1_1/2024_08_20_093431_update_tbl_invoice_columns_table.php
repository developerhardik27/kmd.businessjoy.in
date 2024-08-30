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
        Schema::table('tbl_invoice_columns', function (Blueprint $table) {
            // The "tbl_invoice_columns" table exists and hasn't an "column_width" column...
            if (!Schema::hasColumn('tbl_invoice_columns', 'column_width')) {
                $table->string('column_width')->default(0)->after('column_type');
            }  
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_invoice_columns', function (Blueprint $table) {
            if (Schema::hasColumn('tbl_invoice_columns', 'column_width')) {
                $table->dropColumn('column_width');
            } 
        });
    }
};
