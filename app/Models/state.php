<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class state extends Model
{
    use HasFactory;

    protected $table = 'state';

    protected $fillable = [
       'country_id',
       'state_name',
       'created_by',
       'updated_by',
       'created_at',
       'updated_at',
       'is_active',
       'is_deleted'
    ];
}
