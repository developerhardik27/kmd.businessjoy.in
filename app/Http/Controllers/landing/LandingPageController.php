<?php

namespace App\Http\Controllers\landing;

use GuzzleHttp\Client;
use App\Models\tbllead;
use App\Mail\ThankYouMail;
use App\Models\bj_partner;
use Illuminate\Http\Request;
use App\Mail\LandingPageMail;
use App\Mail\becomePartnerMail;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class LandingPageController extends Controller
{
    public function new(Request $request)
    {

        if (isset($request->subscribe)) {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|max:50',
            ]);
        } else {
            $validator = Validator::make($request->all(), [
                'name' => 'required|alpha|max:30',
                'email' => 'required|email|max:50',
                'subject' => 'nullable|alpha|max:25',
                'mobile_number' => 'required|digits_between:10,12',
                'message' => 'required|max:200'
            ]);
        }


        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->messages()
            ], 422);
        } else {


            // Get the reCAPTCHA response token
            $recaptchaResponse = $request->input('g-recaptcha-response');

            if (empty($recaptchaResponse)) {
                return response()->json([
                    'status' => 500,
                    'message' => 'reCAPTCHA response is missing.',
                ], 500);
            }

            $secretKey = env('RECAPTCHA_SECRET_KEY'); // Get the secret key from .env file

            // Send a request to Google's reCAPTCHA API to verify the token
            $client = new Client();
            $response = $client->post('https://www.google.com/recaptcha/api/siteverify', [
                'form_params' => [
                    'secret' => $secretKey,
                    'response' => $recaptchaResponse,
                ],
            ]);

            $data = json_decode($response->getBody()->getContents());

            if ($data->success && $data->score >= 0.7) {

                $host = $_SERVER['HTTP_HOST'];

                if ($host === 'localhost:8000') {  // If the host is localhost
                    $dbname = 'bj_shree_vinayak_battery_zone_k9r';
                } elseif ($host === 'staging.businessjoy.in') {  // If the host is staging.businessjoy.in
                    $dbname = 'staging_business_joy_parth_fy6';
                } else {  // For any other host, provide a default
                    $dbname = 'business_joy_Oceanmnc_pev';
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
                        return response()->json([
                            'status' => 500,
                            'message' => 'You are already subscribed!.',
                        ], 500);
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
                        'contact_no' => $request->mobile_number,
                        'title' => $request->subject,
                        'msg_from_lead' => $request->message,
                        'audience_type' => 'cool',
                        'source' => 'landing page'
                    ];
                }


                $lead = DB::connection('dynamic_connection')->table('tbllead')->insert($data);

                if ($lead) {

                
                    Mail::to($request->email)->send(new ThankYouMail($request));
                    Mail::to(config('app.bcc_mail_id'))->send(new LandingPageMail($request));

                    return response()->json([
                        'status' => 200,
                        'message' => 'Your request successfully submitted.',
                    ], 200);

                } else {
                    return response()->json([
                        'status' => 500,
                        'message' => 'Something went wrong! please try again some time later.',
                    ], 500);
                }

            }

            // If verification fails, send an error response
            return response()->json([
                'status' => 500,
                'message' => 'reCAPTCHA verification failed. Please try again.',
            ]);


        }
    }

    public function storeNewPartner(Request $request)
    {
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

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->messages()
            ], 422);
        }



        // Get the reCAPTCHA response token
        $recaptchaResponse = $request->input('g-recaptcha-response');

        if (empty($recaptchaResponse)) {
            return response()->json([
                'status' => 500,
                'message' => 'reCAPTCHA response is missing.',
            ], 500);
        }

        $secretKey = env('RECAPTCHA_SECRET_KEY'); // Get the secret key from .env file

        // Send a request to Google's reCAPTCHA API to verify the token
        $client = new Client();
        $response = $client->post('https://www.google.com/recaptcha/api/siteverify', [
            'form_params' => [
                'secret' => $secretKey,
                'response' => $recaptchaResponse,
            ],
        ]);

        $data = json_decode($response->getBody()->getContents());

        if ($data->success && $data->score >= 0.7) {


            $checkEmail = bj_partner::where('contact_person_email', $request->contact_person_email)->exists();
            $errors = [];
            if ($checkEmail) {
                $errors['contact_person_email'] = 'You have already requested for partnership. please contact to support.';
            }

            $checkMobileNumber = bj_partner::where('contact_person_mobile', $request->contact_person_mobile_number)->exists();

            if ($checkMobileNumber) {
                $errors['contact_person_mobile_number'] = 'You have already requested for partnership. please contact to support.';
            }

            if (count($errors) > 0) {
                Mail::to(config('app.bcc_mail_id'))->send(new becomePartnerMail($request));
                return response()->json([
                    'status' => 422,
                    'errors' => $errors
                ], 422);
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

            if ($partner) {
                Mail::to($request->contact_person_email)->send(new becomePartnerMail($request));
                return response()->json([
                    'status' => 200,
                    'message' => 'Thank You! Your request succesfully submitted',
                ], 200); 
            } else {
                return response()->json([
                    'status' => 500,
                    'message' => 'Your request not succesfully submit',
                ], 500);  
            }

        }

        // If verification fails, send an error response
        return response()->json([
            'status' => 500,
            'message' => 'reCAPTCHA verification failed. Please try again.',
        ]);

    }
}
