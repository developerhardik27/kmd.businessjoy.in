<?php

namespace App\Models\v3_0_0;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class customersupporthistory extends Model
{
    use HasFactory;
    protected $connection = 'dynamic_connection';
    protected $table = 'customersupporthistory';

    public $guarded = [];
    
    // protected $fillable = [
    //     'call_date',
    //     'history_notes',
    //     'call_status',
    //     'csid',
    //     'companyid',
    //     'created_at',
    //     'updated_at',
    //     'created_by',
    //     'updated_by',
    //     'is_active',
    //     'is_deleted',
    // ];
}
