<?php

namespace App\Models\v4_3_2;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class companiesholiday extends Model
{
    use HasFactory;
     protected $connection = 'dynamic_connection';

    protected $table = 'companiesholidays';

    public $guarded = [];
}
