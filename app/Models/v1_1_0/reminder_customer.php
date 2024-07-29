<?php

namespace App\Models\v1_1_0;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class reminder_customer extends Model
{
    use HasFactory;

    protected $connection = 'dynamic_connection';

    protected $table = 'reminder_customer';

    protected $fillable = [
       'name',
       'email',
       'contact_no',
       'address',
       'area',
       'country_id',
       'state_id',
       'city_id',
       'pincode',
       'invoice_id',
       'customer_type',
       'created_by',
       'updated_by',
       'created_at',
       'updated_at',
       'is_active',
       'is_deleted'
    ];
}
