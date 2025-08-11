<?php

namespace App\Models\v4_3_0;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class role_permission extends Model
{
    use HasFactory;

    protected $connection = 'dynamic_connection';

    protected $table = 'user_role_permissions';

    protected $guarded = [];
}
