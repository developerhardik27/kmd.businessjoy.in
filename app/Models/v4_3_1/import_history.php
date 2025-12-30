<?php

namespace App\Models\v4_3_1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class import_history extends Model
{
    use HasFactory;

    protected $connection = 'dynamic_connection';

    protected $table = 'import_histories';

    protected $guarded = [];
}
