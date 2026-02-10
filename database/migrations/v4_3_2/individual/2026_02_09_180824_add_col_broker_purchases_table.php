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
            $table->text('source')->nullable()->after('final_net_kg');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('broker_purchases', function (Blueprint $table) {
            $table->dropColumn('source');
        });
    }
};
