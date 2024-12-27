<?php

namespace App\Models\v2_0_0;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tbl_quotation_column extends Model
{
    use HasFactory;

    protected $connection = 'dynamic_connection';
 
    protected $table = 'tbl_quotation_columns';

    protected $guarded = [];
}
