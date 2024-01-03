<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class invoice extends Model
{
    use HasFactory;

    protected $invoice_table = 'invoices';
    protected $invoice_details = 'invoice_details';

    protected $invoice_table_fillable = [
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
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
        'is_active',
        'is_deleted'
    ];

    protected $invoice_details_fillable = [
        'invoice_id',
        'product_id',
        'product_name',
        'quantity',
        'item_description',
        'price',
        'total_amount',
        'currency_id',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
        'is_active',
        'is_deleted'
    ];
}
