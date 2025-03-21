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
        Schema::table('products', function (Blueprint $table) {
            
            // Make the column nullable
            $table->string('name')->nullable()->change();
            $table->longText('description')->nullable()->change();
            $table->string('product_code')->nullable()->change();
            $table->string('unit')->nullable()->change();
            $table->double('price_per_unit')->nullable()->change();
            
            // Rename an existing column
            $table->renameColumn('product_code', 'sku'); // Rename 'product_code' to 'sku'
            
            //add new columns 
            $table->text('short_description')->nullable();
            $table->longtext('product_media')->nullable();
            $table->string('product_category', 50)->nullable();
            $table->integer('track_quantity')->default(1);
            $table->integer('continue_selling')->default(0);
            $table->string('product_type', 50)->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Reverse the changes made in the 'up' method

            // Rename column 'sku' back to 'product_code'
            $table->renameColumn('sku', 'product_code');

            // Revert changes made to columns (change nullable/column types back)
            $table->string('name')->nullable()->change();   
            $table->text('description')->nullable()->change();   
            $table->string('sku')->nullable()->change();   
            $table->string('unit')->nullable()->change();
            $table->double('price_per_unit')->nullable()->change();  
            
            // Drop the newly added columns
            $table->dropColumn('short_description');   
            $table->dropColumn('product_media');
            $table->dropColumn('product_category');
            $table->dropColumn('track_quantity');
            $table->dropColumn('continue_selling');
            $table->dropColumn('product_type');
        });
    }
};
