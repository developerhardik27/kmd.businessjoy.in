<?php

namespace App\Models\v4_3_1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class blog extends Model
{
    use HasFactory;

    
    protected $connection = 'dynamic_connection';

    protected $table = 'blogs';

    public $guarded = [];
}
