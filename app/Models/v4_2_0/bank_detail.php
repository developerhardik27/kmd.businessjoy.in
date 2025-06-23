<?php

namespace App\Models\v4_2_0;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class bank_detail extends Model
{
    use HasFactory;
    
    protected $connection = 'dynamic_connection';

    protected $table = 'bank_details';

    public $guarded = [];

}
