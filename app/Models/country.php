<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class country extends Model
{
    use HasFactory;

    protected $table = 'country';

    protected $fillable = [    
       'country_name',
       'created_by',
       'updated_by',
       'created_at',
       'updated_at',
       'is_active',
       'is_deleted'
    ];
}
