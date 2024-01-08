<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tbl_invoice_column extends Model
{
    use HasFactory;


    protected $fillable = [
        'column_name',
        'column_type',
        'company_id',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
        'is_active',
        'is_deleted'
    ];
}
