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
        Schema::create('partys', function (Blueprint $table) {
            $table->id(); // garden_id
            $table->string('name');
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
            $table->string('party_type', 20)->nullable();
            // Common columns
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->boolean('is_active')->default(1);
            $table->boolean('is_delete')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partys');
    }
};
