<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class company_detail extends Model
{
    use HasFactory;

    protected $table = 'company_details';

    protected $fillable = [
       'name',
       'email',
       'contact_no',
       'address',
       'gst_no',
       'country_id',
       'state_id',
       'city_id',
       'pincode',
       'img'
    ];
}
