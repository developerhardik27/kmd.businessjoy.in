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
        Schema::create('transporter_billing_party', function (Blueprint $table) {
            $table->id();
            $table->string('firstname', 50)->nullable();
            $table->string('lastname', 50)->nullable();
            $table->string('company_name', 50)->nullable();
            $table->string('email', 50)->nullable();
            $table->string('contact_no', 15)->nullable();
            $table->string('house_no_building_name')->nullable();
            $table->string('road_name_area_colony')->nullable();
            $table->integer('country_id')->nullable();
            $table->integer('state_id')->nullable();
            $table->integer('city_id')->nullable();
            $table->integer('pincode')->nullable();
            $table->string('gst_no', 50)->nullable(); 
            $table->string('pan_number', 50)->nullable(); 
            $table->integer('created_by');
            $table->integer('updated_by')->nullable();
            $table->timestamps(); 
            $table->integer('is_active')->default(1);
            $table->integer('is_deleted')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transporter_billing_party');
    }
};
