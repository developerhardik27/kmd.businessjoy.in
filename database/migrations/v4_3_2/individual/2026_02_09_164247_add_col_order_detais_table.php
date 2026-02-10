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
        Schema::table('order_details', function (Blueprint $table) {
            $table->integer('invoice_id')->nullable()->after('amount');
            // $table->double('shortage', 15, 2)->nullable()->after('net_kg');
            // $table->double('final_net_kg', 15, 2)->nullable()->after('shortage');
            // $table->integer('brokerbill_no')->nullable()->after('final_net_kg');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_details', function (Blueprint $table) {
            $table->dropColumn('invoice_id');
            
            // $table->dropColumn('final_net_kg');
            // $table->dropColumn('brokerbill_no');
        });
    }
};
