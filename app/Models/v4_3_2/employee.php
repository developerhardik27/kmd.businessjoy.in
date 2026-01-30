<?php

namespace App\Models\v4_3_2;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class employee extends Model
{
    use HasFactory;
    protected $connection = 'dynamic_connection';
    protected $table = 'employees';

    protected $fillable = [
        'first_name',
        'middle_name',
        'surname',
        'email',
        'mobile',
        'address',
        'bank_details',
        'cv_resume',
        'id_proofs',
        'address_proofs',
        'other_attachments',
        'created_by',
        'updated_by',
        'is_active',
        'is_deleted',
    ];
    protected $casts = [
        'id_proofs' => 'array',
        'address_proofs' => 'array',
        'other_attachments' => 'array',
    ];
}
