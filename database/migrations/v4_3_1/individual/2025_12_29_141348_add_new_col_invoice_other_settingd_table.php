<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public $defaultSettings;
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $this->defaultSettings = json_encode(['invoice', 'quotation']);
        Schema::table('invoice_other_settings', function (Blueprint $table) {
            $table->string('customer_dropdown')->default($this->defaultSettings);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoice_other_settings', function (Blueprint $table) {
            $table->dropColumn('customer_dropdown');
        });
    }
};
