<?php

namespace App\Models\v4_3_2;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class invoice_terms_and_condition extends Model
{
    use HasFactory;

    protected $connection = 'dynamic_connection';

    protected $table = 'invoice_terms_and_conditions';

    public $guarded = [];
    // protected $fillable = [ 
    //     't_and_c',
    //     'created_by',
    //     'updatd_by',
    //     'created_at',
    //     'updated_at',
    //     'is_active',
    //     'is_deleted'
    // ];
}
