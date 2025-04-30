<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\LandingPageActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class otherapiController extends Controller
{

    public function oceanlead(Request $request)
    {

        $domainName = $request->getHost();

        // if($domainName != 'oceanmnc.com'){
        //     return response()->json([
        //         'status' => 403,
        //         'message' => 'Unauthorized'
        //     ]);
        // }
        $dbname = config('app.main_db');

        config(['database.connections.dynamic_connection.database' => $dbname]);

        // Establish connection to the dynamic database
        DB::purge('dynamic_connection');
        DB::reconnect('dynamic_connection');



        if(isset($request->email)){ 
            $checkrecord = DB::connection('dynamic_connection')->table('tbllead')
                ->where('email', $request->email)->get();
               
        }else{
            $checkrecord = DB::connection('dynamic_connection')->table('tbllead')
            ->where('contact_no', $request->contact_no)->get();
        }

        
        if ($checkrecord->count() > 0) {
            return response()->json([
                'status' => 500,
                'message' => 'You are already in'
            ], 500);
        }
       
        function splitName($fullName) {
            // Find the position of the first space
            $spacePosition = strpos($fullName, ' ');
        
            // If there's no space, the whole name is considered the first name
            if ($spacePosition === false) {
                return array('first_name' => $fullName, 'last_name' => '');
            }
        
            // Split the name into first name and last name
            $first_name = substr($fullName, 0, $spacePosition);
            $last_name = substr($fullName, $spacePosition + 1);
        
            return array('first_name' => $first_name, 'last_name' => $last_name);
        }
        
     
        $name = splitName($request->name);

        $lead = DB::connection('dynamic_connection')->table('tbllead')->insert([
            'first_name' => $name['first_name'],
            'last_name' => $name['last_name'],
            'email' => $request->email,
            'contact_no' => $request->contact_no,
            'title' => $request->title,
            'budget' => $request->budget,
            'source' => $request->source,
            'upload' => $request->document,
            'audience_type' => 'cool',
            'attempt_lead' => 0
        ]);

        if ($lead) {
            return response()->json([
                'status' => 200,
                'message' => 'lead succesfully created'
            ], 200);
        } else {
            return response()->json([
                'status' => 500,
                'message' => 'lead not succesfully create'
            ], 500);
        }
    }

    public function store(Request $request)
    {

        $data = $request->validate([
            'activity_name' => 'nullable|string',
            'activity_text' => 'nullable|string',
            'start_time' => 'nullable|date',
            'end_time' => 'nullable|date',
            'ip_address' => 'nullable|string',
            'country' => 'nullable|string',
            'landing_page_name' => 'nullable|string',
            'landing_page_url' => 'nullable|string',
            'duration' => 'nullable',
        ]);

        $timezone = 'Asia/Kolkata';

        // Parse and convert times to IST
        $startTime = Carbon::parse($data['start_time'])->setTimezone($timezone);
        $endTime = Carbon::parse($data['end_time'])->setTimezone($timezone);
        $createdTime = Carbon::now()->setTimezone($timezone);

        // Calculate and format duration as h:i:s if not provided
        if (isset($data['duration'])) {
            $duration = gmdate('H:i:s', $data['duration']); // Format duration as h:i:s
        } else {
            $durationSeconds = $endTime->diffInSeconds($startTime);
            $duration = gmdate('H:i:s', $durationSeconds); // Format duration as h:i:s
        }

        // Prepare data for insertion
        $activityData = [
            'activity_name' => $data['activity_name'],
            'activity_text' => $data['activity_text'],
            'start_time' => $startTime,
            'end_time' => $endTime,
            'created_at' => $createdTime,
            'ip_address' => $data['ip_address'],
            'country' => $data['country'],
            'landing_page_name' => $data['landing_page_name'],
            'landing_page_url' => $data['landing_page_url'],
            'duration' => $duration
        ];

        LandingPageActivity::create($activityData);

        return response()->json(['message' => 'New record created successfully'], 201);
    }


    public function fblead(Request $request)
    {

        $dbname = config('app.main_db');;

        config(['database.connections.dynamic_connection.database' => $dbname]);

        // Establish connection to the dynamic database
        DB::purge('dynamic_connection');
        DB::reconnect('dynamic_connection');


        $lead = DB::connection('dynamic_connection')->table('fblead')->insert([
            'name' => $request->name,
            'email' => $request->email,
            'contact_no' => $request->contact_no
        ]);

        if ($lead) {
            return response()->json([
                'status' => 200,
                'message' => 'lead succesfully created'
            ], 200);
        } else {
            return response()->json([
                'status' => 500,
                'message' => 'lead not succesfully created'
            ], 500);
        }
    }

}
