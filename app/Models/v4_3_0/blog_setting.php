<?php

namespace App\Models\v4_3_0;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class blog_setting extends Model
{
    use HasFactory;
    protected $connection = 'dynamic_connection';

    protected $table = 'blog_settings';

    public $guarded = [];
}
