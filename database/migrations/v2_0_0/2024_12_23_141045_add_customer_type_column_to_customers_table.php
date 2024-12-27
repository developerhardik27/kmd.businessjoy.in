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
        Schema::table('customers', function (Blueprint $table) {
            if(!Schema::hasColumn('customers','customer_tye')){
                $table->string('customer_type')->after('gst_no')->default('invoice');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            if(Schema::hasColumn('customers','customer_tye')){
                $table->dropColumn('customer_type');
            }
        });
    }
};
