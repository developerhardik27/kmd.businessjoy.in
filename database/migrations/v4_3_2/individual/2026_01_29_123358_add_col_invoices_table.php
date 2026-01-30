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
        Schema::table('invoices', function (Blueprint $table) {
            $table->integer('transport_id')->default(0)->after('customer_id');
            $table->text('HSN')->nullable()->after('transport_id');
            $table->text('Description')->nullable()->after('HSN');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('transport_id');
            $table->dropColumn('HSN');
            $table->dropColumn('Description');
           
        });
    }
};
