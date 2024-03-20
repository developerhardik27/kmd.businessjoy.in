<?php

namespace App\Models\v2_0_0;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class invoice_other_setting extends Model
{
    use HasFactory;

    protected $connection = 'dynamic_connection';

    protected $table = 'invoice_other_settings';

    protected $fillable = [
        
        'overdue_day',
        'year_start',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
        'is_active',
        'is_deleted'
    ];
}
