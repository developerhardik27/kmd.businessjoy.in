<?php

namespace App\Models\v4_3_1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class product extends Model
{
    use HasFactory;
    
    protected $connection = 'dynamic_connection';

    protected $table = 'products';

    public $guarded = [];

    // protected $fillable = [
    //    'name',
    //    'description',
    //    'product_code',
    //    'unit',
    //    'price_per_unit',
    //    'company_id',
    //    'created_by',
    //    'updated_by',
    //    'created_at',
    //    'updated_at',
    //    'is_active',
    //    'is_deleted'
    // ];
}
