<?php

namespace App\Models\v4_3_2;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class broker_bill_payment_detail extends Model
{
    use HasFactory;
     protected $connection = 'dynamic_connection';

    protected $table = 'broker_bill_payment_details';

    public $guarded = [];
}
