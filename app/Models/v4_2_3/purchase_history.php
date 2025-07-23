<?php

namespace App\Models\v4_2_3;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class purchase_history extends Model
{
    use HasFactory;

    protected $connection = 'dynamic_connection';

    protected $table = 'purchase_history';

    public $guarded = [];

}
