<?php

namespace App\Models\v4_2_2;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class product_column_mapping extends Model
{
    use HasFactory;
    
    protected $connection = 'dynamic_connection';

    protected $table = 'product_column_mapping';

    public $guarded = [];
    
}
