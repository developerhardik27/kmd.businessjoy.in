<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable ;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;


class User extends Authenticatable
{
    use  HasApiTokens , HasFactory, Notifiable;

  
    protected $table = 'users';

    protected $fillable = [
       'firstname',
       'lastname',
       'email',
       'password',
       'contact_no',
       'country_id',
       'state_id',
       'city_id',
       'pincode',
       'img',
       'api_token',
       'company_id',
       'created_by',
       'updated_by',
       'created_at',
       'updated_at',
       'is_active',
       'is_deleted'
    ];



}
