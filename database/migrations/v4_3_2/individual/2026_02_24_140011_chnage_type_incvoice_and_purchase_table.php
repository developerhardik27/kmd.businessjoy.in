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

        Schema::table('broker_purchases', function (Blueprint $table) {
            // Change brokrage column to DECIMAL(8,2), nullable, default null
            $table->decimal('brokerage', 8, 2)->nullable()->change();
        });

        Schema::table('invoices', function (Blueprint $table) {
            // Change transport_id column to INT, nullable, default null
            $table->integer('transport_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('broker_purchases', function (Blueprint $table) {
            // Revert brokrage column to DECIMAL(15,2), nullable, default null
            $table->decimal('brokerage', 15, 2)->nullable()->change();
        });

        Schema::table('invoices', function (Blueprint $table) {
            // Revert transport_id column to INT, nullable, default 0
            $table->integer('transport_id')->nullable()->change();
        });
    }
};