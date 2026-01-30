<?php

namespace App\Models\v4_3_2;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class quotation_number_pattern extends Model
{
    use HasFactory;

    
    protected $connection = 'dynamic_connection';

    public $timestamps = false;

    protected $table = 'quotation_number_patterns';

    protected $guarded = [];

}
