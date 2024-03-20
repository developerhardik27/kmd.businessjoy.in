<?php

namespace App\Models\v1_0_0;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class customer extends Model
{
    use HasFactory;
    
    protected $connection = 'dynamic_connection';

    protected $table = 'customers';

    protected $fillable = [
       'firstname',
       'lastname',
       'company_name',
       'email',
       'contact_no',
       'address',
       'country_id',
       'state_id',
       'city_id',
       'pincode',
       'gst_no',
       'company_id',
       'created_by',
       'updated_by',
       'created_at',
       'updated_at',
       'is_active',
       'is_deleted'
    ];
}
