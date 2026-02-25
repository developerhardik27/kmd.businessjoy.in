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
        Schema::table('mng_col', function (Blueprint $table) {
            // $table->dropColumn([
            //     'Garden',
            //     'Invoice_no',
            //     'Grade',
            //     'No_Of_Pkags',
            //     'Net_Oty_Per_Pkg',
            //     'Net_Weight_Kgs',
            //     'Rate_per_kg',
            //     'discount',
            //     'first_price',
            //     'percentage',
            //     'second_price',
            //     'third_price',
            //     'Total_Weights',
            //     'shortage'
            // ]);

            $table->string('Garden')->nullable();
            $table->string('Invoice_no')->nullable();
            $table->string('Grade')->nullable();

            $table->integer('No_Of_Pkags')->nullable();
            $table->integer('Net_Oty_Per_Pkg')->nullable();

            $table->decimal('Net_Weight_Kgs', 15, 2)->nullable();
            $table->decimal('Rate_per_kg', 15, 2)->nullable();
            $table->decimal('discount', 15, 2)->nullable();

            $table->integer('first_price')->nullable();
            $table->integer('percentage')->default(100);

            $table->decimal('second_price', 15, 2)->nullable();
            $table->decimal('third_price', 15, 2)->nullable();

            $table->decimal('Total_Weights', 15, 2)->nullable();
            $table->decimal('shortage', 15, 2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mng_col', function (Blueprint $table) {
            $table->dropColumn('Garden');
            $table->dropColumn('Invoice_no');
            $table->dropColumn('Grade');
            $table->dropColumn('No_Of_Pkags');
            $table->dropColumn('Net_Oty_Per_Pkg');
            $table->dropColumn('Net_Weight_Kgs');
            $table->dropColumn('Rate_per_kg');
            $table->dropColumn('discount');
            $table->dropColumn('first_price');
            $table->dropColumn('percentage');
            $table->dropColumn('second_price');
            $table->dropColumn('third_price');
            $table->dropColumn('Total_Weights');
            $table->dropColumn('shortage');
        });
    }
};
