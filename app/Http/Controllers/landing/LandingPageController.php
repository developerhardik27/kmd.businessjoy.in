<?php

namespace App\Http\Controllers\landing;

use App\Http\Controllers\Controller;
use App\Mail\LandingPageMail;
use App\Mail\ThankYouMail;
use App\Models\tbllead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class LandingPageController extends Controller
{
    public function new(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'contact_no' => 'required'
    
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', 'failed');
        } else {
              
           $dbname = 'business_joy_Oceanmnc_fay';

            config(['database.connections.dynamic_connection.database' => $dbname]);

            // Establish connection to the dynamic database
            DB::purge('dynamic_connection');
            DB::reconnect('dynamic_connection');


         
            $lead = DB::connection('dynamic_connection')->table('tbllead')->insert([
                'first_name' => $request->name,
                'email' => $request->email,
                'contact_no' => $request->contact_no,
                'title' => $request->subject,
                'msg_from_lead' => $request->msg,
                'audience_type' => 'cool',
                'source' => 'landing page'
            ]);

            if ($lead) {


                $user = 'oceanmnc1512@gmail.com';
                Mail::to($request->email)->bcc('parthdeveloper9@gmail.com')->send(new ThankYouMail($request));
                Mail::to($user)->bcc('parthdeveloper9@gmail.com')->send(new LandingPageMail($request));
            
                return redirect()->back()->with('success', 'Your inquiry succesfully Submited');
               
            } else {
                return redirect()->back()->with('error', '!Please try Again Sometime Later');
            }

           
        }
    }
}
