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
        'house_no_building_name',
        'road_name_area_colony',
        'gst_no',
        'country_id',
        'state_id',
        'city_id',
        'pincode',
        'img',
        'pr_sign_img',
        'created_at',
        'updated_at'
    ];
}
