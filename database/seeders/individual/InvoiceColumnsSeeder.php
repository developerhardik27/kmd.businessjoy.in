<?php

namespace Database\Seeders\individual;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class InvoiceColumnsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tbl_invoice_columns = array(
            array('id' => '1', 'column_name' => 'Garden', 'column_type' => 'text', 'column_width' => '12', 'default_value' => NULL, 'column_order' => '1', 'is_hide' => '0', 'company_id' => '35', 'created_by' => '19', 'updated_by' => '2', 'created_at' => '2026-01-30 17:34:38', 'updated_at' => '2026-02-05 15:28:36', 'is_active' => '1', 'is_deleted' => '0'),
            array('id' => '2', 'column_name' => 'Invoice no', 'column_type' => 'text', 'column_width' => '10', 'default_value' => NULL, 'column_order' => '2', 'is_hide' => '0', 'company_id' => '35', 'created_by' => '19', 'updated_by' => NULL, 'created_at' => '2026-01-30 17:34:59', 'updated_at' => '2026-02-05 14:14:32', 'is_active' => '1', 'is_deleted' => '0'),
            array('id' => '3', 'column_name' => 'Grade', 'column_type' => 'text', 'column_width' => '8', 'default_value' => NULL, 'column_order' => '3', 'is_hide' => '0', 'company_id' => '35', 'created_by' => '19', 'updated_by' => '2', 'created_at' => '2026-01-30 17:35:16', 'updated_at' => '2026-02-05 15:20:53', 'is_active' => '1', 'is_deleted' => '0'),
            array('id' => '4', 'column_name' => 'No Of Pkags', 'column_type' => 'number', 'column_width' => '10', 'default_value' => NULL, 'column_order' => '4', 'is_hide' => '0', 'company_id' => '35', 'created_by' => '19', 'updated_by' => NULL, 'created_at' => '2026-01-30 17:35:34', 'updated_at' => '2026-02-05 14:14:32', 'is_active' => '1', 'is_deleted' => '0'),
            array('id' => '5', 'column_name' => 'Net Oty Per Pkg', 'column_type' => 'number', 'column_width' => '10', 'default_value' => NULL, 'column_order' => '5', 'is_hide' => '0', 'company_id' => '35', 'created_by' => '19', 'updated_by' => NULL, 'created_at' => '2026-01-30 17:35:48', 'updated_at' => '2026-02-05 14:14:32', 'is_active' => '1', 'is_deleted' => '0'),
            array('id' => '6', 'column_name' => 'Net Weight Kgs', 'column_type' => 'number', 'column_width' => '10', 'default_value' => NULL, 'column_order' => '8', 'is_hide' => '0', 'company_id' => '35', 'created_by' => '19', 'updated_by' => NULL, 'created_at' => '2026-01-30 17:36:08', 'updated_at' => '2026-02-05 14:14:32', 'is_active' => '1', 'is_deleted' => '0'),
            array('id' => '7', 'column_name' => 'Rate per kg', 'column_type' => 'decimal', 'column_width' => '10', 'default_value' => NULL, 'column_order' => '9', 'is_hide' => '0', 'company_id' => '35', 'created_by' => '19', 'updated_by' => NULL, 'created_at' => '2026-01-30 17:36:53', 'updated_at' => '2026-02-05 14:14:32', 'is_active' => '1', 'is_deleted' => '0'),
            array('id' => '8', 'column_name' => 'discount', 'column_type' => 'number', 'column_width' => '6', 'default_value' => NULL, 'column_order' => '10', 'is_hide' => '0', 'company_id' => '35', 'created_by' => '19', 'updated_by' => '2', 'created_at' => '2026-01-30 17:37:17', 'updated_at' => '2026-02-05 15:30:09', 'is_active' => '1', 'is_deleted' => '0'),
            array('id' => '9', 'column_name' => 'first price', 'column_type' => 'number', 'column_width' => '5', 'default_value' => NULL, 'column_order' => '11', 'is_hide' => '1', 'company_id' => '35', 'created_by' => '19', 'updated_by' => NULL, 'created_at' => '2026-01-30 17:37:33', 'updated_at' => '2026-02-05 14:14:32', 'is_active' => '1', 'is_deleted' => '0'),
            array('id' => '10', 'column_name' => 'percentage', 'column_type' => 'number', 'column_width' => '0', 'default_value' => '100', 'column_order' => '12', 'is_hide' => '1', 'company_id' => '35', 'created_by' => '19', 'updated_by' => NULL, 'created_at' => '2026-01-30 17:38:32', 'updated_at' => '2026-02-05 14:14:32', 'is_active' => '1', 'is_deleted' => '0'),
            array('id' => '11', 'column_name' => 'second price', 'column_type' => 'decimal', 'column_width' => '0', 'default_value' => NULL, 'column_order' => '13', 'is_hide' => '1', 'company_id' => '35', 'created_by' => '19', 'updated_by' => NULL, 'created_at' => '2026-01-30 17:38:56', 'updated_at' => '2026-02-05 14:14:32', 'is_active' => '1', 'is_deleted' => '0'),
            array('id' => '12', 'column_name' => 'third price', 'column_type' => 'decimal', 'column_width' => '0', 'default_value' => NULL, 'column_order' => '14', 'is_hide' => '1', 'company_id' => '35', 'created_by' => '19', 'updated_by' => NULL, 'created_at' => '2026-01-30 17:39:14', 'updated_at' => '2026-02-05 14:14:32', 'is_active' => '1', 'is_deleted' => '0'),
            array('id' => '13', 'column_name' => 'Total Weights', 'column_type' => 'decimal', 'column_width' => '5', 'default_value' => NULL, 'column_order' => '6', 'is_hide' => '1', 'company_id' => '35', 'created_by' => '2', 'updated_by' => NULL, 'created_at' => '2026-02-05 14:11:56', 'updated_at' => '2026-02-05 14:18:38', 'is_active' => '1', 'is_deleted' => '0'),
            array('id' => '14', 'column_name' => 'shortage', 'column_type' => 'decimal', 'column_width' => '5', 'default_value' => '0', 'column_order' => '7', 'is_hide' => '0', 'company_id' => '35', 'created_by' => '2', 'updated_by' => '2', 'created_at' => '2026-02-05 14:12:30', 'updated_at' => '2026-02-06 09:59:41', 'is_active' => '1', 'is_deleted' => '0')
        );

        $chunks = array_chunk($tbl_invoice_columns, 5); // split into 1000 rows each

        foreach ($chunks as $chunk) {
            DB::table('tbl_invoice_columns')->insert($chunk);
        }
    }
}
