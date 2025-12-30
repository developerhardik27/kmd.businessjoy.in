<?php

namespace App\Http\Controllers\v4_3_1\api;

use Carbon\Carbon;
use App\Models\company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class quotationController extends commonController
{
    public $userId, $companyId, $masterdbname, $rp, $quotationModel, $tbl_quotatoin_columnModel, $quotation_other_settingModel, $quotation_number_patternModel;

    public function __construct(Request $request)
    {
        $this->companyId = $request->company_id;
        $this->userId = $request->user_id;
        
        $this->dbname($this->companyId);
        // **** for checking user has permission to action on all data 
        $user_rp = DB::connection('dynamic_connection')->table('user_permissions')->where('user_id', $this->userId)->value('rp');

        if (empty($user_rp)) {
            $this->customerrorresponse();
        }

        $this->rp = json_decode($user_rp, true);

        $this->masterdbname = DB::connection()->getDatabaseName();

        $user_rp = DB::connection('dynamic_connection')->table('user_permissions')->select('rp')->where('user_id', $this->userId)->get();
        $permissions = json_decode($user_rp, true);
        if (empty($permissions)) {
            $this->customerrorresponse();
        }
        $this->rp = json_decode($permissions[0]['rp'], true);

        $this->quotationModel = $this->getmodel('quotation');
        $this->quotation_other_settingModel = $this->getmodel('quotation_other_setting');
        $this->tbl_quotatoin_columnModel = $this->getmodel('tbl_quotation_column');
        $this->quotation_number_patternModel = $this->getmodel('quotation_number_pattern');

    }


    public function totalQuotation()
    {
        if ($this->rp['quotationmodule']['quotationdashboard']['view'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $quotations = DB::connection('dynamic_connection')->table('quotations')
            ->where('is_deleted', 0);

        if ($this->rp['quotationmodule']['quotationdashboard']['alldata'] != 1) {
            $quotations->where('created_by', $this->userId);
        }

        $quotations = $quotations->count();

        return $this->successresponse(200, 'quotation', $quotations);
    }

    // chart monthly quotation counting
    public function monthlyQuotationChart(Request $request)
    {
        if ($this->rp['quotationmodule']['quotationdashboard']['view'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $quotations = DB::connection('dynamic_connection')->table('quotations')
            ->select(DB::raw("MONTH(quotation_date) as month, COUNT(*) as total_quotations, SUM(CASE WHEN status = 'accepted' THEN 1 ELSE 0 END) as accepted_quotations"))
            ->where('is_deleted', 0);

        if ($this->rp['quotationmodule']['quotationdashboard']['alldata'] != 1) {
            $quotations->where('created_by', $this->userId);
        }

        $quotations = $quotations->groupBy(DB::raw("MONTH(quotation_date)"))
            ->get();

        return response()->json($quotations);
    }

    //status vise quotation list
    public function status_list(Request $request)
    {
        if ($this->rp['quotationmodule']['quotationdashboard']['view'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $quotations = DB::connection('dynamic_connection')->table('quotations')
            ->whereYear('quotation_date', Carbon::now()->year)
            ->where('is_deleted', 0)
            ->select('quotations.*', DB::raw("DATE_FORMAT(quotations.quotation_date, '%d-%m-%Y %h:%i:%s %p') as quotation_date_formatted"));

        if ($request->quotationmonth == 'current') {
            $quotations->whereMonth('quotation_date', Carbon::now()->month);
        } elseif ($request->quotationmonth != 'all') {
            $quotations->whereMonth('quotation_date', $request->quotationmonth);
        }

        if ($this->rp['quotationmodule']['quotationdashboard']['alldata'] != 1) {
            $quotations->where('created_by', $this->userId);
        }

        $quotations = $quotations->get();
        $groupedQuotations = $quotations->groupBy('status');
        return $groupedQuotations;
    }

    // currency list
    public function currency()
    {
        $currency = DB::table('currency')->orderBy('country')->get();

        if ($currency->isEmpty()) {
            return $this->successresponse(404, 'currency', 'No records found');
        }
        return $this->successresponse(200, 'currency', $currency);
    }


    /**
     * Summary of quotation_list
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function quotation_list(Request $request)
    {

        if ($this->rp['quotationmodule']['quotation']['view'] != 1) {
            return response()->json([
                'status' => 500,
                'message' => 'You are Unauthorized',
                'data' => [],
                'recordsTotal' => 0,
                'recordsFiltered' => 0
            ]);
        }

        $quotationres = DB::connection('dynamic_connection')
            ->table('quotations')
            ->leftJoin('customers', 'quotations.customer_id', '=', 'customers.id')
            ->leftJoin($this->masterdbname . '.country', 'customers.country_id', '=', $this->masterdbname . '.country.id')
            ->leftJoin($this->masterdbname . '.state', 'customers.state_id', '=', $this->masterdbname . '.state.id')
            ->leftJoin($this->masterdbname . '.city', 'customers.city_id', '=', $this->masterdbname . '.city.id')
            ->leftJoin($this->masterdbname . '.country as country_details', 'quotations.currency_id', '=', 'country_details.id')
            ->select(
                'quotations.*',
                DB::raw("DATE_FORMAT(quotations.quotation_date, '%d-%m-%Y %h:%i:%s %p') as quotation_date_formatted"),
                'customers.house_no_building_name',
                'customers.road_name_area_colony',
                DB::raw("CONCAT_WS(' ', customers.firstname, customers.lastname, customers.company_name)as customer"),
                'country.country_name',
                'country_details.currency',
                'country_details.currency_symbol',
                'state.state_name',
                'city.city_name'
            )
            ->where('quotations.is_deleted', 0)
            ->orderBy('quotations.quotation_date', 'desc');

        if ($this->rp['quotationmodule']['quotation']['alldata'] != 1) {
            $quotationres->where('quotations.created_by', $this->userId);
        }

        $totalcount = $quotationres->get()->count(); // count total record

        $quotation = $quotationres->get();

        if ($quotation->isEmpty()) {
            return DataTables::of($quotation)
                ->with([
                    'status' => 404,
                    'message' => 'No Data Found',
                    'recordsTotal' => $totalcount, // Total records count
                ])
                ->make(true);
        }
        return DataTables::of($quotation)
            ->with([
                'status' => 200,
                'recordsTotal' => $totalcount, // Total records count
            ])
            ->make(true);
    }

    //get dynamic column name
    public function columnname(Request $request)
    {

        if ($this->rp['quotationmodule']['quotation']['add'] != 1) {
            return $this->successresponse(500, 'message', 'You are unauthorized');
        }

        $columnname = DB::connection('dynamic_connection')->table('tbl_quotation_columns')
            ->select('id', 'column_name', 'column_type', 'column_width', 'is_hide')
            ->where('is_active', 1)
            ->where('is_deleted', 0)
            ->orderBy('column_order')
            ->get();

        if ($columnname->isEmpty()) {
            return $this->successresponse(404, 'columnname', 'No records found');
        }
        return $this->successresponse(200, 'columnname', $columnname);
    }


    //get column name whose data type nubmer
    public function numbercolumnname(Request $request)
    {
        if ($this->rp['quotationmodule']['quotation']['add'] != 1) {
            return $this->successresponse(500, 'message', 'You are unauthorized');
        }

        $columnname = DB::connection('dynamic_connection')
            ->table('tbl_quotation_columns')
            ->select('column_name')
            ->whereIn('column_type', ['number', 'decimal', 'percentage'])
            ->where('is_active', 1)
            ->where('is_deleted', 0)
            ->get();

        if ($columnname->isEmpty()) {
            return $this->successresponse(404, 'columnname', 'No records found');
        }
        return $this->successresponse(200, 'columnname', $columnname);
    }

    /**
     * Display a listing of the resource.
     * using for pdf
     */
    public function index(string $id)
    {
        if ($this->rp['quotationmodule']['quotation']['view'] != 1) {
            return $this->successresponse(500, 'message', 'You are unauthorized');
        }

        $quotationres = DB::connection('dynamic_connection')->table('quotations')
            ->join('customers', 'quotations.customer_id', '=', 'customers.id')
            ->join('quotation_mng_col', 'quotations.id', '=', 'quotation_mng_col.quotation_id')
            ->leftJoin($this->masterdbname . '.country', 'customers.country_id', '=', $this->masterdbname . '.country.id')
            ->leftJoin($this->masterdbname . '.state', 'customers.state_id', '=', $this->masterdbname . '.state.id')
            ->leftJoin($this->masterdbname . '.city', 'customers.city_id', '=', $this->masterdbname . '.city.id')
            ->leftjoin('quotation_terms_and_conditions', 'quotations.t_and_c_id', '=', 'quotation_terms_and_conditions.id')
            ->join($this->masterdbname . '.country as country_details', 'quotations.currency_id', '=', 'country_details.id')
            ->select('quotation_terms_and_conditions.t_and_c', 'quotations.id', 'quotations.quotation_number', DB::raw("DATE_FORMAT(quotations.quotation_date, '%d-%m-%Y %h:%i:%s %p') as quotation_date_formatted"), 'quotations.notes', 'quotations.total', 'quotations.status', 'quotations.sgst', 'quotations.cgst', 'quotations.gst', 'quotations.grand_total', 'quotations.is_active', 'quotations.is_deleted', 'quotations.created_at', 'quotations.updated_at', 'customers.customer_id as cid', 'customers.firstname', 'customers.lastname', 'customers.company_name', 'customers.email', 'customers.contact_no', 'customers.house_no_building_name', 'customers.road_name_area_colony', 'customers.pincode', 'customers.gst_no', 'country.country_name', 'country_details.currency', 'country_details.currency_symbol', 'state.state_name', 'city.city_name', 'quotations.overdue_date')
            ->groupBy('quotation_terms_and_conditions.t_and_c', 'quotations.id', 'quotations.quotation_number', 'quotations.quotation_date', 'quotations.notes', 'quotations.total', 'quotations.status', 'quotations.sgst', 'quotations.cgst', 'quotations.gst', 'quotations.grand_total', 'quotations.is_active', 'quotations.is_deleted', 'quotations.created_at', 'quotations.updated_at', 'customers.customer_id', 'customers.firstname', 'customers.lastname', 'customers.company_name', 'customers.email', 'customers.contact_no', 'customers.house_no_building_name', 'customers.road_name_area_colony', 'customers.pincode', 'customers.gst_no', 'country.country_name', 'country_details.currency', 'country_details.currency_symbol', 'state.state_name', 'city.city_name', 'quotation_mng_col.quotation_id', 'quotations.overdue_date')
            ->where('quotations.is_active', 1)->where('quotations.is_deleted', 0)->where('quotations.id', $id);

        if ($this->rp['quotationmodule']['quotation']['alldata'] != 1 && $this->rp['reportmodule']['report']['view'] != 1) {
            $quotationres->where('quotations.created_by', $this->userId);
        }

        $quotation = $quotationres->get();
        if ($quotation->isEmpty()) {
            return $this->successresponse(404, 'quotation', 'No records found');
        }
        return $this->successresponse(200, 'quotation', $quotation);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        return $this->executeTransaction(function () use ($request) {

            if ($this->rp['quotationmodule']['quotation']['add'] != 1) {
                return $this->successresponse(500, 'message', 'You are unauthorized');
            }

            $data = $request->data; // quotation details
            $itemdata = $request->iteam_data; // product details

            // validate incoming request data
            $validator = Validator::make($data, [
                "customer" => 'required',
                "total_amount" => 'required|numeric',
                "sgst" => 'nullable|numeric',
                "cgst" => 'nullable|numeric',
                "gst" => 'nullable|numeric',
                "currency" => 'required|numeric',
                "tax_type" => 'required|numeric',
                "country_id",
                "user_id",
                'notes',
                'updated_by',
                'created_at',
                'updated_at',
                'is_active',
                'is_deleted'
            ]);

            if ($validator->fails()) {
                if (isset($data['customer']) && $data['customer'] == 'add_customer') {
                    $validator->errors()->add('customer', 'The customer field is required.');
                    return $this->errorresponse(422, $validator->messages());
                }
                return $this->errorresponse(422, $validator->messages());
            } else {
                if (isset($data['customer']) && $data['customer'] == 'add_customer') {
                    $validator->errors()->add('customer', 'The customer field is required.');
                    return $this->errorresponse(422, $validator->messages());
                }

                //fetch all column for add details into manage column table and add show column into quotation table
                $column = []; // array for show column 
                $mngcol = $this->tbl_quotatoin_columnModel::orderBy('column_order')->where('is_deleted', 0)->where('is_hide', 0)->get();

                foreach ($mngcol as $key => $val) {
                    array_push($column, $val->column_name); // push value in show column array
                }

                // show array modification 
                $columnwithunderscore = array_map(function ($value) {
                    return str_replace(' ', '_', $value); // replace (space) = (_)
                }, $column);

                $showcolumnstring = implode(',', $columnwithunderscore); // make coma separate string for show column

                // fetch last record from quotation tbl for generate dynamic quotation no
                $customer = DB::connection('dynamic_connection')->table('customers')->where('id', $data['customer'])->get();
                $company = company::join('company_details', 'company.company_details_id', '=', 'company_details.id')
                    ->select('company_details.country_id')
                    ->where('company.id', $this->companyId)
                    ->get();

                $customerid = $customer[0]->customer_id;
                $othersetting = $this->quotation_other_settingModel::find(1);
                $patterntype = 2; // pattern type 2 = global 

                if (isset($data['quotation_number'])) { //user entered manully quotation number
                    if ($customer[0]->country_id == $company[0]->country_id || !isset($customer[0]->country_id)) {
                        $patterntype = 1; // patern type 1 = local/domestic
                    }
                    // check if quotation number already exist.
                    $checkquotationnumberrec = DB::connection('dynamic_connection')->table('quotations')->where('quotation_number', $data['quotation_number'])->where('is_deleted', 0)->first();
                    if ($checkquotationnumberrec) {
                        return $this->errorresponse(422, ["quotation_number" => ['This number already exists!']]);
                    }
                    $quotation_no = $data['quotation_number'];
                } else {
                    //generate dynamic quotation number
                    $userStartDate = $othersetting->year_start; // Dynamic start date provided by the user
                    $currentMonth = date('m'); // Current month
                    $currentDay = date('d'); // Current day
                    $startMonth = date('m', strtotime($userStartDate));
                    $startDay = date('d', strtotime($userStartDate));

                    // Compare the start date's month and day with the current month and day
                    if ($currentMonth < $startMonth || ($currentMonth == $startMonth && $currentDay < $startDay)) {
                        // If the current date is before the user's starting month, count the previous year
                        $year = date('y', strtotime('-1 year'));
                    } else {
                        // If the current date is after or on the user's starting month, count the current year
                        $year = date('y');
                    }

                    $month = date('m');
                    $date = date('d');
                    $ai = '';
                    $cidai = '';
                    $patterntype = '';

                    if ($customer[0]->country_id == $company[0]->country_id || !isset($customer[0]->country_id)) {
                        $patterntype = 1; // pattern type 1 = local/domestic 
                        $increment_number = 1; // increment type 1 = increment quotation number by quotation 
                        do {
                            $getpattern = $this->quotation_number_patternModel::where('pattern_type', 'domestic')->where('is_deleted', 0)->first();
                            $quotation_no = $getpattern->quotation_pattern;
                            if ($getpattern->increment_type == 1) {
                                // Replace placeholders with actual values
                                $ai = $getpattern->current_increment_number;
                                $getpattern->update([
                                    'current_increment_number' => $ai + 1
                                ]);
                            } else {
                                $oldquotation = DB::connection('dynamic_connection')
                                    ->table('quotations')
                                    ->where('customer_id', $customerid)
                                    ->where('is_deleted', 0)
                                    ->where('quotation_number_type', 'a') // a means automatic generated number
                                    ->where('increment_type', 2) // increment type 2 = increment quotation number by customer
                                    ->orderBy('last_increment_number', 'desc')
                                    ->select('last_increment_number')
                                    ->first();

                                if (!empty($oldquotation)) {
                                    $cidai = $oldquotation->last_increment_number + $increment_number;
                                } else {
                                    $cidai = $getpattern->start_increment_number != null ? $getpattern->start_increment_number : $increment_number;
                                }
                            }

                            $quotation_no = str_replace(['date', 'month', 'year', 'customerid', 'cidai', 'ai'], [$date, $month, $year, $customerid, $cidai, $ai], $quotation_no);
                            $existingQuotation = DB::connection('dynamic_connection')->table('quotations')->where('quotation_number', $quotation_no)->where('is_deleted', 0)->exists();
                            $increment_number++;

                            if (!$existingQuotation) {
                                break;
                            }
                        } while ($existingQuotation);


                    } else {
                        $patterntype = 2; // pattern type 2 = global 
                        $increment_number = 1; // increment number 1 = increment by quotation 

                        do {
                            $getpattern = $this->quotation_number_patternModel::where('pattern_type', 'global')->where('is_deleted', 0)->first();
                            $quotation_no = $getpattern->quotation_pattern;
                            if ($getpattern->increment_type == 1) {
                                // Replace placeholders with actual values
                                $ai = $getpattern->current_increment_number;
                                $getpattern->update([
                                    'current_increment_number' => $ai + 1
                                ]);
                            } else {
                                $oldquotation = DB::connection('dynamic_connection')
                                    ->table('quotations')
                                    ->where('customer_id', $customerid)
                                    ->where('is_deleted', 0)
                                    ->where('increment_type', 2) // increment type 2 = increment by customer 
                                    ->where('quotation_number_type', 'a') // automatic generated number
                                    ->orderBy('last_increment_number', 'desc')
                                    ->select('last_increment_number')
                                    ->first();

                                if (!empty($oldquotation)) {
                                    $cidai = $oldquotation->last_increment_number + $increment_number;
                                } else {
                                    $cidai = $getpattern->start_increment_number != null ? $getpattern->start_increment_number : 1;
                                }
                            }
                            $quotation_no = str_replace(['date', 'month', 'year', 'customerid', 'cidai', 'ai'], [$date, $month, $year, $customerid, $cidai, $ai], $quotation_no);
                            $existingQuotation = DB::connection('dynamic_connection')->table('quotations')->where('quotation_number', $quotation_no)->where('is_deleted', 0)->exists();
                            $increment_number++;
                            if (!$existingQuotation) {
                                break;
                            }
                        } while ($existingQuotation);

                    }
                }


                $company_details = company::find($this->companyId);

                if ($company_details) {

                    $company_details_id = $company_details->company_details_id;

                    $quotationrec = [
                        'quotation_number' => $quotation_no,
                        'customer_id' => $data['customer'],
                        'notes' => $data['notes'],
                        'total' => $data['total_amount'],
                        'grand_total' => $data['grandtotal'],
                        'currency_id' => $data['currency'],
                        'company_id' => $this->companyId,
                        'company_details_id' => $company_details_id,
                        'created_by' => $this->userId,
                        'show_col' => $showcolumnstring,
                        'gstsettings' => json_encode($data['gstsettings']),
                        'overdue_date' => $othersetting->overdue_day,
                        'pattern_type' => $patterntype
                    ];


                    if ($data['quotation_date']) {
                        $quotationrec['quotation_date'] = $data['quotation_date'];
                    }

                    if ($data['quotation_number']) {
                        $quotationrec['quotation_number_type'] = 'm';  // set flag "m" if user entered manully quotation number - default(a)
                    }

                    if (isset($cidai) && $cidai != '') {
                        $quotationrec['last_increment_number'] = $cidai;
                        $quotationrec['increment_type'] = 2; // type 2 = increment by customer 
                    } else {
                        $quotationrec['increment_type'] = 1; // type 1 = increment by quotation 
                    }

                    if ($data['tax_type'] != 2) {
                        if (isset($data['gst'])) { // combine gst count 
                            $quotationrec['gst'] = $data['gst'];
                        } else { // seperate gst count
                            $quotationrec['sgst'] = $data['sgst'];
                            $quotationrec['cgst'] = $data['cgst'];
                        }
                    }

                    // get terms and conditions id
                    $tclastrec = DB::connection('dynamic_connection')->table('quotation_terms_and_conditions')->select('id')->where('is_deleted', 0)->where('is_active', 1)->orderBy('id', 'desc')->first();


                    if ($tclastrec) {
                        $quotationrec['t_and_c_id'] = $tclastrec->id;
                    }

                    $quotation = DB::connection('dynamic_connection')->table('quotations')->insertGetId($quotationrec); // insert quotation record 

                    if ($quotation) {
                        $quotation_id = $quotation;

                        foreach ($itemdata as $row) {
                            $dynamicdata = [];

                            // Map the values to the corresponding columns
                            foreach ($columnwithunderscore as $column) {
                                $dynamicdata[$column] = $row[$column];
                            }

                            // Add additional columns and their values
                            $dynamicdata['quotation_id'] = $quotation_id;
                            $dynamicdata['amount'] = $row['amount'];
                            $dynamicdata['created_by'] = $this->userId;
                            // Add more columns as needed

                            // Insert the record into the database
                            $quotation_mng_col = DB::connection('dynamic_connection')->table('quotation_mng_col')->insert($dynamicdata); // insert product record line by line
                        }

                        if ($quotation_mng_col) {
                            return $this->successresponse(200, 'message', 'quotation  succesfully created');
                        } else {
                            throw new \Exception("Quotation creation failed");
                        }
                    } else {
                        throw new \Exception("Quotation creation failed");
                    }
                } else {
                    return $this->successresponse(500, 'message', 'company Details not found');
                }

            }
        });

    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $quotationdetails = $this->quotationModel::where('id', $id)
            ->select('quotations.*', DB::raw("DATE_FORMAT(quotations.quotation_date, '%Y-%m-%d %H:%i') as quotation_date_formatted"))
            ->first();
        $productdetails = DB::connection('dynamic_connection')
            ->table('quotation_mng_col')
            ->where('quotation_id', $id)
            ->where('is_active', 1)
            ->where('is_deleted', 0)
            ->get();

        $data = [
            'quotationdetails' => $quotationdetails,
            'productdetails' => $productdetails
        ];
        return $this->successresponse(200, 'data', $data);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

        return $this->executeTransaction(function () use ($request, $id) {

            if ($this->rp['quotationmodule']['quotation']['edit'] != 1) {
                return $this->successresponse(500, 'message', 'You are unauthorized');
            }

            $data = $request->data; // quotation data

            // validate incoming request data
            $validator = Validator::make($data, [
                "customer" => 'required',
                "total_amount" => 'required|numeric',
                "sgst" => 'nullable|numeric',
                "cgst" => 'nullable|numeric',
                "gst" => 'nullable|numeric',
                "currency" => 'required|numeric',
                "tax_type" => 'required|numeric',
                "country_id",
                "user_id",
                'notes',
                'updated_by',
                'created_at',
                'updated_at',
                'is_active',
                'is_deleted'
            ]);

            if ($validator->fails()) {
                if (isset($data['customer']) && $data['customer'] == 'add_customer') {
                    $validator->errors()->add('customer', 'The customer field is required.');
                }
                return $this->errorresponse(422, $validator->messages());
            } else {
                if (isset($data['customer']) && $data['customer'] == 'add_customer') {
                    $validator->errors()->add('customer', 'The customer field is required.');
                    return $this->errorresponse(422, $validator->messages());
                }

                $oldquotation = $this->quotationModel::find($id);

                // update old product data
                $columanname = [];
                if ($request->old_iteam_data) {
                    $oldproductdata = $request->old_iteam_data;
                    foreach ($oldproductdata as $val) {
                        foreach ($val as $productid => $productvalue) {
                            DB::connection('dynamic_connection')->table('quotation_mng_col')->where('id', $productid)->update(
                                $productvalue
                            );
                            foreach ($productvalue as $key => $value) {
                                if (count($columanname) >= count($productvalue)) {
                                    break;
                                }
                                $columanname[] = $key; // collect column for use add new data
                            }
                        }
                    }
                } else {
                    $columanname = explode(',', $oldquotation->show_col);
                }

                // delete old product if any deleted 
                if ($request->deletedproduct) {
                    $deletedproduct = $request->deletedproduct;
                    DB::connection('dynamic_connection')->table('quotation_mng_col')->whereIn('id', $deletedproduct)->update([
                        'is_deleted' => 1,
                        'is_active' => 0,
                    ]);
                }


                // update in quotation table
                $quotationrec = [
                    'customer_id' => $data['customer'],
                    'notes' => $data['notes'],
                    'total' => $data['total_amount'],
                    'grand_total' => $data['grandtotal'],
                    'currency_id' => $data['currency'],
                    'updated_by' => $this->userId,
                ];


                if (isset($data['quotation_number'])) { //user entered manully quotation number 
                    // check if quotation number already exist.
                    $checkquotationnumberrec = DB::connection('dynamic_connection')->table('quotations')->where('quotation_number', $data['quotation_number'])->whereNot('id', $id)->where('is_deleted', 0)->first();
                    if ($checkquotationnumberrec) {
                        return $this->errorresponse(422, ["quotation_number" => ['This number already exists!']]);
                    }
                    $quotationrec['quotation_number'] = $data['quotation_number'];
                    $quotationrec['quotation_number_type'] = 'm';
                }

                if ($data['quotation_date']) {
                    $quotationrec['quotation_date'] = $data['quotation_date'];
                }


                if ($data['tax_type'] != 2) {
                    if (isset($data['gst'])) {
                        $quotationrec['gst'] = $data['gst'];
                    } else {
                        $quotationrec['sgst'] = $data['sgst'];
                        $quotationrec['cgst'] = $data['cgst'];
                    }
                }

                $quotation = DB::connection('dynamic_connection')->table('quotations')->where('id', $id)->update($quotationrec);

                // create new product data
                if ($request->iteam_data) {
                    $itemdata = $request->iteam_data;

                    foreach ($itemdata as $row) {
                        $dynamicdata = [];
                        // Map the values to the corresponding columns
                        foreach ($columanname as $column) {
                            $dynamicdata[$column] = $row[$column];
                        }
                        // Add additional columns and their values
                        $dynamicdata['quotation_id'] = $id;
                        $dynamicdata['amount'] = $row['amount'];
                        $dynamicdata['created_by'] = $this->userId;
                        $dynamicdata['updated_by'] = $this->userId;
                        // Add more columns as needed

                        // Insert the record into the database
                        $quotation_mng_col = DB::connection('dynamic_connection')->table('quotation_mng_col')->insert($dynamicdata);
                    }

                }

                return $this->successresponse(200, 'message', 'Quotation successfully updated');

            }
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {

        return $this->executeTransaction(function () use ($id) {
            // Check if the user is authorized to delete the quotation
            if ($this->rp['quotationmodule']['quotation']['delete'] != 1) {
                return $this->successresponse(500, 'message', 'You are unauthorized');
            }

            // Find the quotation by ID
            $quotation = $this->quotationModel::find($id);
            $lastquotation = $this->quotationModel::orderBy('id', 'desc')->where('is_deleted', 0)->first();

            if (!$quotation) {
                return $this->successresponse(404, 'message', 'Quotation not found');
            }

            // Update increment numbers if applicable
            if ($quotation->increment_type != 2) {
                $patterntype = $quotation->pattern_type == 1 ? 'domestic' : 'global';
                $pattern = $this->quotation_number_patternModel::where('pattern_type', $patterntype)
                    ->where('is_deleted', 0)
                    ->first();

                if ($pattern) {
                    if ($id == $lastquotation->id) {
                        $pattern->current_increment_number = max(0, $pattern->current_increment_number - 1);
                        $pattern->save();
                    }
                }
            }

            // Mark the quotation and related entries as deleted
            $quotations = DB::connection('dynamic_connection')->table('quotations')
                ->where('id', $id)
                ->update(['is_deleted' => 1]);

            if ($quotations) {
                $quotation_mng_col = DB::connection('dynamic_connection')->table('quotation_mng_col')
                    ->where('quotation_id', $id)
                    ->update(['is_deleted' => 1]);

                if ($quotation_mng_col) {
                    return $this->successresponse(200, 'message', 'Quotation successfully deleted');
                }
            }

            return $this->successresponse(404, 'message', 'Quotation not successfully deleted!');
        });
    }

    /**
     * get the specified resource from storage.
     */
    public function getquotationremarks(string $id)
    {

        // Check if the user is authorized to delete the quotation
        if ($this->rp['quotationmodule']['quotation']['edit'] != 1) {
            return $this->successresponse(500, 'message', 'You are unauthorized');
        }

        // Find the quotation by ID
        $quotation = $this->quotationModel::find($id);

        if (!$quotation) {
            return $this->successresponse(404, 'message', 'Quotation not found');
        }

        return $this->successresponse(200, 'remarks', $quotation->remarks);


    }

    /**
     * update the specified resource from storage.
     */
    public function updatequotationremarks(Request $request)
    {
        // Check if the user is authorized to delete the quotation
        if ($this->rp['quotationmodule']['quotation']['edit'] != 1) {
            return $this->successresponse(500, 'message', 'You are unauthorized');
        }

        $validator = validator::make($request->all(), [
            'remarks' => 'required',
            'quotatoin_id' => 'required'
        ]);



        // Find the quotation by ID
        $quotation = $this->quotationModel::find($request->quotation_id);

        if (!$quotation) {
            return $this->successresponse(404, 'message', 'Quotation not found');
        }

        $quotation->remarks = $request->remarks;
        $quotation->save();

        return $this->successresponse(200, 'message', 'Remarks succesfully updated.');


    }

    // use for pdf
    public function quotation_details(string $id)
    {
        $columnname = $this->quotationModel::find($id);

        if (!$columnname) {
            return $this->successresponse(404, 'quotation', 'No records found');
        }

        $columnWithoutSpaces = explode(',', $columnname->show_col);
        $columnWithSpaces = str_replace('_', ' ', $columnname->show_col);
        $column = explode(',', $columnWithSpaces);

        $columnwithtype = $this->tbl_quotatoin_columnModel::whereIn('column_name', $column)
            ->select('column_name', 'column_type', 'column_width')->orderBy('column_order')->where('is_deleted', 0)->get();

        $columnarray = array_merge($columnWithoutSpaces, ['amount']);

        // Convert collection to array of associative arrays
        $columnwithtypeArray = $columnwithtype->map(function ($item) {
            return [
                'column_name' => $item->column_name,
                'column_type' => $item->column_type,
                'column_width' => $item->column_width
            ];
        })->toArray();

        // Prepare additional column type
        $addamounttype = [
            'column_name' => 'amount',
            'column_type' => 'decimal',
            'column_width' => 'auto'
        ];

        // Merge existing column types with the new column type
        $columnwithtypeArray[] = $addamounttype;


        if ($columnarray[0] == '') {
            return $this->successresponse(404, 'quotation', 'No records found');
        }

        $quotation = DB::connection('dynamic_connection')->table('quotation_mng_col')->select($columnarray)
            ->where('quotation_id', $id)->where('is_deleted', 0)->where('is_active', 1)->get();

        $gstsettingsdetails = DB::connection('dynamic_connection')->table('quotations')
            ->select('gstsettings')->where('id', $id)
            ->get();

        if ($quotation->count() > 0) {
            return response()->json([
                'status' => 200,
                'quotation' => $quotation,
                'columns' => $columnarray,
                'othersettings' => $gstsettingsdetails,
                'columnswithtype' => $columnwithtypeArray
            ]);
        } else {
            return $this->successresponse(404, 'quotation', 'No records found');
        }
    }

    public function status(Request $request, string $id)
    {
        $quotations = DB::connection('dynamic_connection')->table('quotations')
            ->where('id', $id)
            ->update([
                'status' => $request->status
            ]);
        if ($quotations) {
            return $this->successresponse(200, 'message', 'Status updated');
        } else {
            return $this->successresponse(404, 'message', 'Quotation status not succesfully updated!');
        }
    }

    // check quotation number exist or not
    public function checkquotationnumber(Request $request)
    {
        $existsquotation = DB::connection('dynamic_connection')
            ->table('quotations')
            ->where('quotation_number', $request->quotation_number)
            ->where('is_deleted', 0);

        if ($request->searchtype == 'update') {
            $existsquotation->whereNot('id', $request->quotation_id);
        }

        $existsquotation = $existsquotation->exists();

        if ($existsquotation) {
            return $this->errorresponse(422, ["quotation_number" => ['This number already exists!']]);
        } else {
            return true;
        }
    }
}

