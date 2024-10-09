<?php

namespace App\Models\v1_2_1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class bank_detail extends Model
{
    use HasFactory;
    
    protected $connection = 'dynamic_connection';

    protected $table = 'bank_details';


    protected $fillable = [
        'holder_name',
        'account_no',
        'swift_code',
        'ifsc_code',
        'branch_name',
        'bank_name',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
        'is_active',
        'is_deleted'
     ];

}
