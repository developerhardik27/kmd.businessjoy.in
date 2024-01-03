<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class payment_details extends Model
{
    use HasFactory;
    protected $table = 'payment_details';

    protected $fillable = [
        'inv_id',
        'transaction_id',
        'datetime',
        'paid_by',
        'paid_type',
        'created_at',
        'updated_at'
    ];
}
