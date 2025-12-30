<?php

namespace App\Models\v4_3_1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class api_server_key extends Model
{
    use HasFactory;

    protected $connection = 'dynamic_connection';

    protected $table = 'api_server_keys';

    public $guarded = [];
}
