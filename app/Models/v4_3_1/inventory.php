<?php

namespace App\Models\v4_3_1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class inventory extends Model
{
    use HasFactory;

    protected $connection = 'dynamic_connection';

    protected $table = 'inventory';

    protected $guarded = [];
}
