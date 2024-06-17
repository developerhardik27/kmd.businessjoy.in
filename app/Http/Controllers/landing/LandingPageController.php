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


        if (isset($request->subscribe)) {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
            ]);
        } else {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'required|email',
                'contact_no' => 'required'

            ]);
        }


        if ($validator->fails()) {
            return redirect()->back()->with('error', 'Please enter valid details');
        } else {

            $host = $_SERVER['HTTP_HOST'];

            if ($host === 'localhost:8000') {
                // If the host is localhost
                $dbname = 'bj_shree_vinayak_battery_zone_k9r';
            } elseif ($host === 'staging.businessjoy.in') {
                // If the host is staging.businessjoy.in
                $dbname = 'staging_business_joy_parth_fy6';
            } else {
                // For any other host, provide a default
                $dbname = 'business_joy_oceanmnc_fay';
            }



            config(['database.connections.dynamic_connection.database' => $dbname]);

            // Establish connection to the dynamic database
            DB::purge('dynamic_connection');
            DB::reconnect('dynamic_connection');



            if (isset($request->subscribe)) {

                $checkoldrec = DB::connection('dynamic_connection')->table('tbllead')
                    ->where('email', $request->email)
                    ->where('source', 'Business Joy Website Page')
                    ->get();


                if (count($checkoldrec) > 1) {

                    return redirect()->back()->with('error', 'Your are already subscribed!');
                }


                $data = [
                    'email' => $request->email,
                    'audience_type' => 'cool',
                    'source' => 'Business Joy Website Page'
                ];
            } else {

                $data = [
                    'first_name' => $request->name,
                    'email' => $request->email,
                    'contact_no' => $request->contact_no,
                    'title' => $request->subject,
                    'msg_from_lead' => $request->msg,
                    'audience_type' => 'cool',
                    'source' => 'landing page'
                ];
            }


            $lead = DB::connection('dynamic_connection')->table('tbllead')->insert($data);

            if ($lead) {


                $user = 'oceanmnc1512@gmail.com';
                Mail::to($request->email)->bcc('parthdeveloper9@gmail.com')->send(new ThankYouMail($request));
                Mail::to($user)->bcc('parthdeveloper9@gmail.com')->send(new LandingPageMail($request));

                return redirect()->back()->with('success', 'Your Request succesfully Submited');

            } else {
                return redirect()->back()->with('error', 'Something Went Wrong!Please try Again Sometime Later');
            }


        }
    }
}
