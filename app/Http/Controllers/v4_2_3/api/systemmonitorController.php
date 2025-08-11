<?php

namespace App\Http\Controllers\v4_2_3\api;

use Illuminate\Http\Request;
use App\Models\page_load_log;
use Illuminate\Support\Carbon;
use App\Models\task_schedule_list;
use Illuminate\Support\Facades\DB;
use App\Models\activity_recent_data;
use Illuminate\Support\Facades\File;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class systemmonitorController extends commonController
{
    public $userId, $companyId, $masterdbname, $rp, $supplierModel;

    public function __construct(Request $request)
    {
        $this->companyId = $request->company_id;
        $this->userId = $request->user_id;

        $this->dbname($this->companyId);
        // **** for checking user has permission to action on all data 
        $user_rp = DB::connection('dynamic_connection')->table('user_permissions')->select('rp')->where('user_id', $this->userId)->value('rp');

        if (empty($user_rp)) {
            $this->customerrorresponse();
        }

        $this->rp = json_decode($user_rp, true);

        $this->masterdbname = DB::connection()->getDatabaseName();

        $this->supplierModel = $this->getmodel('supplier');
    }

    public function companyWiseChartData(Request $request)
    {
        if ($this->rp['developermodule']['developerdashboard']['view'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $query = page_load_log::Join($this->masterdbname . '.company', 'page_load_logs.company_name', $this->masterdbname . '.company.id')
            ->Join($this->masterdbname . '.company_details', 'company.company_details_id', $this->masterdbname . '.company_details.id');

        // 🗓 Apply date filter
        $dateFilter = $request->input('date_filter');
        $from = null;
        $to = now();

        switch ($dateFilter) {
            case '1_month':
                $from = now()->subMonth();
                break;
            case '3_months':
                $from = now()->subMonths(3);
                break;
            case '6_months':
                $from = now()->subMonths(6);
                break;
            case '1_year':
                $from = now()->subYear();
                break;
            case 'custom':
                $from = $request->input('from_date') ? Carbon::parse($request->input('from_date')) : null;
                $to = $request->input('to_date') ? Carbon::parse($request->input('to_date'))->addDay() : now();
                break;
        }

        if ($from) {
            $query->whereBetween('page_load_logs.start_time', [$from, $to]);
        }

        $slowStats = $query->select(
            'company_details.id',
            'company_details.name as company_name',
            DB::raw('ROUND(AVG(page_load_logs.load_time) / 1000, 2) as total_seconds'),
            DB::raw('COUNT(DISTINCT page_load_logs.id) as total_count')  // Count distinct log IDs
        )
            ->groupBy('company_details.id', 'company_details.name')
            ->orderByDesc(DB::raw('AVG(page_load_logs.load_time)'))
            ->get();

        return response()->json($slowStats);
    }


    public function dailySlowPagesReport(Request $request)
    {
        // Authorization check (as before)
        if ($this->rp['developermodule']['developerdashboard']['view'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }   

        $query = page_load_log::query();

        // Apply date filter
        $filter = $request->input('date_filter');
        $from = null;
        $to = now();

        switch ($filter) {
            case '1_month':
                $from = now()->subMonth();
                break;
            case '3_months':
                $from = now()->subMonths(3);
                break;
            case '6_months':
                $from = now()->subMonths(6);
                break;
            case '1_year':
                $from = now()->subYear();
                break;
            case 'custom':
                $from = $request->input('from_date')
                    ? Carbon::parse($request->input('from_date'))
                    : null;
                $to = $request->input('to_date')
                    ? Carbon::parse($request->input('to_date'))->addDay()
                    : now();
                break;
        }
        if ($from) {
            $query->whereBetween('start_time', [$from, $to]);
        }   

        // Group by date and aggregate
        $dailyStats = $query
            ->select(
                DB::raw("DATE(start_time) as date"),
                DB::raw('ROUND(AVG(load_time)/1000, 2) as avg_seconds'),
                DB::raw('COUNT(*) as total_logs')
            )
            ->groupBy(DB::raw("DATE(start_time)"))
            ->orderBy('date', 'DESC')
            ->get();

        return response()->json($dailyStats);
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

        $slowPages = page_load_log::leftJoin($this->masterdbname . '.company', 'page_load_logs.company_name', $this->masterdbname . '.company.id')
            ->leftJoin($this->masterdbname . '.company_details', 'company.company_details_id', $this->masterdbname . '.company_details.id')
            ->select(
                'page_load_logs.id',
                'page_load_logs.page_url',
                'page_load_logs.controller',
                'page_load_logs.method',
                'page_load_logs.view_name',
                'page_load_logs.load_time',
                'page_load_logs.username',
                'page_load_logs.user_email',
                'page_load_logs.db_name',
                'company_details.name as company_name',
                DB::raw("DATE_FORMAT(page_load_logs.start_time, '%d-%M-%Y %h:%i:%s %p') as start_time_formatted"),
                DB::raw("DATE_FORMAT(page_load_logs.end_time, '%d-%M-%Y %h:%i:%s %p') as end_time_formatted"),
                DB::raw("DATE_FORMAT(page_load_logs.date_time, '%d-%M-%Y %h:%i:%s %p') as date_time_formatted")
            )->distinct()->get();


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


    /**
     * Summary of recentactivitydata
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function recentactivitydata(Request $request)
    {
        if ($this->rp['developermodule']['recentactivitydata']['view'] != 1) {
            return response()->json([
                'status' => 500,
                'message' => 'You are Unauthorized',
                'data' => [],
                'recordsTotal' => 0,
                'recordsFiltered' => 0
            ]);
        }

        $recentActivityData = activity_recent_data::select(
            'id',
            'module',
            'page',
            'limit'
        )->where('is_deleted', 0);

        if ($this->rp['developermodule']['recentactivitydata']['alldata'] != 1) {
            $recentActivityData->where('created_by', $this->userId);
        }

        $recentActivityData = $recentActivityData->get();

        if ($recentActivityData->isEmpty()) {
            return DataTables::of($recentActivityData)
                ->with([
                    'status' => 404,
                    'message' => 'No Data Found'
                ])
                ->make(true);
        }

        return DataTables::of($recentActivityData)
            ->with([
                'status' => 200,
            ])
            ->make(true);

    }


    /**
     * Summary of storerecentactivitydata
     * store new activity/recent data
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function storerecentactivitydata(Request $request)
    {
        if ($this->rp['developermodule']['recentactivitydata']['add'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $validator = Validator::make($request->all(), [
            'module' => 'required|string|max:255',
            'page' => 'required|string|max:255',
            'limit' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            return $this->errorresponse(422, $validator->messages());
        } else {

            $recentActivityData = activity_recent_data::create([
                'module' => $request->module,
                'page' => $request->page,
                'limit' => $request->limit,
                'created_by' => $this->userId,
            ]);

            if ($recentActivityData) {
                return $this->successresponse(200, 'message', 'Record succesfully added');
            } else {
                return $this->successresponse(500, 'message', 'Record not succesfully added');
            }

        }
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function editrecentactivitydata(string $id)
    {
        if ($this->rp['developermodule']['recentactivitydata']['edit'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $recentActivityData = activity_recent_data::find($id);

        if (!$recentActivityData) {
            return $this->successresponse(404, 'message', "No such record found!");
        }

        if ($this->rp['developermodule']['recentactivitydata']['alldata'] != 1) {
            if ($recentActivityData->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }

        return $this->successresponse(200, 'recentactivitydata', $recentActivityData);
    }

    /**
     * Update the specified resource in storage.
     */
    public function updaterecentactivitydata(Request $request, string $id)
    {
        if ($this->rp['developermodule']['recentactivitydata']['edit'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $validator = Validator::make($request->all(), [
            'module' => 'required|string|max:255',
            'page' => 'required|string|max:255',
            'limit' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            return $this->errorresponse(422, $validator->messages());
        } else {

            $recentActivityData = activity_recent_data::find($id);

            if (!$recentActivityData) {
                return $this->successresponse(404, 'message', 'No such record found!');
            }

            if ($this->rp['developermodule']['recentactivitydata']['alldata'] != 1) {
                if ($recentActivityData->created_by != $this->userId) {
                    return $this->successresponse(500, 'message', 'You are Unauthorized');
                }
            }

            $recentActivityData->update([
                'module' => $request->module,
                'page' => $request->page,
                'limit' => $request->limit,
                'updated_by' => $this->userId
            ]);

            return $this->successresponse(200, 'message', 'Record succesfully updated');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroyrecentactivitydata(Request $request)
    {
        if ($this->rp['developermodule']['recentactivitydata']['delete'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $recentActivityData = activity_recent_data::find($request->id);

        if (!$recentActivityData) {
            return $this->successresponse(404, 'message', 'No such record found!');
        }

        if ($this->rp['developermodule']['recentactivitydata']['alldata'] != 1) {
            if ($recentActivityData->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }

        $recentActivityData->update([
            'is_deleted' => 1
        ]);

        return $this->successresponse(200, 'message', 'Record succesfully deleted');
    }

}
