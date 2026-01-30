<?php

namespace App\Models\v4_3_2;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class garden extends Model
{
    use HasFactory;

    protected $connection = 'dynamic_connection';

    protected $table = 'gardens';

    public $guarded = [];
}
