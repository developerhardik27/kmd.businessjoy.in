<?php

namespace App\Models\v1_1_0;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class blog extends Model
{
    use HasFactory;

    
    protected $connection = 'dynamic_connection';

    protected $table = 'blogs';

    public $guarded = [];
}
