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
            $table->text('god_name_show/hide')->default(1);
            $table->text('customer_dropdown')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoice_other_settings', function (Blueprint $table) {
            $table->dropColumn('god_name_show/hide');
            $table->dropColumn('customer_dropdown');
        });
    }
};
