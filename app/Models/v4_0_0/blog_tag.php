<?php

namespace App\Models\v4_0_0;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class blog_tag extends Model
{
    use HasFactory;
    protected $connection = 'dynamic_connection';

    protected $table = 'blog_tags';

    public $guarded = [];
}
