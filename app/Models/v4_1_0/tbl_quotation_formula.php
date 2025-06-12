<?php

namespace App\Models\v4_1_0;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tbl_quotation_formula extends Model
{
    use HasFactory;

    protected $connection = 'dynamic_connection';
 
    protected $table = 'tbl_quotation_formulas';

    protected $guarded = [];
}
