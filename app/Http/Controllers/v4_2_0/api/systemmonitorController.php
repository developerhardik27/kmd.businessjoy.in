<?php

namespace App\Http\Controllers\v4_2_0\api;

use App\Models\task_schedule_list;
use Illuminate\Http\Request;
use App\Models\page_load_log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Yajra\DataTables\Facades\DataTables;

class systemmonitorController extends commonController
{
    public $userId, $companyId, $masterdbname, $rp, $supplierModel;

    public function __construct(Request $request)
    {
        $this->dbname($request->company_id);
        $this->companyId = $request->company_id;
        $this->userId = $request->user_id;
        $this->masterdbname = DB::connection()->getDatabaseName();

        $user_rp = DB::connection('dynamic_connection')->table('user_permissions')->select('rp')->where('user_id', $this->userId)->get();
        $permissions = json_decode($user_rp, true);
        if (empty($permissions)) {
            $this->customerrorresponse();
        }
        $this->rp = json_decode($permissions[0]['rp'], true);

        $this->supplierModel = $this->getmodel('supplier');
    }


    /**
     * Summary of slowpages
     * slow pages list
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function slowpages(Request $request)
    {
        if ($this->rp['developermodule']['slowpage']['view'] != 1) {
            return response()->json([
                'status' => 500,
                'message' => 'You are Unauthorized',
                'data' => [],
                'recordsTotal' => 0,
                'recordsFiltered' => 0
            ]);
        }

        $slowPages = page_load_log::select(
            'id',
            'page_url',
            'controller',
            'method',
            'view_name',
            'load_time',
            'username',
            'db_name',
            DB::raw("DATE_FORMAT(start_time, '%d-%M-%Y %h:%i:%s %p') as start_time_formatted"),
            DB::raw("DATE_FORMAT(end_time, '%d-%M-%Y %h:%i:%s %p') as end_time_formatted"),
            DB::raw("DATE_FORMAT(date_time, '%d-%M-%Y %h:%i:%s %p') as date_time_formatted")
        )->get();


        if ($slowPages->isEmpty()) {
            return DataTables::of($slowPages)
                ->with([
                    'status' => 404,
                    'message' => 'No Data Found'
                ])
                ->make(true);
        }

        return DataTables::of($slowPages)
            ->with([
                'status' => 200,
            ])
            ->make(true);

    }

    /**
     * Remove the specified resource from storage.
     * remove slow page record 
     */
    public function slowpagedestroy(string $id)
    {
        if ($this->rp['developermodule']['slowpage']['delete'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }
        $pageRecord = page_load_log::find($id);

        if (!$pageRecord) {
            return $this->successresponse(404, 'message', 'No such record found!');
        }

        $pageRecord->update([
            'is_deleted' => 1
        ]);

        return $this->successresponse(200, 'message', 'Record succesfully deleted');
    }

    /**
     * Summary of geterrorlogfiles
     * get error log files
     * @return \Illuminate\Http\JsonResponse
     */
    public function geterrorlogfiles()
    {
        if ($this->rp['developermodule']['errorlog']['view'] != 1) {
            return DataTables::of(collect())
                ->with([
                    'status' => 403,
                    'message' => 'You are Unauthorized'
                ])->make(true);
        }

        $logDir = storage_path('logs');
        $files = File::files($logDir);

        $errorFiles = collect();

        foreach ($files as $file) {
            $errorFiles->push([
                'name' => $file->getFilename(),
                'size_kb' => round($file->getSize() / 1024, 2),
                'modified_at' => trim(date('d-M-Y H:i:s', $file->getMTime()))
            ]);

        }

        if ($errorFiles->isEmpty()) {
            return DataTables::of($errorFiles)
                ->with([
                    'status' => 404,
                    'message' => 'No Error log exists'
                ])->make(true);
        }

        return DataTables::of($errorFiles)
            ->with([
                'status' => 200,
            ])->make(true);
    }

    /**
     * Summary of downloaderrorlog
     * download error log files
     * @param mixed $filename
     * @return mixed|\Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloaderrorlog($filename)
    {
        if ($this->rp['developermodule']['errorlog']['view'] != 1) {
            return response()->json([
                'status' => 403,
                'message' => 'Unauthorized'
            ], 403);
        }

        $path = storage_path('logs/' . $filename);

        if (!File::exists($path)) {
            return response()->json([
                'status' => 404,
                'message' => 'Log file not found'
            ], 404);
        }

        return response()->download($path);
    }


    /**
     * Summary of cronjobs
     * Cron job list(schedule task)
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function cronjobs(Request $request)
    {
        if ($this->rp['developermodule']['cronjob']['view'] != 1) {
            return response()->json([
                'status' => 500,
                'message' => 'You are Unauthorized',
                'data' => [],
                'recordsTotal' => 0,
                'recordsFiltered' => 0
            ]);
        }

        $cronJobs = task_schedule_list::select(
            'id',
            'name',
            'description',
            'is_active',
            DB::raw("DATE_FORMAT(schedule, '%d-%M-%Y %h:%i:%s %p') as schedule_formatted"),
            DB::raw("DATE_FORMAT(updated_at, '%d-%M-%Y %h:%i:%s %p') as updated_at_formatted")
        )->get();


        if ($cronJobs->isEmpty()) {
            return DataTables::of($cronJobs)
                ->with([
                    'status' => 404,
                    'message' => 'No Data Found'
                ])
                ->make(true);
        }

        return DataTables::of($cronJobs)
            ->with([
                'status' => 200,
            ])
            ->make(true);

    }
 

}
