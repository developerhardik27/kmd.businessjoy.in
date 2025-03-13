<?php

namespace App\Models\v3_0_0;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tbllead extends Model
{
    use HasFactory;
    
    protected $connection = 'dynamic_connection';

    protected $table = 'tbllead';

    public $guarded = [];
    
    // protected $fillable = [
    //     'first_name',
    //     'last_name',
    //     'email',
    //     'contact_no',
    //     'title',
    //     'budget',
    //     'company',
    //     'audience_type',
    //     'customer_type',
    //     'status',
    //     'attempt_lead',
    //     'last_follow_up',
    //     'next_follow_up',
    //     'number_of_follow_up',
    //     'notes',
    //     'lead_stage',
    //     'web_url',
    //     'assigned_to',
    //     'assigned_by',
    //     'created_by',
    //     'updated_by',
    //     'created_at',
    //     'updated_at',
    //     'is_active',
    //     'is_deleted',
    //     'source',
    //     'ip',
    //     'upload'
    // ];
}
