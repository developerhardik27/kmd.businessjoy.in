<?php

namespace App\Http\Controllers\landing;

use App\Http\Controllers\Controller;
use App\Mail\becomePartnerMail;
use App\Mail\LandingPageMail;
use App\Mail\ThankYouMail;
use App\Models\bj_partner;
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

    public function storeNewPartner(Request $request){
        $validator = Validator::make($request->all(), [
            'company_name' => 'required|string',
            'company_website' => 'nullable|string',
            'company_address' => 'nullable|string',
            'company_area' => 'nullable|alpha',
            'company_pincode' => 'nullable|numeric',
            'company_city' => 'nullable|alpha',
            'company_state' => 'nullable|alpha',
            'company_country' => 'nullable|alpha',
            'company_tax_identification_number' => 'nullable|string',
            'contact_person_name' => 'required|alpha',
            'contact_person_email' => 'required|email',
            'contact_person_mobile_number' => 'required|digits_between:10,15',
        ]);

        if($validator->fails()){
            return redirect()->back()->withInput()->withErrors($validator);
        }


        $checkEmail = bj_partner::where('contact_person_email',$request->contact_person_email)->exists();
        $errors = [];
        if($checkEmail){
            $errors['contact_person_email'] = 'You have already requested for partnership. please contact to support.' ;
        }
        
        $checkMobileNumber = bj_partner::where('contact_person_mobile',$request->contact_person_mobile_number)->exists();

        if($checkMobileNumber){
            $errors['contact_person_mobile_number'] = 'You have already requested for partnership. please contact to support.' ;
        }

        if( count($errors) > 0){
            Mail::to(env('MAIL_FROM_ADDRESS','businessjoy.in@gmail.com'))->bcc(['parthdeveloper9@gmail.com'])->send(new becomePartnerMail($request));
            return redirect()->back()->withInput()->withErrors($errors);
        }

        $partner = bj_partner::create([
            'company_name' => $request->company_name,
            'company_website' => $request->company_website,
            'company_address' => $request->company_address,
            'company_area' => $request->company_area,
            'company_pincode' => $request->company_pincode,
            'company_city' => $request->company_city,
            'company_state' => $request->company_state,
            'company_country' => $request->company_country,
            'company_tax_id_number' => $request->company_tax_identification_number,
            'contact_person_name' => $request->contact_person_name,
            'contact_person_email' => $request->contact_person_email,
            'contact_person_mobile' => $request->contact_person_mobile_number,
        ]);

        if($partner){ 
            Mail::to($request->contact_person_email)->bcc(['jay@oceanmnc.com','parthdeveloper9@gmail.com'])->send(new becomePartnerMail($request));

            return redirect()->back()->with('success','Thank You! Your request succesfully submitted');
        }else{
            return redirect()->back()->with('error','Your request not succesfully submit');
        }

    }
}
