<?php

namespace App\Models\v4_3_0;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class quotation extends Model
{
    use HasFactory;

    protected $connection = 'dynamic_connection';
 
    protected $table = 'quotations';

    protected $guarded = [];
}
