<?php

namespace App\Models\v4_3_1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class customer extends Model
{
    use HasFactory;
    
    protected $connection = 'dynamic_connection';

    protected $table = 'customers';

    public $guarded = [];
    
    // protected $fillable = [
    //    'firstname',
    //    'lastname',
    //    'company_name',
    //    'email',
    //    'contact_no',
    //    'house_no_building_name', 
    //    'road_name_area_colony', 
    //    'country_id',
    //    'state_id',
    //    'city_id',
    //    'pincode',
    //    'gst_no',
    //    'company_id',
    //    'created_by',
    //    'updated_by',
    //    'created_at',
    //    'updated_at',
    //    'is_active',
    //    'is_deleted'
    // ];
}
