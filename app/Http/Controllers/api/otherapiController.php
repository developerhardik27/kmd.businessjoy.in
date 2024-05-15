<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class otherapiController extends Controller
{

    public function oceanlead(Request $request)
    {

        $domainName = $request->getHost();
        if($domainName != 'oceanmnc.com'){
            return response()->json([
                'status' => 403,
                'message' => 'Unauthorized'
            ]);
        }
        $dbname = 'business_joy_Oceanmnc_fay';

        config(['database.connections.dynamic_connection.database' => $dbname]);

        // Establish connection to the dynamic database
        DB::purge('dynamic_connection');
        DB::reconnect('dynamic_connection');



        $checkrecord = DB::connection('dynamic_connection')->table('tbllead')
            ->where('email', $request->email)->get();
           
        
        if ($checkrecord->count() > 0) {
            return response()->json([
                'status' => 500,
                'message' => 'lead already exist'
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

}
