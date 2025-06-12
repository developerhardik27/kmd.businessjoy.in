<?php

namespace App\Models\v4_1_0;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class temp_image extends Model
{
    use HasFactory;

    protected $connection = 'dynamic_connection'; 
    protected $table = 'temp_images';

    public $guarded = [];
}
