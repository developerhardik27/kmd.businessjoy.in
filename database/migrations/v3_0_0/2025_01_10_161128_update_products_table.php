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
        // Step 1: Rename the column in its own block
        Schema::table('products', function (Blueprint $table) {
            $table->renameColumn('product_code', 'sku');
        });

        // Step 2: Modify existing columns (including the newly renamed one)
        Schema::table('products', function (Blueprint $table) {
            $table->string('name')->nullable()->change();
            $table->longText('description')->nullable()->change();
            $table->string('sku')->nullable()->change(); // Now using 'sku'
            $table->string('unit')->nullable()->change();
            $table->double('price_per_unit')->nullable()->change();

            // Add new columns
            $table->text('short_description')->nullable();
            $table->longText('product_media')->nullable();
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
        // Step 1: Rename back
        Schema::table('products', function (Blueprint $table) {
            $table->renameColumn('sku', 'product_code');
        });

        // Step 2: Revert changes
        Schema::table('products', function (Blueprint $table) {
            $table->string('name')->nullable(false)->change();
            $table->text('description')->nullable(false)->change();
            $table->string('product_code')->nullable(false)->change(); // Using 'product_code' now
            $table->string('unit')->nullable(false)->change();
            $table->double('price_per_unit')->nullable(false)->change();

            // Drop the added columns
            $table->dropColumn([
                'short_description',
                'product_media',
                'product_category',
                'track_quantity',
                'continue_selling',
                'product_type'
            ]);
        });
    }
};
