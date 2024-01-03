<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tbllead extends Model
{
    use HasFactory;

    protected $table = 'tbllead';

    protected $fillable = [
        'name',
        'email',
        'contact_no',
        'title',
        'budget',
        'audience_type',
        'customer_type',
        'status',
        'last_follow_up',
        'next_follow_up',
        'number_of_follow_up',
        'notes',
        'created_at',
        'updated_at',
        'is_active',
        'is_deleted',
        'source',
        'ip'
    ];
}
