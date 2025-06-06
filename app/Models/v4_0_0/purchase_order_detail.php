<?php

namespace App\Models\v4_0_0;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class purchase_order_detail extends Model
{
    use HasFactory;

    
    protected $connection = 'dynamic_connection';

    protected $table = 'purchase_order_details';

    public $guarded = [];
}
