<?php

namespace App\Models\v4_2_1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tbllead extends Model
{
    use HasFactory;
    
    protected $connection = 'dynamic_connection';

    protected $table = 'tbllead';

    public $guarded = [];
    
}
