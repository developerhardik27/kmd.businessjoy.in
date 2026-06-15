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
        Schema::table('orders', function (Blueprint $table) {
            $table->integer('reference')->nullable()->after('transport');
            $table->date('expected_dispatch_date')->nullable()->after('credit_days');
            $table->string('dispatch_status')->default('Pending')->after('expected_dispatch_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['reference', 'expected_dispatch_date', 'dispatch_status']);
        });
    }
};
