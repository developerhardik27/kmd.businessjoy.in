<?php

namespace App\Models\v1_2_1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class invoice_terms_and_condition extends Model
{
    use HasFactory;

    protected $connection = 'dynamic_connection';

    protected $table = 'invoice_terms_and_conditions';

    protected $fillable = [
        
        't_and_c',
        'created_by',
        'updatd_by',
        'created_at',
        'updated_at',
        'is_active',
        'is_deleted'
    ];
}
