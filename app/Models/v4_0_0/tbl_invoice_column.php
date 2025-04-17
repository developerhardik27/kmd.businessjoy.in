<?php

namespace App\Models\v4_0_0;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tbl_invoice_column extends Model
{
    use HasFactory;
    
    protected $connection = 'dynamic_connection';


    public $guarded = [];

    // protected $fillable = [
    //     'column_name',
    //     'column_type',
    //     'column_width',
    //     'column_order',
    //     'is_hide',
    //     'company_id',
    //     'created_by',
    //     'updated_by',
    //     'created_at',
    //     'updated_at',
    //     'is_active',
    //     'is_deleted'
    // ];
}
