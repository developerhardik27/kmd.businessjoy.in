<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AmazonConnection extends Model
{
    use HasFactory;

    protected $table = 'amazon_connections';

    public $guarded = [];
}
