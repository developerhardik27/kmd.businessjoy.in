<?php

namespace App\Models\v3_0_0;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $connection = 'dynamic_connection';
    
    protected $table = 'purchases';

    public $guarded = [];

    // protected $fillable = [
    //     'name',
    //     'description',
    //     'amount',
    //     'amount_type',
    //     'date',
    //     'img',
    //     'company_id',
    //     'created_by',
    //     'updated_by',
    //     'created_at',
    //     'updated_at',
    //     'is_active',
    //     'is_deleted'
    // ];
}
