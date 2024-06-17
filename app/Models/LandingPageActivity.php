<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LandingPageActivity extends Model
{
    use HasFactory;

    protected $table = 'tbl_landingpage_activity';

    protected $guarded = [''];

    public $timestamps = false; // Disable automatic timestamps
}
