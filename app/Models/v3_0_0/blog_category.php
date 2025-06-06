<?php

namespace App\Models\v3_0_0;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class blog_category extends Model
{
    use HasFactory;


    protected $connection = 'dynamic_connection';

    protected $table = 'blog_categories';

    public $guarded = [];
}
