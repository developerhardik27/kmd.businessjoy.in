<?php

namespace App\Models\v4_0_0;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class reminder extends Model
{
    use HasFactory;

    protected $connection = 'dynamic_connection';

    protected $table = 'reminder';

    public $guarded = [];

    // protected $fillable = [
    //    'customer_id',
    //    'next_reminder_date',
    //    'before_service_note',
    //    'after_service_note',
    //    'reminder_status',
    //    'service_type',
    //    'amount',
    //    'service_completed_date',
    //    'product_unique_id',
    //    'product_name',
    //    'assigned_to',
    //    'assigned_by ',
    //    'created_by',
    //    'updated_by',
    //    'created_at',
    //    'updated_at',
    //    'is_active',
    //    'is_deleted'
    // ];
}
