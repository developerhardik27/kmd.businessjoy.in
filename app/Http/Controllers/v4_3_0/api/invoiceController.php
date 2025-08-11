<?php

namespace App\Http\Controllers\v4_3_0\api;


use Carbon\Carbon;
use App\Models\company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class invoiceController extends commonController
{

    public $userId, $companyId, $masterdbname, $rp, $invoiceModel, $tbl_invoice_columnModel, $invoice_other_settingModel, $invoice_number_patternModel, $inventoryModel, $product_Model, $product_column_mappingModel;

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

        $this->invoiceModel = $this->getmodel('invoice');
        $this->invoice_other_settingModel = $this->getmodel('invoice_other_setting');
        $this->tbl_invoice_columnModel = $this->getmodel('tbl_invoice_column');
        $this->invoice_number_patternModel = $this->getmodel('invoice_number_pattern');
        $this->inventoryModel = $this->getmodel('inventory');
        $this->product_Model = $this->getmodel('product');
        $this->product_column_mappingModel = $this->getmodel('product_column_mapping');

    }


    public function totalInvoice()
    {

        if ($this->rp['invoicemodule']['invoicedashboard']['view'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $invoices = $this->invoiceModel::where('is_deleted', 0);

        if ($this->rp['invoicemodule']['invoicedashboard']['alldata'] != 1) {
            $invoices->where('created_by', $this->userId);
        }

        $invoices = $invoices->count();

        return $this->successresponse(200, 'invoice', $invoices);
    }

    // chart monthly invoice counting
    public function monthlyInvoiceChart(Request $request)
    {
        if ($this->rp['invoicemodule']['invoicedashboard']['view'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }
        $invoices = $this->invoiceModel::select(DB::raw("MONTH(inv_date) as month, COUNT(*) as total_invoices, SUM(CASE WHEN status = 'paid' THEN 1 ELSE 0 END) as paid_invoices"))
            ->where('is_deleted', 0);

        if ($this->rp['invoicemodule']['invoicedashboard']['alldata'] != 1) {
            $invoices->where('created_by', $this->userId);
        }

        $invoices = $invoices->groupBy(DB::raw("MONTH(inv_date)"))->get();

        return response()->json($invoices);
    }

    //status vise invoice list
    public function status_list(Request $request)
    {

        if ($this->rp['invoicemodule']['invoicedashboard']['view'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $invoices = $this->invoiceModel::whereYear('inv_date', Carbon::now()->year)
            ->where('is_deleted', 0)
            ->select('invoices.*', DB::raw("DATE_FORMAT(invoices.inv_date, '%d-%m-%Y %h:%i:%s %p') as inv_date_formatted"));

        if ($request->invoicemonth == 'current') {
            $invoices->whereMonth('inv_date', Carbon::now()->month);
        } elseif ($request->invoicemonth != 'all') {
            $invoices->whereMonth('inv_date', $request->invoicemonth);
        }

        if ($this->rp['invoicemodule']['invoicedashboard']['alldata'] != 1) {
            $invoices->where('created_by', $this->userId);
        }


        $invoices = $invoices->get();
        $groupedInvoices = $invoices->groupBy('status');
        return $groupedInvoices;
    }

    // currency list
    public function currency()
    {
        $currency = DB::table('currency')->orderBy('country')->get();

        if ($currency->isEmpty()) {
            return $this->successresponse(404, 'currency', 'No Records Found');
        }
        return $this->successresponse(200, 'currency', $currency);
    }

    //get bank details
    public function bdetails(Request $request)
    {
        $bank = DB::connection('dynamic_connection')->table('bank_details')
            ->where('is_active', 1)
            ->where('is_deleted', 0)
            ->get();

        if ($bank->isEmpty()) {
            return $this->successresponse(404, 'bank', 'No Records Found');
        }
        return $this->successresponse(200, 'bank', $bank);
    }

    //use for pdf
    public function inv_list(Request $request)
    {

        if ($this->rp['invoicemodule']['invoice']['view'] != 1) {
            return response()->json([
                'status' => 500,
                'message' => 'You are Unauthorized',
                'data' => [],
                'recordsTotal' => 0,
                'recordsFiltered' => 0
            ]);
        }

        $invoiceres = $this->invoiceModel::leftJoin('customers', 'invoices.customer_id', '=', 'customers.id')
            ->leftJoin($this->masterdbname . '.country', 'customers.country_id', '=', $this->masterdbname . '.country.id')
            ->leftJoin($this->masterdbname . '.state', 'customers.state_id', '=', $this->masterdbname . '.state.id')
            ->leftJoin($this->masterdbname . '.city', 'customers.city_id', '=', $this->masterdbname . '.city.id')
            ->leftJoin('payment_details', function ($join) {
                $join->on('invoices.id', '=', 'payment_details.inv_id')
                    ->whereRaw('payment_details.id = (SELECT id FROM payment_details WHERE inv_id = invoices.id ORDER BY id DESC LIMIT 1)');
            })
            ->leftJoin($this->masterdbname . '.country as country_details', 'invoices.currency_id', '=', 'country_details.id')
            ->select(
                'invoices.*',
                DB::raw("DATE_FORMAT(invoices.inv_date, '%d-%m-%Y %h:%i:%s %p') as inv_date_formatted"),
                'payment_details.part_payment',
                'payment_details.pending_amount',
                'customers.house_no_building_name',
                'customers.road_name_area_colony',
                DB::raw("CONCAT_WS(' ', customers.firstname, customers.lastname, customers.company_name)as customer"),
                'country.country_name',
                'country_details.currency',
                'country_details.currency_symbol',
                'state.state_name',
                'city.city_name'
            )
            ->where('invoices.is_deleted', 0)
            ->orderBy('invoices.inv_date', 'desc');

        if ($this->rp['invoicemodule']['invoice']['alldata'] != 1) {
            $invoiceres->where('invoices.created_by', $this->userId);
        }

        $totalcount = $invoiceres->get()->count(); // count total record

        $invoice = $invoiceres->get();

        if ($invoice->isEmpty()) {
            return DataTables::of($invoice)
                ->with([
                    'status' => 404,
                    'message' => 'No Data Found',
                    'recordsTotal' => $totalcount, // Total records count
                ])
                ->make(true);
        }

        return DataTables::of($invoice)
            ->with([
                'status' => 200,
                'recordsTotal' => $totalcount, // Total records count
            ])
            ->make(true);
    }

    //get dynamic column name
    public function columnname(Request $request)
    {

        if ($this->rp['invoicemodule']['invoice']['add'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $columnname = $this->tbl_invoice_columnModel::select('id', 'column_name', 'column_type', 'column_width', 'default_value', 'is_hide')->where('is_active', 1)->where('is_deleted', 0)->orderBy('column_order')->get();

        if ($columnname->isEmpty()) {
            return $this->successresponse(404, 'columnname', 'No Records Found');
        }
        return $this->successresponse(200, 'columnname', $columnname);
    }

    //get column name whose data type nubmer
    public function numbercolumnname(Request $request)
    {
        if ($this->rp['invoicemodule']['invoice']['add'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $columnname = $this->tbl_invoice_columnModel::select('column_name')->whereIn('column_type', ['number', 'decimal', 'percentage'])->where('is_active', 1)->where('is_deleted', 0)->get();

        if ($columnname->isEmpty()) {
            return $this->successresponse(404, 'columnname', 'No Records Found');
        }
        return $this->successresponse(200, 'columnname', $columnname);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(string $id)
    {
        if ($this->rp['invoicemodule']['invoice']['view'] != 1 && $this->rp['reportmodule']['report']['view'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $invoiceres = $this->invoiceModel::join('customers', 'invoices.customer_id', '=', 'customers.id')
            ->join('mng_col', 'invoices.id', '=', 'mng_col.invoice_id')
            ->leftJoin($this->masterdbname . '.country', 'customers.country_id', '=', $this->masterdbname . '.country.id')
            ->leftJoin($this->masterdbname . '.state', 'customers.state_id', '=', $this->masterdbname . '.state.id')
            ->leftJoin($this->masterdbname . '.city', 'customers.city_id', '=', $this->masterdbname . '.city.id')
            ->leftjoin('invoice_terms_and_conditions', 'invoices.t_and_c_id', '=', 'invoice_terms_and_conditions.id')
            ->join($this->masterdbname . '.country as country_details', 'invoices.currency_id', '=', 'country_details.id')
            ->select(
                'invoice_terms_and_conditions.t_and_c',
                'invoices.id',
                'invoices.inv_no',
                DB::raw("DATE_FORMAT(invoices.inv_date, '%d-%m-%Y %h:%i:%s %p') as inv_date_formatted"),
                'invoices.notes',
                'invoices.total',
                'invoices.status',
                'invoices.sgst',
                'invoices.cgst',
                'invoices.gst',
                'invoices.grand_total',
                'invoices.payment_type',
                'invoices.is_active',
                'invoices.is_deleted',
                'invoices.created_at',
                'invoices.updated_at',
                'customers.customer_id as cid',
                'customers.firstname',
                'customers.lastname',
                'customers.company_name',
                'customers.email',
                'customers.contact_no',
                'customers.house_no_building_name',
                'customers.road_name_area_colony',
                'customers.pincode',
                'customers.gst_no',
                'country.country_name',
                'country_details.currency',
                'country_details.currency_symbol',
                'state.state_name',
                'city.city_name'
            )
            ->groupBy('invoice_terms_and_conditions.t_and_c', 'invoices.id', 'invoices.inv_no', 'invoices.inv_date', 'invoices.notes', 'invoices.total', 'invoices.status', 'invoices.sgst', 'invoices.cgst', 'invoices.gst', 'invoices.grand_total', 'invoices.payment_type', 'invoices.is_active', 'invoices.is_deleted', 'invoices.created_at', 'invoices.updated_at', 'customers.customer_id', 'customers.firstname', 'customers.lastname', 'customers.company_name', 'customers.email', 'customers.contact_no', 'customers.house_no_building_name', 'customers.road_name_area_colony', 'customers.pincode', 'customers.gst_no', 'country.country_name', 'country_details.currency', 'country_details.currency_symbol', 'state.state_name', 'city.city_name', 'mng_col.invoice_id')
            ->where('invoices.is_active', 1)->where('invoices.is_deleted', 0)->where('invoices.id', $id);

        if ($this->rp['invoicemodule']['invoice']['alldata'] != 1 && $this->rp['reportmodule']['report']['view'] != 1) {
            $invoiceres->where('invoices.created_by', $this->userId);
        }

        $invoice = $invoiceres->get();
        if ($invoice->isEmpty()) {
            return $this->successresponse(404, 'invoice', 'No Records Found');
        }
        return $this->successresponse(200, 'invoice', $invoice);
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        return $this->executeTransaction(function () use ($request) {

            $data = $request->data; // invoice details
            $itemdata = $request->iteam_data; // product details

            // validate incoming request data
            $validator = Validator::make($data, [
                "bank_account" => 'required',
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
                return $this->errorresponse(422, $validator->messages());
            } else {

                if ($this->rp['invoicemodule']['invoice']['add'] != 1) {
                    return $this->successresponse(500, 'message', 'You are Unauthorized');
                }

                //fetch all column for add details into manage column table and add show column into invoice table
                $column = []; // array for show column 
                $mngcol = $this->tbl_invoice_columnModel::orderBy('column_order')->where('is_deleted', 0)->where('is_hide', 0)->get();

                foreach ($mngcol as $key => $val) {
                    array_push($column, $val->column_name); // push value in show column array
                }

                // show array modification 
                $columnwithunderscore = array_map(function ($value) {
                    return str_replace(' ', '_', $value); // replace (space) = (_)
                }, $column);

                $showcolumnstring = implode(',', $columnwithunderscore); // make coma separate string for show column

                // fetch last record from invoice tbl for generate dynamic inv no
                $customer = DB::connection('dynamic_connection')->table('customers')->where('id', $data['customer'])->get();
                $company = company::join('company_details', 'company.company_details_id', '=', 'company_details.id')
                    ->select('company_details.country_id')
                    ->where('company.id', $this->companyId)
                    ->get();

                $customerid = $customer[0]->customer_id;
                $othersetting = $this->invoice_other_settingModel::find(1);
                $patterntype = 2; // pattern type 2 = global 

                if (isset($data['inv_number'])) { //user entered manully inv number
                    if ($customer[0]->country_id == $company[0]->country_id || !isset($customer[0]->country_id)) {
                        $patterntype = 1; // pattern type = 1 -> local
                    }
                    // check inv number already exist.
                    $checkinvnumberrec = $this->invoiceModel::where('inv_no', $data['inv_number'])->where('is_deleted', 0)->first();
                    if ($checkinvnumberrec) {
                        return $this->errorresponse(422, ["inv_number" => ['This number already exists!']]);
                    }
                    $inv_no = $data['inv_number'];
                } else { //generate dynmaic inv number
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
                        $patterntype = 1; //  // pattern type = 1 -> local
                        $increment_number = 1;
                        do {
                            $getpattern = $this->invoice_number_patternModel::where('pattern_type', 'domestic')->where('is_deleted', 0)->first();
                            $inv_no = $getpattern->invoice_pattern;
                            if ($getpattern->increment_type == 1) {
                                // Replace placeholders with actual values
                                $ai = $getpattern->current_increment_number;
                                $getpattern->update([
                                    'current_increment_number' => $ai + 1
                                ]);
                            } else {
                                $oldinvoice = $this->invoiceModel::where('customer_id', $customerid)
                                    ->where('is_deleted', 0)
                                    ->where('inv_number_type', 'a') // a means dynamic generated number
                                    ->where('increment_type', 2) // increment type 2 = increment invoice number by customer
                                    ->orderBy('last_increment_number', 'desc')
                                    ->select('last_increment_number')
                                    ->first();

                                if (!empty($oldinvoice)) {
                                    $cidai = $oldinvoice->last_increment_number + $increment_number;
                                } else {
                                    $cidai = $getpattern->start_increment_number != null ? $getpattern->start_increment_number : $increment_number;
                                }
                            }

                            $inv_no = str_replace(['date', 'month', 'year', 'customerid', 'cidai', 'ai'], [$date, $month, $year, $customerid, $cidai, $ai], $inv_no);
                            $existingInvoice = $this->invoiceModel::where('inv_no', $inv_no)->where('is_deleted', 0)->exists();
                            $increment_number++;

                            if (!$existingInvoice) {
                                break;
                            }
                        } while ($existingInvoice);


                    } else {
                        $patterntype = 2; // pattern type 2 = global 
                        $increment_number = 1;

                        do {
                            $getpattern = $this->invoice_number_patternModel::where('pattern_type', 'global')->where('is_deleted', 0)->first();
                            $inv_no = $getpattern->invoice_pattern;
                            if ($getpattern->increment_type == 1) { // increment by invoice
                                // Replace placeholders with actual values
                                $ai = $getpattern->current_increment_number;
                                $getpattern->update([
                                    'current_increment_number' => $ai + 1
                                ]);
                            } else { // increment by customer
                                $oldinvoice = $this->invoiceModel::where('customer_id', $customerid)
                                    ->where('is_deleted', 0)
                                    ->where('increment_type', 2)
                                    ->where('inv_number_type', 'a')
                                    ->orderBy('last_increment_number', 'desc')
                                    ->select('last_increment_number')
                                    ->first();

                                if (!empty($oldinvoice)) {
                                    $cidai = $oldinvoice->last_increment_number + $increment_number;
                                } else {
                                    $cidai = $getpattern->start_increment_number != null ? $getpattern->start_increment_number : 1;
                                }
                            }
                            $inv_no = str_replace(['date', 'month', 'year', 'customerid', 'cidai', 'ai'], [$date, $month, $year, $customerid, $cidai, $ai], $inv_no);
                            $existingInvoice = $this->invoiceModel::where('inv_no', $inv_no)->where('is_deleted', 0)->exists();
                            $increment_number++;
                            if (!$existingInvoice) {
                                break;
                            }
                        } while ($existingInvoice);

                    }
                }


                $company_details = company::find($data['company_id']);

                if ($company_details) {

                    $company_details_id = $company_details->company_details_id;

                    $invoicerec = [
                        'inv_no' => $inv_no,
                        'customer_id' => $data['customer'],
                        'notes' => $data['notes'],
                        'total' => $data['total_amount'],
                        'grand_total' => $data['grandtotal'],
                        'currency_id' => $data['currency'],
                        'account_id' => $data['bank_account'],
                        'company_id' => $this->companyId,
                        'company_details_id' => $company_details_id,
                        'created_by' => $data['user_id'],
                        'show_col' => $showcolumnstring,
                        'gstsettings' => json_encode($data['gstsettings']),
                        'overdue_date' => $othersetting->overdue_day,
                        'pattern_type' => $patterntype
                    ];

                    if ($data['invoice_date']) { // if user entered manually 
                        $invoicerec['inv_date'] = $data['invoice_date'];
                    }

                    if ($data['inv_number']) { // if user enetered manually 
                        $invoicerec['inv_number_type'] = 'm';  // set flag "m" if user entered manully inv number - default(a)
                    }

                    if (isset($cidai) && $cidai != '') { // incase invoice number increment by customer 
                        $invoicerec['last_increment_number'] = $cidai;
                        $invoicerec['increment_type'] = 2;
                    } else {
                        $invoicerec['increment_type'] = 1; // incase invoice number increment by invoice
                    }

                    if ($data['tax_type'] != 2) {  // if combine gst
                        if (isset($data['gst'])) {
                            $invoicerec['gst'] = $data['gst'];
                        } else { // if sepereate gst 
                            $invoicerec['sgst'] = $data['sgst'];
                            $invoicerec['cgst'] = $data['cgst'];
                        }
                    }

                    // get terms and conditions id
                    $tclastrec = DB::connection('dynamic_connection')->table('invoice_terms_and_conditions')->select('id')->where('is_deleted', 0)->where('is_active', 1)->orderBy('id', 'desc')->first();


                    if ($tclastrec) {
                        $invoicerec['t_and_c_id'] = $tclastrec->id;
                    }

                    $invoice = $this->invoiceModel::insertGetId($invoicerec); // insert invoice record 

                    if ($invoice) {
                        $inv_id = $invoice;

                        foreach ($itemdata as $row) {
                            $dynamicdata = [];

                            // Map the values to the corresponding columns
                            foreach ($columnwithunderscore as $column) {
                                $dynamicdata[$column] = $row[$column];
                            }

                            // Add additional columns and their values
                            $dynamicdata['invoice_id'] = $inv_id;
                            $dynamicdata['amount'] = $row['amount'];
                            $dynamicdata['created_by'] = $data['user_id'];
                            // Add more columns as needed

                            if (isset($row['inventoryproduct'])) {
                                $dynamicdata['inventory_product_id'] = $row['inventoryproduct'];

                                $product = $this->product_Model::find($row['inventoryproduct']);


                                $quantitycolumn = $this->product_column_mappingModel::where('product_column', 'quantity')->where('is_deleted', 0)->pluck('invoice_column');

                                if ($quantitycolumn->count() > 0) {


                                    $updateinventory = $this->inventoryModel::where('product_id', $row['inventoryproduct'])->where('is_deleted', 0)->first();

                                    if ($product) {
                                        if ($product->continue_selling != 1) {
                                            if ($updateinventory->available < $row[$quantitycolumn[0]]) {
                                                throw new \Exception("Insufficient stock for product '{$product->name}'. Available: {$updateinventory->available}.");
                                            }
                                        }
                                    }

                                    $updateinventory->available -= $row[$quantitycolumn[0]];
                                    $updateinventory->on_hand -= $row[$quantitycolumn[0]];

                                    $updateinventory->save();
                                }

                            }

                            // Insert the record into the database
                            $mng_col = DB::connection('dynamic_connection')->table('mng_col')->insert($dynamicdata); // insert product record line by line
                        }

                        if ($mng_col) {
                            return $this->successresponse(200, 'message', 'invoice  succesfully created');
                        } else {
                            throw new \Exception("Invoice creation failed");
                        }
                    } else {
                        throw new \Exception("Invoice creation failed");
                    }
                } else {
                    return $this->successresponse(500, 'message', 'company Details not found');
                }

            }

        });
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        if ($this->rp['invoicemodule']['invoice']['view'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $invoice = $this->invoiceModel::join('customers', 'invoices.customer_id', '=', 'customers.id')
            ->join('mng_col', 'invoices.id', '=', 'mng_col.invoice_id')
            ->join('products', 'mng_col.product_id', '=', 'products.id')
            ->select('invoices.*', 'customers.firstname', 'customers.lastname', 'mng_col.item_description', 'mng_col.price', 'products.price_per_unit')
            ->where('invoices.is_deleted', 0)->where('invoices.is_active', 1)->where('id', $id)->get();

        if ($invoice->isEmpty()) {
            return $this->successresponse(404, 'invoice', 'No Records Found');
        }

        if ($this->rp['adminmodule']['user']['alldata'] != 1) {
            if ($invoice[0]->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }

        return $this->successresponse(404, 'invoice', $invoice);


    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $invdetails = $this->invoiceModel::where('id', $id)
            ->select('invoices.*', DB::raw("DATE_FORMAT(invoices.inv_date, '%Y-%m-%d %H:%i') as inv_date_formatted"))
            ->first();
        $productdetails = DB::connection('dynamic_connection')->table('mng_col')
            ->leftJoin('inventory', 'mng_col.inventory_product_id', 'inventory.product_id')
            ->where('mng_col.invoice_id', $id)
            ->where('mng_col.is_active', 1)
            ->where('mng_col.is_deleted', 0)
            ->select('mng_col.*', 'inventory.available as available_stock')
            ->get();

        $data = [
            'invdetails' => $invdetails,
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

            if ($this->rp['invoicemodule']['invoice']['edit'] != 1) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }

            $data = $request->data; // invoice data

            // validate incoming request data
            $validator = Validator::make($data, [
                "bank_account" => 'required',
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
                return $this->errorresponse(422, $validator->messages());
            } else {

                $oldinvoice = $this->invoiceModel::find($id);

                //get quantity linked column
                $quantitycolumn = $this->product_column_mappingModel::where('product_column', 'quantity')->where('is_deleted', 0)->pluck('invoice_column');

                // update old product data
                $columanname = [];
                if ($request->old_iteam_data) {
                    $oldproductdata = $request->old_iteam_data;
                    foreach ($oldproductdata as $val) {
                        foreach ($val as $productid => $productvalue) {
                            //get old product record
                            $fetcholdproduct = DB::connection('dynamic_connection')->table('mng_col')->find($productid);

                            if (isset($fetcholdproduct->inventory_product_id) && $quantitycolumn->count() > 0) {


                                $oldquantity = (int) $fetcholdproduct->{$quantitycolumn[0]};

                                $newquantity = (int) $productvalue[$quantitycolumn[0]];

                                $inventory = $this->inventoryModel::where('product_id', $fetcholdproduct->inventory_product_id ?? 1)->where('is_deleted', 0)->first();

                                if ($inventory) {
                                    if ($oldquantity > $newquantity) {
                                        $managequantity = $oldquantity - $newquantity;
                                        $inventory->available += $managequantity;
                                        $inventory->on_hand += $managequantity;
                                    } else if ($oldquantity < $newquantity) {
                                        $managequantity = $newquantity - $oldquantity;
                                        $inventory->available -= (int) $managequantity;
                                        $inventory->on_hand -= (int) $managequantity;
                                    }
                                    $inventory->save();
                                }

                            }

                            unset($productvalue['inventoryproduct']);

                            DB::connection('dynamic_connection')->table('mng_col')
                                ->where('id', $productid)
                                ->update(
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
                    $columanname = explode(',', $oldinvoice->show_col);
                }


                // delete old product if any deleted
                if ($request->deletedproduct) {
                    $deletedproduct = $request->deletedproduct;
                    $getdeletedproduct = DB::connection('dynamic_connection')->table('mng_col')->whereIn('id', $deletedproduct)->get();


                    if ($getdeletedproduct->count() > 0) {
                        foreach ($getdeletedproduct as $product) {

                            if (count($quantitycolumn) > 0) {
                                // Ensure $quantitycolumn is an array and access its first element properly
                                $quantity = isset($product->{$quantitycolumn[0]}) ? (int) $product->{$quantitycolumn[0]} : 0;

                                // Check if inventory_product_id exists
                                if (isset($product->inventory_product_id)) {
                                    $inventory = $this->inventoryModel::where('product_id', $product->inventory_product_id)
                                        ->where('is_deleted', 0)
                                        ->first();

                                    if ($inventory) {
                                        // Update inventory values with the quantity from the deleted product
                                        $inventory->available += $quantity;
                                        $inventory->on_hand += $quantity;

                                        // Save updated inventory
                                        $inventory->save();
                                    }
                                }
                            }

                            // Manually update the product in the mng_col table to mark it as deleted
                            DB::connection('dynamic_connection')->table('mng_col')
                                ->where('id', $product->id)
                                ->update([
                                    'is_deleted' => 1,
                                    'is_active' => 0
                                ]);
                        }
                    }
                }



                // update in invoice table
                $invoicerec = [
                    'customer_id' => $data['customer'],
                    'notes' => $data['notes'],
                    'total' => $data['total_amount'],
                    'grand_total' => $data['grandtotal'],
                    'currency_id' => $data['currency'],
                    'account_id' => $data['bank_account'],
                    'updated_by' => $data['user_id'],
                ];


                if (isset($data['inv_number'])) { //user entered manully inv number 
                    // check if inv number already exist.
                    $checkinvnumberrec = $this->invoiceModel::where('inv_no', $data['inv_number'])->whereNot('id', $id)->where('is_deleted', 0)->first();
                    if ($checkinvnumberrec) {
                        return $this->errorresponse(422, ["inv_number" => ['This number already exists!']]);
                    }
                    $invoicerec['inv_no'] = $data['inv_number'];
                    $invoicerec['inv_number_type'] = 'm';
                }

                if ($data['invoice_date']) {
                    $invoicerec['inv_date'] = $data['invoice_date'];
                }


                if ($data['tax_type'] != 2) {
                    if (isset($data['gst'])) {
                        $invoicerec['gst'] = $data['gst'];
                    } else {
                        $invoicerec['sgst'] = $data['sgst'];
                        $invoicerec['cgst'] = $data['cgst'];
                    }
                }

                $invoice = $this->invoiceModel::where('id', $id)->update($invoicerec);

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
                        $dynamicdata['invoice_id'] = $id;
                        $dynamicdata['amount'] = $row['amount'];
                        $dynamicdata['created_by'] = $data['user_id'];
                        $dynamicdata['updated_by'] = $data['user_id'];
                        // Add more columns as needed


                        if (isset($row['inventoryproduct'])) {
                            $dynamicdata['inventory_product_id'] = $row['inventoryproduct'];

                            $product = $this->product_Model::find($row['inventoryproduct']);

                            if ($quantitycolumn->count() > 0) {
                                $updateinventory = $this->inventoryModel::where('product_id', $row['inventoryproduct'])->where('is_deleted', 0)->first();

                                if ($product) {
                                    if ($product->continue_selling != 1) {
                                        if ($updateinventory->available < $row[$quantitycolumn[0]]) {
                                            throw new \Exception("Insufficient stock for product '{$product->name}'. Available: {$updateinventory->available}.");
                                        }
                                    }
                                }

                                $updateinventory->available -= $row[$quantitycolumn[0]];
                                $updateinventory->on_hand -= $row[$quantitycolumn[0]];

                                $updateinventory->save();
                            }

                        }

                        // Insert the record into the database
                        $mng_col = DB::connection('dynamic_connection')->table('mng_col')->insert($dynamicdata);
                    }

                }

                return $this->successresponse(200, 'message', 'Invoice successfully updated');

            }
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        return $this->executeTransaction(function () use ($id) {
            // Check if the user is authorized to delete the invoice
            if ($this->rp['invoicemodule']['invoice']['delete'] != 1) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }

            // Find the invoice by ID
            $inv = $this->invoiceModel::find($id);
            $lastinv = $this->invoiceModel::orderBy('id', 'desc')->where('is_deleted', 0)->first();

            if (!$inv) {
                return $this->successresponse(404, 'message', 'Invoice not found');
            }

            // Update increment numbers if applicable
            if ($inv->increment_type != 2) {
                $patterntype = $inv->pattern_type == 1 ? 'domestic' : 'global';
                $pattern = $this->invoice_number_patternModel::where('pattern_type', $patterntype)
                    ->where('is_deleted', 0)
                    ->first();

                if ($pattern) {
                    if ($id == $lastinv->id) {
                        $pattern->current_increment_number = max(0, $pattern->current_increment_number - 1);
                        $pattern->save();
                    }
                }
            }

            // Mark the invoice and related entries as deleted
            $invoices = $this->invoiceModel::where('id', $id)
                ->update(['is_deleted' => 1]);

            if ($invoices) {

                $quantitycolumn = $this->product_column_mappingModel::where('product_column', 'quantity')->where('is_deleted', 0)->pluck('invoice_column');


                if ($quantitycolumn->count() > 0) {

                    $inventoryproduct = DB::connection('dynamic_connection')->table('mng_col')
                        ->where('invoice_id', $id)
                        ->whereNotNull('inventory_product_id')
                        ->where('is_deleted', 0)
                        ->pluck($quantitycolumn[0], 'inventory_product_id');

                    if ($inventoryproduct->count() > 0) {

                        foreach ($inventoryproduct as $productid => $quantity) {
                            $inventory = $this->inventoryModel::where('product_id', $productid)->where('is_deleted', 0)->first();

                            if ($inventory) {
                                $inventory->available += (int) $quantity;
                                $inventory->on_hand += (int) $quantity;

                                $inventory->save();
                            }

                        }

                    }

                }



                $mng_col = DB::connection('dynamic_connection')->table('mng_col')
                    ->where('invoice_id', $id)
                    ->update(['is_deleted' => 1]);

                if ($mng_col) {
                    return $this->successresponse(200, 'message', 'Invoice successfully deleted');
                }
            }
            return $this->successresponse(404, 'message', 'Invoice not successfully deleted!');
        });

    }

    // use for pdf
    public function inv_details(string $id)
    {

        $columnname = $this->invoiceModel::find($id);

        if (!$columnname) {
            return $this->successresponse(404, 'invoice', 'No Records Found');
        }

        $columnWithoutSpaces = explode(',', $columnname->show_col);
        $columnWithSpaces = str_replace('_', ' ', $columnname->show_col);
        $column = explode(',', $columnWithSpaces);

        $columnwithtype = $this->tbl_invoice_columnModel::whereIn('column_name', $column)
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
            'column_width' => '20'
        ];

        // Merge existing column types with the new column type
        $columnwithtypeArray[] = $addamounttype;


        if ($columnarray[0] == '') {
            return $this->successresponse(404, 'invoice', 'No Records Found');
        }

        $invoice = DB::connection('dynamic_connection')->table('mng_col')->select($columnarray)
            ->where('invoice_id', $id)->where('is_deleted', 0)->where('is_active', 1)->get();

        $gstsettingsdetails = $this->invoiceModel::select('gstsettings')->where('id', $id)
            ->get();

        $invoiceothersettings = $this->invoice_other_settingModel::first();

        if ($invoice->isEmpty()) {
            return $this->successresponse(404, 'invoice', 'No Records Found');
        }
        return response()->json([
            'status' => 200,
            'invoice' => $invoice,
            'columns' => $columnarray,
            'othersettings' => $gstsettingsdetails,
            'columnswithtype' => $columnwithtypeArray,
            'invoiceothersettings' => $invoiceothersettings

        ]);
    }

    /**
     * Summary of status
     * update invoice status 
     * @param \Illuminate\Http\Request $request
     * @param string $id
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function status(Request $request, string $id)
    {
        $invoices = $this->invoiceModel::where('id', $id)
            ->update([
                'status' => $request->status
            ]);
        if ($invoices) {
            return $this->successresponse(200, 'message', 'status updated');
        } else {
            return $this->successresponse(404, 'message', 'invoice  status not succesfully updated!');
        }
    }

    /**
     * Summary of reportlogsdetails
     * report log by user
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function reportlogsdetails(Request $request)
    {
        if ($this->rp['reportmodule']['report']['log'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $reports = DB::connection('dynamic_connection')->table('reportlogs')
            ->join($this->masterdbname . '.users', 'reportlogs.created_by', '=', $this->masterdbname . '.users.id')
            ->select('reportlogs.*', DB::raw("DATE_FORMAT(reportlogs.from_date, '%d-%m-%Y') as from_date_formatted"), DB::raw("DATE_FORMAT(reportlogs.to_date, '%d-%m-%Y') as to_date_formatted"), DB::raw("DATE_FORMAT(reportlogs.created_at, '%d-%m-%Y %h:%i:%s %p') as created_at_formatted"), 'users.firstname', 'users.lastname')
            ->where('reportlogs.is_deleted', 0)->orderBy('id', 'desc')->get();

        if ($reports->isEmpty()) {
            return $this->successresponse(404, 'reports', 'No Records Found');
        }
        return $this->successresponse(200, 'reports', $reports);


    }

    /**
     * Summary of reportlogdestroy
     * delete report log history record
     * @param \Illuminate\Http\Request $request
     * @param string $id
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function reportlogdestroy(Request $request, string $id)
    {
        if ($this->rp['reportmodule']['report']['delete'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }
        $reportlog = DB::connection('dynamic_connection')
            ->table('reportlogs')
            ->where('id', $id)
            ->update(['is_deleted' => 1]);


        if (!$reportlog) {
            return $this->successresponse(404, 'message', 'No Such Log Found!');
        }
        return $this->successresponse(200, 'message', 'reportlog succesfully deleted');
    }

    // check invoice number exist or not
    public function checkinvoicenumber(Request $request)
    {
        $existsinvoice = $this->invoiceModel::where('inv_no', $request->inv_number)->where('is_deleted', 0);

        if ($request->searchtype == 'update') {
            $existsinvoice->whereNot('id', $request->inv_id);
        }

        $existsinvoice = $existsinvoice->exists();

        if (!$existsinvoice) {
            return true;
        }

        return $this->errorresponse(422, ["inv_number" => ['This number already exists!']]);
    }
}
