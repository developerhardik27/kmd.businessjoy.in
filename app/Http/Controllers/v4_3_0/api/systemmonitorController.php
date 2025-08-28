<?php

namespace App\Http\Controllers\v4_3_0\api;

use App\Models\company;
use Illuminate\Http\Request;
use App\Models\page_load_log;
use Illuminate\Support\Carbon;
use App\Models\task_schedule_list;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\activity_recent_data;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
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
            DB::raw("DATE_FORMAT(last_run_time, '%d-%M-%Y %h:%i:%s %p') as last_run"),
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

    public function clearDataAnalyzation(Request $request)
    {
        if ($this->rp['developermodule']['cleardata']['edit'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $validator = Validator::make($request->all(), [
            'company' => 'required|array',
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date'
        ]);

        if ($validator->fails()) {
            return $this->errorresponse(422, $validator->messages());
        }

        $companies = company::whereIn('id', $request->company)->get();
        $result = [];

        foreach ($companies as $company) {
            $isSuperAdmin = $company->id == 1;

            $companyName = DB::connection('mysql')
                ->table('company_details')
                ->where('id', $company->company_details_id)
                ->value('name');

            // Case: Company is soft-deleted
            if ($company->is_deleted == 1) {
                $masterDeletedCount = $this->countSoftDeletedRecords('mysql', 'company', ['id' => $company->id]);
                $masterRelatedCount = 0;

                if (Schema::connection('mysql')->hasTable('company_details')) {
                    $columns = Schema::connection('mysql')->getColumnListing('company_details');

                    $companyHisotryCount = DB::connection('mysql')
                        ->table('company_details')
                        ->where('company_id', $company->id)
                        ->orWhere('id', $company->company_details_id)
                        ->count();
                        
                    $usersCount = DB::connection('mysql')
                        ->table('users')
                        ->where('company_id', $company->id) 
                        ->count();

                    $masterRelatedCount = $companyHisotryCount + $usersCount ;

                }

                $result[] = [
                    'company_id' => $company->id,
                    'company_name' => $companyName,
                    'modules' => [
                        'Admin' => $masterDeletedCount + $masterRelatedCount
                    ],
                    'note' => 'This company is soft-deleted. The entire company database, master database records, and associated attachments folder (uploads/' . $company->id . ') will be permanently deleted.'
                ];
                continue;
            }

            // Set DB connection for this company
            if (!$isSuperAdmin) {
                config(['database.connections.dynamic_connection.database' => $company->dbname]);
                DB::purge('dynamic_connection');
                DB::reconnect('dynamic_connection');
            }

            $moduleResult = [];

            foreach ($this->getModuleTableMap() as $moduleName => $tables) {
                if ($isSuperAdmin && $moduleName !== 'Admin') {
                    continue;
                }

                $moduleCount = 0;

                foreach ($tables as $alias => $tableInfo) {
                    $tableName = $tableInfo['tableName'];
                    $location = $tableInfo['location'];
                    $relatedTables = $tableInfo['relatedTables'];

                    $connection = ($location === 'master') ? 'mysql' : (!$isSuperAdmin ? 'dynamic_connection' : 'mysql');

                    if (!Schema::connection($connection)->hasTable($tableName)) {
                        continue;
                    }

                    try {
                        $columns = Schema::connection($connection)->getColumnListing($tableName);
                    } catch (\Exception $e) {
                        Log::warning("Skipping table `$tableName` for company ID {$company->id}: " . $e->getMessage());
                        continue; // Skip this table safely
                    }

                    $parentDeletedIds = [];

                    // Count soft-deleted rows in parent table
                    if (in_array('is_deleted', $columns)) {
                        $query = DB::connection($connection)->table($tableName)->where('is_deleted', 1);

                        if (in_array('created_at', $columns)) {
                            if ($request->from_date && $request->to_date) {
                                $query->whereBetween('created_at', [$request->from_date, $request->to_date]);
                            } elseif ($request->from_date) {
                                $query->where('created_at', '>=', $request->from_date);
                            } elseif ($request->to_date) {
                                $query->where('created_at', '<=', $request->to_date);
                            }
                        }


                        // If it's a master table and has company_id column, filter
                        if ($location === 'master' && in_array('company_id', $columns)) {
                            $query->where('company_id', $company->id);
                        }


                        try {
                            $parentDeletedIds = $query->pluck('id')->toArray();
                        } catch (\Exception $e) {
                            Log::warning("Skipping table `$tableName` for company ID {$company->id}: " . $e->getMessage());
                            continue; // Skip this table safely
                        }


                        $moduleCount += count($parentDeletedIds);
                    }

                    // Now handle related tables
                    foreach ($relatedTables as $relatedTable) {
                        if (!Schema::connection($connection)->hasTable($relatedTable)) {
                            continue;
                        }

                        $relatedCols = Schema::connection($connection)->getColumnListing($relatedTable);
                        $query = DB::connection($connection)->table($relatedTable);

                        // Guess FK
                        $fk = $alias . '_id';
                        if (!in_array($fk, $relatedCols)) {
                            $fk = $tableName . '_id';
                        }
                        if (!in_array($fk, $relatedCols)) {
                            $fk = 'parent_id';
                        }

                        if (in_array('is_deleted', $relatedCols)) {
                            $query->where('is_deleted', 1);

                            if (in_array('created_at', $columns)) {
                                if ($request->from_date && $request->to_date) {
                                    $query->whereBetween('created_at', [$request->from_date, $request->to_date]);
                                } elseif ($request->from_date) {
                                    $query->where('created_at', '>=', $request->from_date);
                                } elseif ($request->to_date) {
                                    $query->where('created_at', '<=', $request->to_date);
                                }
                            }


                            if ($location === 'master' && in_array('company_id', $relatedCols)) {
                                $query->where('company_id', $company->id);
                            }

                            $moduleCount += $query->count();
                        } elseif (!empty($parentDeletedIds) && in_array($fk, $relatedCols)) {
                            $query->whereIn($fk, $parentDeletedIds);

                            if (in_array('created_at', $columns)) {
                                if ($request->from_date && $request->to_date) {
                                    $query->whereBetween('created_at', [$request->from_date, $request->to_date]);
                                } elseif ($request->from_date) {
                                    $query->where('created_at', '>=', $request->from_date);
                                } elseif ($request->to_date) {
                                    $query->where('created_at', '<=', $request->to_date);
                                }
                            }


                            $moduleCount += $query->count();
                        }
                    }
                }

                $moduleResult[$moduleName] = $moduleCount;
            }

            $result[] = [
                'company_id' => $company->id,
                'company_name' => $companyName,
                'modules' => $moduleResult
            ];
        }

        return $this->successresponse(200, 'data', $result);
    }


    /**
     * Helper function to count soft-deleted records in master tables
     */
    private function countSoftDeletedRecords($connection, $table, $conditions = [])
    {
        if (!Schema::connection($connection)->hasTable($table)) {
            return 0;
        }

        $columns = Schema::connection($connection)->getColumnListing($table);

        if (!in_array('is_deleted', $columns)) {
            return 0;
        }

        $query = DB::connection($connection)->table($table)->where('is_deleted', 1);

        foreach ($conditions as $field => $value) {
            $query->where($field, $value);
        }

        return $query->count();
    }

    /**
     * The getModuleTableMap() stays the same as your existing one
     */
    private function getModuleTableMap()
    {
        return  [
            'Admin' => [
                'company' => ['tableName' => 'company', 'location' => 'master', 'relatedTables' => ['company_details']],
                'user' => ['tableName' => 'users', 'location' => 'master', 'relatedTables' => ['user_permissions']],
                'userrolepermissions' => ['tableName' => 'user_role_permissions', 'location' => 'individual', 'relatedTables' => []],
                'techsupport' => ['tableName' => 'tech_supports', 'location' => 'master', 'relatedTables' => []]
            ],
            'Invoice' => [
                'bankdetail' => ['tableName' => 'bank_details', 'location' => 'individual', 'relatedTables' => []],
                'inv' => ['tableName' => 'invoices', 'location' => 'individual', 'relatedTables' => ['payment_details']],
                'invoicenumberpattern' => ['tableName' => 'invoice_number_patterns', 'location' => 'individual', 'relatedTables' => []],
                'invoicetandc' => ['tableName' => 'invoice_terms_and_conditions', 'location' => 'individual', 'relatedTables' => []],
                'invoicecustomers' => ['tableName' => 'customers', 'location' => 'individual', 'relatedTables' => []],
                'invoicedetails' => ['tableName' => 'mng_col', 'location' => 'individual', 'relatedTables' => []],
                'invoicecolumn' => ['tableName' => 'tbl_invoice_columns', 'location' => 'individual', 'relatedTables' => []],
                'invoiceformula' => ['tableName' => 'tbl_invoice_formulas', 'location' => 'individual', 'relatedTables' => []],
            ],
            'Quotation' => [
                'quotationcustomers' => ['tableName' => 'customers', 'location' => 'individual', 'relatedTables' => []],
                'quotation' => ['tableName' => 'quotations', 'location' => 'individual', 'relatedTables' => []],
                'quotationdetails' => ['tableName' => 'quotation_mng_col', 'location' => 'individual', 'relatedTables' => []],
                'quotationnumberpattern' => ['tableName' => 'quotation_number_patterns', 'location' => 'individual', 'relatedTables' => []],
                'quotationtermsandconditions' => ['tableName' => 'quotation_terms_and_conditions', 'location' => 'individual', 'relatedTables' => []],
                'quotationcolumn' => ['tableName' => 'tbl_quotation_columns', 'location' => 'individual', 'relatedTables' => []],
                'quotationformula' => ['tableName' => 'tbl_quotation_formulas', 'location' => 'individual', 'relatedTables' => []],
            ],
            'Lead' => [
                'lead' => ['tableName' => 'tbllead', 'location' => 'individual', 'relatedTables' => []],
                'leadhistory' => ['tableName' => 'tblleadhistory', 'location' => 'individual', 'relatedTables' => []],
                'api' => ['tableName' => 'api_server_keys', 'location' => 'individual', 'relatedTables' => []]
            ],
            'Customer Support' => [
                'customersupport' => ['tableName' => 'customer_support', 'location' => 'individual', 'relatedTables' => []],
                'customersupporthistory' => ['tableName' => 'customersupporthistory', 'location' => 'individual', 'relatedTables' => []]
            ],
            'Inventory' => [
                'inventory' => ['tableName' => 'inventory', 'location' => 'individual', 'relatedTables' => []],
                'product' => ['tableName' => 'products', 'location' => 'individual', 'relatedTables' => []],
                'productcategory' => ['tableName' => 'product_categories', 'location' => 'individual', 'relatedTables' => []],
                'productcolumnmapping' => ['tableName' => 'product_column_mapping', 'location' => 'individual', 'relatedTables' => []],
                'purchase' => ['tableName' => 'purchases', 'location' => 'individual', 'relatedTables' => ['purchase_history']],
                'purchaseorderdetails' => ['tableName' => 'purchase_order_details', 'location' => 'individual', 'relatedTables' => []],
                'purchaseorderdetailhistory' => ['tableName' => 'purchase_order_detail_history', 'location' => 'individual', 'relatedTables' => []],
                'supplier' => ['tableName' => 'suppliers', 'location' => 'individual', 'relatedTables' => []],
            ],
            'Reminder' => [
                'reminder' => ['tableName' => 'reminder', 'location' => 'individual', 'relatedTables' => []],
                'remindercustomer' => ['tableName' => 'reminder_customer', 'location' => 'individual', 'relatedTables' => []]
            ],
            'Blog' => [
                'blog' => ['tableName' => 'blogs', 'location' => 'individual', 'relatedTables' => []],
                'blogcategory' => ['tableName' => 'blog_categories', 'location' => 'individual', 'relatedTables' => []],
                'blogtag' => ['tableName' => 'blog_tags', 'location' => 'individual', 'relatedTables' => []],
                'api' => ['tableName' => 'api_server_keys', 'location' => 'individual', 'relatedTables' => []]
            ],
            'Logistic' => [
                'consignorcopy' => ['tableName' => 'consignor_copy', 'location' => 'individual', 'relatedTables' => []],
                'consignorcopytermsandconditions' => ['tableName' => 'consignor_copy_terms_and_conditions', 'location' => 'individual', 'relatedTables' => []],
                'consignee' => ['tableName' => 'consigness', 'location' => '', 'relatedTables' => []],
                'consignor' => ['tableName' => 'consignors', 'location' => '', 'relatedTables' => []]
            ]
        ];
    }

    public function deleteSoftDeletedData(Request $request)
    {
        if ($this->rp['developermodule']['cleardata']['edit'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $validator = Validator::make($request->all(), [
            'company' => 'required|array',
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date'
        ]);

        if ($validator->fails()) {
            return $this->errorresponse(422, $validator->messages());
        }

        $companies = company::whereIn('id', $request->company)->get();
        $result = [];

        foreach ($companies as $company) {
            $isSuperAdmin = $company->id == 1;
            $companyName = DB::connection('mysql')->table('company_details')->where('id', $company->company_details_id)->value('name');

            // Full delete path
            if ($company->is_deleted == 1 && !$isSuperAdmin) {
                try {
                    $dbName = $company->dbname;

                    // Drop all tables in the DB
                    $tables = DB::select("SHOW TABLES FROM `$dbName`");
                    $tableKey = 'Tables_in_' . $dbName;

                    foreach ($tables as $table) {
                        $tableName = $table->$tableKey;
                        DB::statement("DROP TABLE IF EXISTS `$dbName`.`$tableName`");
                    }

                    // Flush & disconnect
                    DB::statement("USE `$dbName`");
                    DB::statement("FLUSH TABLES");
                    DB::disconnect('dynamic_connection');
                    DB::disconnect('mysql');
                    usleep(200000); // wait 200ms

                    // Drop the DB
                    DB::connection('mysql')->statement("DROP DATABASE IF EXISTS `$dbName`");

                    // Delete company and user records from master
                    DB::connection('mysql')->table('company')->where('id', $company->id)->delete();
                    DB::connection('mysql')->table('company_details')
                        ->where('company_id', $company->id)
                        ->orWhere('id', $company->company_details_id)
                        ->delete();
                    DB::connection('mysql')->table('users')
                        ->where('company_id', $company->id) 
                        ->delete();

                    // Delete uploads folder
                    $companyFolder = public_path('uploads/' . $company->id);
                    if (File::exists($companyFolder)) {
                        File::deleteDirectory($companyFolder);
                        Log::info("Deleted upload folder: $companyFolder");
                    }

                    $result[] = [
                        'company_id' => $company->id,
                        'company_name' => $companyName,
                        'modules' => [],
                        'note' => "Company deleted successfully."
                    ];
                } catch (\Exception $e) {
                    $result[] = [
                        'company_id' => $company->id,
                        'company_name' => $companyName,
                        'modules' => [],
                        'note' => "Company not deleted successfully."
                    ];
                    Log::error("Failed to delete company {$company->id}: " . $e->getMessage());
                }
                continue;
            }

            // Soft delete path
            if (!$isSuperAdmin) {
                config(['database.connections.dynamic_connection.database' => $company->dbname]);
                DB::purge('dynamic_connection');
                DB::reconnect('dynamic_connection');
            }

            $moduleDeleteCounts = [];

            foreach ($this->getModuleTableMap() as $moduleName => $tables) {
                if ($isSuperAdmin && $moduleName !== 'Admin') continue;

                $moduleTotalDeleted = 0;

                foreach ($tables as $alias => $tableInfo) {
                    $tableName = $tableInfo['tableName'];
                    $location = $tableInfo['location'];
                    $relatedTables = $tableInfo['relatedTables'];

                    $connection = ($location === 'master') ? 'mysql' : (!$isSuperAdmin ? 'dynamic_connection' : 'mysql');

                    if (!Schema::connection($connection)->hasTable($tableName)) continue;

                    try {
                        $columns = Schema::connection($connection)->getColumnListing($tableName);
                    } catch (\Exception $e) {
                        Log::warning("Skip table $tableName: " . $e->getMessage());
                        continue;
                    }

                    if (!in_array('is_deleted', $columns)) continue;

                    $query = DB::connection($connection)->table($tableName)->where('is_deleted', 1);

                    if (in_array('created_at', $columns)) {
                        if ($request->from_date && $request->to_date) {
                            $query->whereBetween('created_at', [$request->from_date, $request->to_date]);
                        } elseif ($request->from_date) {
                            $query->where('created_at', '>=', $request->from_date);
                        } elseif ($request->to_date) {
                            $query->where('created_at', '<=', $request->to_date);
                        }
                    }

                    if ($location === 'master' && in_array('company_id', $columns)) {
                        $query->where('company_id', $company->id);
                    }

                    $records = $query->get();
                    $ids = $records->pluck('id')->toArray();
                    $attachmentCols = $this->getAttachmentColumnsMap()[$tableName] ?? [];

                    foreach ($records as $record) {
                        $this->deleteAttachments($record, $attachmentCols);
                        DB::connection($connection)->table($tableName)->where('id', $record->id)->delete();
                        $moduleTotalDeleted++;
                    }

                    // Related tables
                    foreach ($relatedTables as $relatedTable) {
                        if (!Schema::connection($connection)->hasTable($relatedTable)) continue;

                        $relatedCols = Schema::connection($connection)->getColumnListing($relatedTable);
                        $query = DB::connection($connection)->table($relatedTable)->whereIn($alias . '_id', $ids);

                        if (in_array('is_deleted', $relatedCols)) {
                            $query->where('is_deleted', 1);
                        }

                        if (in_array('created_at', $relatedCols)) {
                            if ($request->from_date && $request->to_date) {
                                $query->whereBetween('created_at', [$request->from_date, $request->to_date]);
                            } elseif ($request->from_date) {
                                $query->where('created_at', '>=', $request->from_date);
                            } elseif ($request->to_date) {
                                $query->where('created_at', '<=', $request->to_date);
                            }
                        }

                        $relatedRecords = $query->get();
                        $attachmentCols = $this->getAttachmentColumnsMap()[$relatedTable] ?? [];

                        foreach ($relatedRecords as $rRecord) {
                            $this->deleteAttachments($rRecord, $attachmentCols);
                            DB::connection($connection)->table($relatedTable)->where('id', $rRecord->id)->delete();
                            $moduleTotalDeleted++;
                        }
                    }
                }

                // Save total for the module
                $moduleDeleteCounts[$moduleName] = $moduleTotalDeleted;
            }

            $result[] = [
                'company_id' => $company->id,
                'company_name' => $companyName,
                'modules' => $moduleDeleteCounts,
                'note' => "Soft-deleted data and attachments removed."
            ];
        }

        return $this->successresponse(200, 'data', $result);
    }

    private function deleteAttachments($record, $columns)
    {
        foreach ($columns as $col) {
            if (!isset($record->$col)) continue;

            $value = $record->$col;
            $files = [];

            if (is_string($value) && $this->isJson($value)) {
                $files = json_decode($value, true);
            } elseif (is_string($value) && strpos($value, ',') !== false) {
                $files = explode(',', $value);
            } elseif (is_string($value)) {
                $files = [$value];
            }

            foreach ($files as $filePath) {
                $filePath = str_replace('\\/', '/', trim($filePath));
                $filePath = ltrim($filePath, '/');
                $fullPath = public_path('uploads/' . $filePath);

                if (file_exists($fullPath)) {
                    @unlink($fullPath);
                    Log::info("Deleted file: $fullPath");
                } else {
                    Log::info("File not found: $fullPath");
                }
            }
        }
    }

    private function isJson($string)
    {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }


    private function getAttachmentColumnsMap()
    {
        return [
            'company_details' => ['img', 'pr_sign_img', 'watermark_img'],
            'users' => ['img'],
            'tech_supports' => ['attachment'],
            'products' => ['product_media'],
            'blogs' => ['img', 'thumbanail_img'],
            'tbllead' => ['attachment'],
            'tblleadhistory' => ['attachment'],
        ];
    }
}
