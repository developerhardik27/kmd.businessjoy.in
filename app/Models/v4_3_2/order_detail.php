<?php

namespace App\Models\v4_3_2;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class order_detail extends Model
{
    use HasFactory;
    protected $connection = 'dynamic_connection';

    protected $table = 'order_details';

    public $guarded = [];
    protected $fillable = [
        'order_id',
        'garden_id',
        'invoice_no',
        'grade',
        'bags',
        'kg',
        'net_kg',
        'rate',
        'amount'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
