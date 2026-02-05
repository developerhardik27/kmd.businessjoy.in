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
        Schema::table('payment_details', function (Blueprint $table) {
            $table->double('tds_amount', 15, 2)->default(0)->after('amount');
            $table->string('challan_no', 50)->nullable()->after('tds_amount');
            $table->string('tds_status', 50)->nullable()->after('challan_no');
            $table->tinyInteger('tds_credited')->default(0)->after('tds_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_details', function (Blueprint $table) {
            $table->dropColumn(['tds_amount','challan_no','tds_status','tds_credited']);
        });
    }
};
