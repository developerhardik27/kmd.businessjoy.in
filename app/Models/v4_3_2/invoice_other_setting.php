<?php

namespace App\Models\v4_3_2;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class invoice_other_setting extends Model
{
    use HasFactory;

    protected $connection = 'dynamic_connection';

    protected $table = 'invoice_other_settings';

    protected $guarded = [];
}
