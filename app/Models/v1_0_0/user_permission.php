<?php

namespace App\Models\v1_0_0;

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
        "rp"
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
