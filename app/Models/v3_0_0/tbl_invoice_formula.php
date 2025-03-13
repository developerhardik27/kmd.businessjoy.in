<?php

namespace App\Models\v3_0_0;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tbl_invoice_formula extends Model
{
    use HasFactory;

    protected $connection = 'dynamic_connection';
    
    public $guarded = [];

    // protected $fillable = [
    //     'first_column',
    //     'operation',
    //     'second_column',
    //     'output_column',
    //     'formula_order',
    //     'company_id',
    //     'created_by',
    //     'updated_by',
    //     'created_at',
    //     'updated_at',
    //     'is_active',
    //     'is_deleted'
    // ];
}
