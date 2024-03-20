<?php

namespace App\Models\v1_0_0;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class invoice extends Model
{
    use HasFactory;
    
    protected $connection = 'dynamic_connection';

    protected $table = 'invoices';

    protected $fillable = [
        'inv_no',
        'inv_date',
        'customer_id',
        'notes',
        'total',
        'gst',
        'grand_total',
        'currency_id',
        'payment_type',
        'status',
        'account_id',
        'template_version',
        'company_id',
        'company_details_id',
        'show_col',
        'overdue_date',
        't_and_c_id',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
        'is_active',
        'is_deleted'
    ];

}
