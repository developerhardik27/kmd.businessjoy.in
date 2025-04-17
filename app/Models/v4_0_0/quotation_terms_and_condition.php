<?php

namespace App\Models\v4_0_0;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class quotation_terms_and_condition extends Model
{
    use HasFactory;

    
    protected $connection = 'dynamic_connection';
 
    protected $table = 'quotation_terms_and_conditions';

    protected $guarded = [];
}
