<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Mail\sendmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class mailcontroller extends Controller
{
    public function sendmail(){
        $username = 'sendmail';
        $password = '1234';
        Mail::to('parthdeveloper9@gmail.com')->send(new sendmail($username,$password,'parthdeveloper9@gmail.com'));
        return 'Email sent succesfully';
    }
}
