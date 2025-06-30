<?php

namespace App\Models\v4_2_1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class purchase_order_detail_history extends Model
{
    use HasFactory;

    
    protected $connection = 'dynamic_connection';

    protected $table = 'purchase_order_detail_history';

    public $guarded = [];
}
