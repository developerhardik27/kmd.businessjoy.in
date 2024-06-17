<?php

namespace App\Models\v1_0_0;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class payment_details extends Model
{
    use HasFactory;
   
    protected $connection = 'dynamic_connection';

    protected $table = 'payment_details';

    protected $fillable = [
        'inv_id',
        'receipt_number',
        'transaction_id',
        'datetime',
        'paid_by',
        'paid_type',
        'amount',
        'paid_amount',
        'pending_amount',
        'part_payment',
        'created_at',
        'updated_at'
    ];
}
