<?php

namespace App\Models\v1_2_1;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class user_permission extends Model
{
    use HasFactory;
 
    protected $connection = 'dynamic_connection';
    protected $table = 'user_permissions';

    protected $fillable = [
        "user_id",
        "rp",
        "created_by",
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
