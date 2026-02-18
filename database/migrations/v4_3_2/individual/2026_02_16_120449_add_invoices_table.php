<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('consignment_number', 30)->after('inv_date');
            $table->dateTime('consignment_date')->default(DB::raw('CURRENT_TIMESTAMP'))->after('consignment_number');
            $table->float('igst', 15, 2)->after('cgst')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('consignment_number');
            $table->dropColumn('consignment_date');
            $table->dropColumn('igst');
        });
    }
};
