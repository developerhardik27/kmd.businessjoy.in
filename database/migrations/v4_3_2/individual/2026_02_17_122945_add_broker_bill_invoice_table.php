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
        Schema::table('broker_bill_invoice', function (Blueprint $table) {
            $table->double('cgst', 15, 2)->nullable()->after('igst');
            $table->double('sgst', 15, 2)->nullable()->after('cgst');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('broker_bill_invoice', function (Blueprint $table) {

            $table->dropColumn('cgst');
            $table->dropColumn('sgst');
        });
    }
};
