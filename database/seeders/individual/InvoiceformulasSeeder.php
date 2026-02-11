<?php

namespace Database\Seeders\individual;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class InvoiceformulasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tbl_invoice_formulas = array(
            array('id' => '1', 'first_column' => 'No Of Pkags', 'operation' => '*', 'second_column' => 'Net Oty Per Pkg', 'output_column' => 'Total Weights', 'formula_order' => '1', 'company_id' => '35', 'created_by' => '19', 'updated_by' => '2', 'created_at' => '2026-01-30 17:41:33', 'updated_at' => '2026-02-05 15:01:59', 'is_active' => '1', 'is_deleted' => '0'),
            array('id' => '2', 'first_column' => 'Net Weight Kgs', 'operation' => '*', 'second_column' => 'Rate per kg', 'output_column' => 'first price', 'formula_order' => '3', 'company_id' => '35', 'created_by' => '19', 'updated_by' => NULL, 'created_at' => '2026-01-30 17:41:33', 'updated_at' => '2026-02-05 15:01:59', 'is_active' => '1', 'is_deleted' => '0'),
            array('id' => '3', 'first_column' => 'first price', 'operation' => '*', 'second_column' => 'discount', 'output_column' => 'second price', 'formula_order' => '4', 'company_id' => '35', 'created_by' => '19', 'updated_by' => NULL, 'created_at' => '2026-01-30 17:41:33', 'updated_at' => '2026-02-05 15:01:59', 'is_active' => '1', 'is_deleted' => '0'),
            array('id' => '4', 'first_column' => 'second price', 'operation' => '/', 'second_column' => 'percentage', 'output_column' => 'third price', 'formula_order' => '5', 'company_id' => '35', 'created_by' => '19', 'updated_by' => NULL, 'created_at' => '2026-01-30 17:41:33', 'updated_at' => '2026-02-05 15:01:59', 'is_active' => '1', 'is_deleted' => '0'),
            array('id' => '5', 'first_column' => 'first price', 'operation' => '-', 'second_column' => 'third price', 'output_column' => 'Amount', 'formula_order' => '6', 'company_id' => '35', 'created_by' => '19', 'updated_by' => NULL, 'created_at' => '2026-01-30 17:41:33', 'updated_at' => '2026-02-05 15:01:59', 'is_active' => '1', 'is_deleted' => '0'),
            array('id' => '6', 'first_column' => 'Total Weights', 'operation' => '-', 'second_column' => 'shortage', 'output_column' => 'Net Weight Kgs', 'formula_order' => '2', 'company_id' => '2', 'created_by' => '2', 'updated_by' => NULL, 'created_at' => '2026-02-05 14:16:37', 'updated_at' => '2026-02-05 15:01:59', 'is_active' => '1', 'is_deleted' => '0')
        );

        $chunks = array_chunk($tbl_invoice_formulas, 1); // split into 1 rows each

        foreach ($chunks as $chunk) {
            DB::table('tbl_invoice_formulas')->insert($chunk);
        }
    }
}
