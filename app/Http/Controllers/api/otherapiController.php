<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\LandingPageActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class otherapiController extends Controller
{


    /**
     * Summary of splitName
     * helper function
     * @param mixed $fullName
     * @return array{first_name: mixed, last_name: string|array{first_name: string, last_name: string}}
     */
    function splitName($fullName)
    {
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

    /**
     * Summary of newlead
     * add new lead
     * @param \Illuminate\Http\Request $request
     */
    public function newlead(Request $request)
    {
        $leaddata = app('App\Http\Controllers\\' . $request->app_version . '\api\tblleadController')->store($request);
        $leaddata = $leaddata->getContent();

        $leaddata = json_decode($leaddata);
        return $leaddata;
    }

    /**
     * Summary of oceanlead
     * ocean newsletter
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function oceanlead(Request $request)
    {
        // $checkrecord = DB::connection('dynamic_connection')->table('tbllead')
        //     ->where(function ($query) use ($request) {
        //         if (!empty($request->email)) {
        //             $query->orWhere('email', $request->email);
        //         }
        //         if (!empty($request->contact_no)) {
        //             $query->orWhere('contact_no', $request->contact_no);
        //         }
        //     })->where('is_deleted', 0)->exists();


        // if ($checkrecord) {
        //     return response()->json([
        //         'status' => 500,
        //         'message' => 'You are already in'
        //     ], 200);
        // }


        $name = $this->splitName($request->name);

        $attachments = [];

        // ✅ Detect both single and multiple file uploads
        if ($request->hasFile('attachments')) {
            $files = $request->file('attachments');

            // If it's a single file, wrap it into an array for consistency
            if (!is_array($files)) {
                $files = [$files];
            }

            $timestamp = now()->format('dmY');

            foreach ($files as $file) {
                $filename = $request->name . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
                $dir = public_path('uploads/' . $request->company_id . '/lead/attachments/' . $timestamp);

                if (!file_exists($dir)) {
                    mkdir($dir, 0755, true);
                }

                $file->move($dir, $filename);
                $attachments[] = $request->company_id . '/lead/attachments/' . $timestamp . '/' . $filename;
            }
        }


        $attachments = json_encode($attachments); // Store as JSON or any format you prefer


        $lead = DB::connection('dynamic_connection')->table('tbllead')->insert([
            'first_name' => $name['first_name'],
            'last_name' => $name['last_name'],
            'email' => $request->email,
            'contact_no' => $request->contact_no,
            'title' => $request->title,
            'budget' => $request->budget,
            'source' => $request->source,
            'msg_from_lead' => $request->notes,
            'notes' => $request->message,
            'web_url' => $request->web_url,
            'audience_type' => 'cool',
            'attempt_lead' => 0,
            'attachment' => $attachments,
            'created_by' => $request->user_id
        ]);

        if ($lead) {
            return response()->json([
                'status' => 200,
                'message' => 'lead succesfully created',
                'email' => $request->email,
                'contact_no' => $request->contact_no
            ], 200);
        } else {
            return response()->json([
                'status' => 500,
                'message' => 'lead not succesfully create'
            ], 200);
        }
    }

    /**
     * Summary of blog
     * @param \Illuminate\Http\Request $request
     */
    public function blog(Request $request)
    {

        $blogdata = app('App\Http\Controllers\\' . $request->app_version . '\api\blogController')->index($request);

        $blogdata = $blogdata->getContent();

        $blogdata = json_decode($blogdata);

        return $blogdata;
    }

    public function blogdetails(Request $request, $slug = null)
    {
        $blogdetails = app('App\Http\Controllers\\' . $request->app_version . '\api\blogController')->show($request, $slug);

        $blogdetails = $blogdetails->getContent();

        $blogdetails = json_decode($blogdetails);

        return $blogdetails;
    }

    /**
     * Summary of store
     * oceanmnc visitor activity store
     * its not require server key and company uuid
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
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
            $duration = intval($data['duration']);
            $duration = gmdate('H:i:s', $duration); // Format duration as h:i:s
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
            'ip_address' => isset($data['ip_address']) ? $data['ip_address'] : '',
            'country' => $data['country'],
            'landing_page_name' => $data['landing_page_name'],
            'landing_page_url' => $data['landing_page_url'],
            'duration' => $duration
        ];

        LandingPageActivity::create($activityData);

        return response()->json(['message' => 'New record created successfully'], 200);
    }
}
