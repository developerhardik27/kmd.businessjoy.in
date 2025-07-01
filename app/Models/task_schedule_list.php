<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class task_schedule_list extends Model
{
    use HasFactory;

    protected $table = 'task_schedule_list';

    public $guarded = [] ;
}
