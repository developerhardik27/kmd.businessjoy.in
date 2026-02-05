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
           $table->float('rate',8,2)->default(0)->after('brokerage');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
           Schema::table('broker_purchases', function (Blueprint $table) {
           $table->dropColumn('rate');
        });
    }
};
