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
        Schema::create('consignor_copy', function (Blueprint $table) {
            $table->id();
            $table->integer('consingment_note_no')->nullable();
            $table->date('loading_date')->nullable();
            $table->date('stuffing_date')->nullable();
            $table->string('truck_number')->nullable();
            $table->string('driver_name')->nullable();
            $table->string('licence_number')->nullable();
            $table->string('mobile_number')->nullable();
            $table->string('from')->nullable();
            $table->string('to')->nullable();
            $table->string('to_2')->nullable();
            $table->string('gst_tax_payable_by')->nullable();
            $table->integer('consignor_id')->nullable();
            $table->integer('consignee_id')->nullable();
            $table->string('cha')->nullable();
            $table->string('type')->nullable();
            $table->string('container_no')->nullable();
            $table->integer('size')->nullable();
            $table->string('shipping_line')->nullable();
            $table->string('seal_no')->nullable();
            $table->string('be_inv_no')->nullable();
            $table->string('port')->nullable();
            $table->string('pod')->nullable();
            $table->string('service')->nullable();
            $table->string('sac_code')->nullable();
            $table->string('weight_type')->nullable();
            $table->float('actual')->nullable();
            $table->float('charged')->nullable();
            $table->float('value')->nullable();
            $table->float('paid')->nullable();
            $table->float('to_pay')->nullable();
            $table->date('reached_at_factory_date')->nullable();
            $table->time('reached_at_factory_time')->nullable();
            $table->date('left_from_factory_date')->nullable();
            $table->time('left_from_factory_time')->nullable();
            $table->integer('t_and_c_id')->nullable();
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
        Schema::dropIfExists('consignor_copy');
    }
};
