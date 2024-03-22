<?php

namespace App\Http\Controllers\landing;

use App\Http\Controllers\Controller;
use App\Mail\LandingPageMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class LandingPageController extends Controller
{
    public function new(Request $request)
    {

        $validator = Validator::make($request->all(), [

            'name' => 'required|string|max:50',
            'email' => 'required|email',
            'contact_no' => 'required',
        
        ]);
 

        if ($validator->fails()) {
            return redirect()->back()->with('error', 'sorry ! you are not registered '); 
        } else {
            $user = '';
            Mail::to($user)->cc('parthdeveloper9@gmail.com')->send(new LandingPageMail($request));
            return redirect()->back()->with('success', 'plz check your mailbox and reset your password');  
         }
            
    }
}
