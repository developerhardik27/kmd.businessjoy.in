<?php

namespace App\Models\v2_0_0;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class customer_support extends Model
{
    use HasFactory;
    protected $connection = 'dynamic_connection';
    protected $table = 'customer_support';

    protected $fillable = [
        'name',
        'email',
        'contact_no',
        'title',
        'budget',
        'audience_type',
        'customer_type',
        'status',
        'last_call',
        'next_call',
        'number_of_call',
        'notes',
        'ticket',
        'assigned_to',
        'assigned_by',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
        'is_active',
        'is_deleted',
        'source',
        'ip'
    ];
}
