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
        Schema::create('companymasters', function (Blueprint $table) {
            $table->id(); 
            $table->string('company_name');
            $table->string('email')->nullable();
            $table->string('contact_person_name')->nullable();
            $table->string('mobile_1', 15)->nullable();
            $table->string('mobile_2', 15)->nullable();
            $table->integer('country_id')->nullable();
            $table->integer('state_id')->nullable();
            $table->integer('city_id')->nullable();
            $table->integer('pincode')->nullable();
            $table->text('address')->nullable();
            $table->string('gst_no', 20)->nullable();
            $table->string('pan', 20)->nullable();

            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('is_active')->default(1);
            $table->integer('is_deleted')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companymasters');
    }
};
