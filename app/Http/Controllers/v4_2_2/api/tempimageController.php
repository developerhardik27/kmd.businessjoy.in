<?php

namespace App\Http\Controllers\v4_2_2\api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;

class tempimageController extends commonController
{
    public $userId, $companyId, $masterdbname, $rp, $temp_imageModel;

    public function __construct(Request $request)
    {
        $this->companyId = $request->company_id ?? session('company_id');
        $this->userId = $request->user_id ?? session('user_id');
        
        $this->dbname($this->companyId);
        // **** for checking user has permission to action on all data 
        $user_rp = DB::connection('dynamic_connection')->table('user_permissions')->select('rp')->where('user_id', $this->userId)->value('rp');

        if (empty($user_rp)) {
            $this->customerrorresponse();
        }

        $this->rp = json_decode($user_rp, true);

        $this->masterdbname = DB::connection()->getDatabaseName();
        $this->temp_imageModel = $this->getmodel('temp_image');

    } 

    public function store(Request $request)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:jpeg,png,jpg,gif,mp4,mov,avi,pdf|max:5000', // Max size of 5MB
        ]);

        if ($validator->fails()) {
            return $this->errorresponse(422, $validator->messages());
        }

        // Check if the request has a file
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $originalFilename = $file->getClientOriginalName(); // Store original filename
            $filename = time() . '.' . $file->getClientOriginalExtension(); // Generated filename

            // Set the directory path for the temp images
            $directory = public_path('uploads/temp');

            // Check if the directory exists, if not, create it
            if (!File::exists($directory)) {
                File::makeDirectory($directory, 0775, true); // Create the directory with appropriate permissions
            }

            // Store the file in the 'uploads/temp' directory
            $file->move($directory, $filename);

            // Store both the original filename and the generated filename in the database
            $storefile = $this->temp_imageModel::create([
                'original_name' => $originalFilename, // Store the original filename
                'media_name' => $filename, // Store the generated filename
            ]);

            // Construct the file URL for the front-end
            $fileUrl = url('uploads/temp/' . $filename);

            return response()->json([
                'status' => 200,
                'message' => 'File successfully uploaded',
                'filename' => $originalFilename,
                'fileUrl' => $fileUrl

            ]);
            // Return response
            // return $this->successresponse(200, 'message', 'File successfully uploaded', 'filename', $originalFilename);
        }

        return $this->successresponse(500, 'message', 'File not successfully uploaded');
    } 

    public function deleteFile(Request $request)
    {
        // Validate the filename input
        $validator = Validator::make($request->all(), [
            'filename' => 'required|string', // This is the original filename from Dropzone
        ]);

        if ($validator->fails()) {
            return $this->errorresponse(422, $validator->messages());
        }

        // Find the file in the database using the original filename
        $file = $this->temp_imageModel::where('original_name', $request->input('filename'))->first();

        if ($file) {
            $filePath = public_path('uploads/temp/' . $file->media_name); // Get the path to the generated file

            // Check if the file exists and delete it
            if (File::exists($filePath)) {
                File::delete($filePath);  // Delete the file
                // Optionally, delete the record from the database
                $file->delete();

                return $this->successresponse(200, 'message', 'File successfully deleted');
            }

            return $this->errorresponse(404, 'message', 'File not found on the server');
        }

        return $this->errorresponse(404, 'message', 'File record not found');
    }
 
}
