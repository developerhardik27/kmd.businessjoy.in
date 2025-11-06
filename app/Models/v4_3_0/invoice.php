<?php

namespace App\Models\v4_3_0;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class invoice extends Model
{
    use HasFactory;
    
    protected $connection = 'dynamic_connection';

    protected $table = 'invoices';

    public $guarded = [];
    // protected $fillable = [
    //     'inv_no',
    //     'inv_date',
    //     'customer_id',
    //     'notes',
    //     'total',
    //     'sgst',
    //     'cgst',
    //     'gst',
    //     'grand_total',
    //     'currency_id',
    //     'payment_type',
    //     'status',
    //     'account_id',
    //     'template_version',
    //     'company_id',
    //     'company_details_id',
    //     'show_col',
    //     'gstsettings',
    //     'inv_number_type',
    //     'overdue_date',
    //     't_and_c_id',
    //     'last_increment_number',
    //     'increment_type',
    //     'pattern_type',
    //     'created_by',
    //     'updated_by',
    //     'created_at',
    //     'updated_at',
    //     'is_active',
    //     'is_deleted',
    //     'is_editable',
    // ];

}
