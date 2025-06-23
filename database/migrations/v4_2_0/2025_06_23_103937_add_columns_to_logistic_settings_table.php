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
        Schema::table('logistic_settings', function (Blueprint $table) {
            $table->string('gst_tax_payable_by')->nullable()->after('current_consignment_note_no');
            $table->string('weight')->nullable()->after('gst_tax_payable_by');
            $table->string('authorized_signatory')->nullable()->after('weight');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('logistic_settings', function (Blueprint $table) {
            $table->dropColumn([
                'gst_tax_payable_by',
                'weight',
                'authorized_signatory'
            ]);
        });
    }
};
