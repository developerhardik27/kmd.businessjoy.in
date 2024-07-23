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
        Schema::create('company_details', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->notNullable();
            $table->string('email', 50)->nullable();
            $table->bigInteger('contact_no')->nullable(); // Adjust the maximum length if necessary
            $table->string('house_no_building_name')->notNullable();
            $table->string('road_name_area_colony')->notNullable();
            $table->string('gst_no', 50)->nullable();
            $table->integer('country_id')->nullable(); // Adjust if you are referencing another table's id
            $table->integer('state_id')->nullable(); // Adjust if you are referencing another table's id
            $table->integer('city_id')->nullable(); // Adjust if you are referencing another table's id
            $table->integer('pincode')->nullable(); // Adjust the maximum length if necessary
            $table->string('img', 50)->nullable();
            $table->string('pr_sign_img', 50)->nullable();
            $table->timestamps(); // This will automatically create created_at and updated_at columns
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_details');
    }
};
