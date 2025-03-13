<?php

namespace App\Models\v3_0_0;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class user_permission extends Model
{
    use HasFactory;
 
    protected $connection = 'dynamic_connection';
    protected $table = 'user_permissions';

    public $guarded = [];

    // protected $fillable = [
    //     "user_id",
    //     "rp",
    //     "created_by",
    // ];
 
}
