<?php

namespace App\Models\v4_0_0;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class product_category extends Model
{
    use HasFactory;

    protected $connection = 'dynamic_connection';

    protected $table = 'product_categories';

    public $guarded = [];
}
