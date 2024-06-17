<?php

namespace App\Models\v1_0_0;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class invoice_number_pattern extends Model
{
    use HasFactory;

    protected $connection = 'dynamic_connection';

    public $timestamps = false;

    protected $table = 'invoice_number_patterns';

    protected $guarded = [];

}
