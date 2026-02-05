<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('inv_no', 30);
            $table->dateTime('inv_date')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->integer('customer_id');
            $table->longText('notes')->nullable();
            $table->float('total', 15, 2);
            $table->float('sgst', 15, 2)->nullable();
            $table->float('cgst', 15, 2)->nullable();
            $table->float('gst')->default(0);
            $table->float('grand_total', 15, 2);
            $table->integer('currency_id');
            $table->string('payment_type', 30);
            $table->string('status', 30)->default('pending');
            $table->bigInteger('account_id');
            $table->float('template_version')->default(1);
            $table->integer('company_id');
            $table->integer('company_details_id');
            $table->longText('show_col')->nullable();
            $table->integer('overdue_date')->nullable();
            $table->integer('t_and_c_id')->nullable();
            $table->integer('last_increment_number')->nullable()->comment('use during increment by customer');
            $table->integer('increment_type')->nullable()->comment('type-1 = by invoice , type-2 = by customer');
            $table->integer('pattern_type')->nullable()->comment('type-1 = local , type-2 = global');
            $table->integer('created_by');
            $table->integer('updated_by')->nullable();
            $table->dateTime('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->dateTime('updated_at')->nullable();
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
        Schema::dropIfExists('invoices');
    }
};
