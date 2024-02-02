<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class company extends Model
{
    use HasFactory;

    protected $table = 'company';

    protected $fillable = [
       'company_details_id',
       'dbname',
       'created_by',
       'updated_by',
       'created_at',
       'updated_at',
       'is_active',
       'is_deleted'
    ];
}
