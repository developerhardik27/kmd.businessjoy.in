<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();
            $table->string('quotation_number', 30)->nullable();
            $table->dateTime('quotation_date')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->integer('customer_id');
            $table->longText('notes')->nullable();
            $table->float('total', 10, 2);
            $table->float('sgst', 10, 2)->nullable();
            $table->float('cgst', 10, 2)->nullable();
            $table->float('gst')->default(0);
            $table->float('grand_total', 10, 2);
            $table->integer('currency_id'); 
            $table->string('status', 30)->default('pending'); 
            $table->float('template_version')->default(1);
            $table->integer('company_id');
            $table->integer('company_details_id');
            $table->longText('show_col')->nullable();
            $table->mediumText('gstsettings')->nullable()->comment('store gst settings for edit and view quotation details');
            $table->string('quotation_number_type')->default('a')->comment('flag quotation number (auto-a , manual-m)');
            $table->integer('overdue_date')->nullable();
            $table->integer('t_and_c_id')->nullable();
            $table->integer('last_increment_number')->nullable()->comment('use during increment by customer');
            $table->integer('increment_type')->nullable()->comment('type-1 = by quotation , type-2 = by customer');
            $table->integer('pattern_type')->nullable()->comment('type-1 = local , type-2 = global');
            $table->text('remarks')->nullable();
            $table->integer('created_by');
            $table->integer('updated_by')->nullable();
            $table->timestamps();
            $table->integer('is_active')->default(1);
            $table->integer('is_deleted')->default(0);
            $table->integer('is_editable')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotations');
    }
};
