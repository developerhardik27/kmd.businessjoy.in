<?php

namespace App\Models\v4_2_2;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class quotation_other_setting extends Model
{
    use HasFactory;

    
    protected $connection = 'dynamic_connection';
 
    protected $table = 'quotation_other_settings';

    protected $guarded = [];
}
