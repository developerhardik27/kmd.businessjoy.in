<?php

namespace App\Http\Controllers\landing;

use App\Http\Controllers\Controller;
use App\Mail\LandingPageMail;
use App\Mail\ThankYouMail;
use App\Models\tbllead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class LandingPageController extends Controller
{
    public function new(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required',
            'contact_no' => 'required'

        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', 'failed');
        } else {
              
           
            $lead = tbllead::create([
                'name' => $request->name,
                'email' => $request->email,
                'contact_no' => $request->contact_no,
                'title' => $request->subject,
                'message' => $request->msg,
                'audience_type' => 'cool',
                'source' => 'Manually'
            ]);

            if ($lead) {


                $user = 'inquiry@businessjoy.com';
                Mail::to($request->email)->cc('parthdeveloper9@gmail.com')->send(new ThankYouMail($request));
                Mail::to($user)->cc('parthdeveloper9@gmail.com')->send(new LandingPageMail($request));
            
                return redirect()->back()->with('success', 'Your inquiry succesfully Submited');
               
            } else {
                return redirect()->back()->with('error', '!Please try Again Sometime Later');
            }

           
        }
    }
}
