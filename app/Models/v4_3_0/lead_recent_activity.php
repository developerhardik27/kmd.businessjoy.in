<?php

namespace App\Models\v4_3_0;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class lead_recent_activity extends Model
{
    use HasFactory;

     protected $connection = 'dynamic_connection';

    protected $table = 'lead_recent_activities';

    public $guarded = [];
}
