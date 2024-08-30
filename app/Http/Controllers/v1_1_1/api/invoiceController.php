<?php

namespace App\Http\Controllers\v1_1_1\api;


use App\Models\company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class invoiceController extends commonController
{

    public $userId, $companyId, $masterdbname, $rp, $invoiceModel, $tbl_invoice_columnModel, $invoice_other_settingModel, $invoice_number_patternModel;

    public function __construct(Request $request)
    {
        if ($request->company_id) {
            $this->dbname($request->company_id);
            $this->companyId = $request->company_id;
        } else {
            $this->dbname(session()->get('company_id'));
        }
        if ($request->user_id) {
            $this->userId = $request->user_id;
        } else {
            $this->userId = session()->get('user_id');
        }

        $this->masterdbname = DB::connection()->getDatabaseName();

        $user_rp = DB::connection('dynamic_connection')->table('user_permissions')->select('rp')->where('user_id', $this->userId)->get();
        $permissions = json_decode($user_rp, true);
        $this->rp = json_decode($permissions[0]['rp'], true);

        $this->invoiceModel = $this->getmodel('invoice');
        $this->invoice_other_settingModel = $this->getmodel('invoice_other_setting');
        $this->tbl_invoice_columnModel = $this->getmodel('tbl_invoice_column');
        $this->invoice_number_patternModel = $this->getmodel('invoice_number_pattern');

    }

    // chart monthly invoice counting
    public function monthlyInvoiceChart(Request $request)
    {
        $invoices = DB::connection('dynamic_connection')->table('invoices')
            ->select(DB::raw("MONTH(inv_date) as month, COUNT(*) as total_invoices, SUM(CASE WHEN status = 'paid' THEN 1 ELSE 0 END) as paid_invoices"))
            ->groupBy(DB::raw("MONTH(inv_date)"))->where('created_by', $this->userId)->where('is_deleted', 0)
            ->get();

        return $invoices;
    }

    //status vise invoice list
    public function status_list(Request $request)
    {
        $currentMonth = Carbon::now()->format('Y-m');

        $invoices = DB::connection('dynamic_connection')->table('invoices')->whereYear('inv_date', Carbon::now()->year)
            ->whereMonth('inv_date', Carbon::now()->month)->where('created_by', $this->userId)->where('is_deleted', 0)
            ->select('invoices.*', DB::raw("DATE_FORMAT(invoices.inv_date, '%d-%m-%Y %h:%i:%s %p') as inv_date_formatted"))
            ->get();
        $groupedInvoices = $invoices->groupBy('status');
        return $groupedInvoices;
    }

    // currency list
    public function currency()
    {
        $currency = DB::table('currency')->orderBy('country')->get();

        if ($currency->count() > 0) {
            return $this->successresponse(200, 'currency', $currency);
        } else {
            return $this->successresponse(404, 'currency', 'No Records Found');
        }
    }
    //get bank details
    public function bdetails(Request $request)
    {
        $bank = DB::connection('dynamic_connection')->table('bank_details')->get()->where('is_active', 1)->where('is_deleted', 0);

        if ($bank->count() > 0) {
            return $this->successresponse(200, 'bank', $bank);
        } else {
            return $this->successresponse(404, 'bank', 'No Records Found');
        }
    }

    //use for pdf
    public function inv_list(Request $request)
    {

        if ($this->rp['invoicemodule']['invoice']['view'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $invoiceres = DB::connection('dynamic_connection')->table('invoices')->leftJoin('customers', 'invoices.customer_id', '=', 'customers.id')
            ->leftJoin($this->masterdbname . '.country', 'customers.country_id', '=', $this->masterdbname . '.country.id')
            ->leftJoin($this->masterdbname . '.state', 'customers.state_id', '=', $this->masterdbname . '.state.id')
            ->leftJoin($this->masterdbname . '.city', 'customers.city_id', '=', $this->masterdbname . '.city.id')
            ->leftJoin('payment_details', function ($join) {
                $join->on('invoices.id', '=', 'payment_details.inv_id')
                    ->whereRaw('payment_details.id = (SELECT id FROM payment_details WHERE inv_id = invoices.id ORDER BY id DESC LIMIT 1)');
            })
            ->leftJoin($this->masterdbname . '.country as country_details', 'invoices.currency_id', '=', 'country_details.id')
            ->select('invoices.*', DB::raw("DATE_FORMAT(invoices.inv_date, '%d-%m-%Y %h:%i:%s %p') as inv_date_formatted"), 'payment_details.part_payment', 'payment_details.pending_amount', 'customers.house_no_building_name', 'customers.road_name_area_colony', 'customers.firstname', 'customers.lastname', 'customers.company_name', 'country.country_name', 'country_details.currency', 'country_details.currency_symbol', 'state.state_name', 'city.city_name')
            ->where('invoices.is_deleted', 0)
            ->orderBy('invoices.inv_date', 'desc');

        if ($this->rp['invoicemodule']['invoice']['alldata'] != 1) {
            $invoiceres->where('invoices.created_by', $this->userId);
        }
        $invoice = $invoiceres->get();

        if ($invoice->count() > 0) {
            return $this->successresponse(200, 'invoice', $invoice);
        } else {
            return $this->successresponse(404, 'invoice', 'No Records Found');
        }
    }

    //get dynamic column name
    public function columnname(Request $request)
    {

        if ($this->rp['invoicemodule']['invoice']['add'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $columnname = DB::connection('dynamic_connection')->table('tbl_invoice_columns')->select('id', 'column_name', 'column_type','column_width', 'is_hide')->where('is_active', 1)->where('is_deleted', 0)->orderBy('column_order')->get();

        if ($columnname->count() > 0) {
            return $this->successresponse(200, 'columnname', $columnname);
        } else {
            return $this->successresponse(404, 'columnname', 'No Records Found');
        }
    }


    //get column name whose data type nubmer
    public function numbercolumnname(Request $request)
    {
        if ($this->rp['invoicemodule']['invoice']['add'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $columnname = DB::connection('dynamic_connection')->table('tbl_invoice_columns')->select('column_name')->whereIn('column_type', ['number', 'decimal', 'percentage'])->where('is_active', 1)->where('is_deleted', 0)->get();

        if ($columnname->count() > 0) {
            return $this->successresponse(200, 'columnname', $columnname);
        } else {
            return $this->successresponse(404, 'columnname', 'No Records Found');
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index(string $id)
    {
        if ($this->rp['invoicemodule']['invoice']['view'] != 1 && $this->rp['reportmodule']['report']['view'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $invoiceres = DB::connection('dynamic_connection')->table('invoices')
            ->join('customers', 'invoices.customer_id', '=', 'customers.id')
            ->join('mng_col', 'invoices.id', '=', 'mng_col.invoice_id')
            ->leftJoin($this->masterdbname . '.country', 'customers.country_id', '=', $this->masterdbname . '.country.id')
            ->leftJoin($this->masterdbname . '.state', 'customers.state_id', '=', $this->masterdbname . '.state.id')
            ->leftJoin($this->masterdbname . '.city', 'customers.city_id', '=', $this->masterdbname . '.city.id')
            ->leftjoin('invoice_terms_and_conditions', 'invoices.t_and_c_id', '=', 'invoice_terms_and_conditions.id')
            ->join($this->masterdbname . '.country as country_details', 'invoices.currency_id', '=', 'country_details.id')
            ->select('invoice_terms_and_conditions.t_and_c', 'invoices.id', 'invoices.inv_no', DB::raw("DATE_FORMAT(invoices.inv_date, '%d-%m-%Y %h:%i:%s %p') as inv_date_formatted"), 'invoices.notes', 'invoices.total', 'invoices.status', 'invoices.sgst', 'invoices.cgst', 'invoices.gst', 'invoices.grand_total', 'invoices.payment_type', 'invoices.is_active', 'invoices.is_deleted', 'invoices.created_at', 'invoices.updated_at', 'customers.customer_id as cid', 'customers.firstname', 'customers.lastname', 'customers.company_name', 'customers.email', 'customers.contact_no', 'customers.house_no_building_name', 'customers.road_name_area_colony', 'customers.pincode', 'customers.gst_no', 'country.country_name', 'country_details.currency', 'country_details.currency_symbol', 'state.state_name', 'city.city_name')
            ->groupBy('invoice_terms_and_conditions.t_and_c', 'invoices.id', 'invoices.inv_no', 'invoices.inv_date', 'invoices.notes', 'invoices.total', 'invoices.status', 'invoices.sgst', 'invoices.cgst', 'invoices.gst', 'invoices.grand_total', 'invoices.payment_type', 'invoices.is_active', 'invoices.is_deleted', 'invoices.created_at', 'invoices.updated_at', 'customers.customer_id', 'customers.firstname', 'customers.lastname', 'customers.company_name', 'customers.email', 'customers.contact_no', 'customers.house_no_building_name', 'customers.road_name_area_colony', 'customers.pincode', 'customers.gst_no', 'country.country_name', 'country_details.currency', 'country_details.currency_symbol', 'state.state_name', 'city.city_name', 'mng_col.invoice_id')
            ->where('invoices.is_active', 1)->where('invoices.is_deleted', 0)->where('invoices.id', $id);

        if ($this->rp['invoicemodule']['invoice']['alldata'] != 1 && $this->rp['reportmodule']['report']['view'] != 1) {
            $invoiceres->where('invoices.created_by', $this->userId);
        }

        $invoice = $invoiceres->get();
        if ($invoice->count() > 0) {
            return $this->successresponse(200, 'invoice', $invoice);
        } else {
            return $this->successresponse(404, 'invoice', 'No Records Found');
        }
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $data = $request->data; // invoice details
        $itemdata = $request->iteam_data; // product details

        // validate incoming request data
        $validator = Validator::make($data, [
            "payment_mode" => 'required',
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
            $patterntype = 2;

            if (isset($data['inv_number'])) { //user entered manully inv number
                if ($customer[0]->country_id == $company[0]->country_id || !isset($customer[0]->country_id)) {
                    $patterntype = 1;
                }
                // check if inv number already exist.
                $checkinvnumberrec = DB::connection('dynamic_connection')->table('invoices')->where('inv_no', $data['inv_number'])->where('is_deleted', 0)->first();
                if ($checkinvnumberrec) {
                    return $this->errorresponse(422, ["inv_number" => ['This number already exists!']]);
                }
                $inv_no = $data['inv_number'];
            } else {
                //generate dynmaic inv number
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
                    $patterntype = 1;
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
                            $oldinvoice = DB::connection('dynamic_connection')
                                ->table('invoices')
                                ->where('customer_id', $customerid)
                                ->where('is_deleted', 0)
                                ->where('inv_number_type', 'a')
                                ->where('increment_type', 2)
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
                        $existingInvoice = DB::connection('dynamic_connection')->table('invoices')->where('inv_no', $inv_no)->where('is_deleted', 0)->exists();
                        $increment_number++;

                        if (!$existingInvoice) {
                            break;
                        }
                    } while ($existingInvoice);


                } else {
                    $patterntype = 2;
                    $increment_number = 1;

                    do {
                        $getpattern = $this->invoice_number_patternModel::where('pattern_type', 'global')->where('is_deleted', 0)->first();
                        $inv_no = $getpattern->invoice_pattern;
                        if ($getpattern->increment_type == 1) {
                            // Replace placeholders with actual values
                            $ai = $getpattern->current_increment_number;
                            $getpattern->update([
                                'current_increment_number' => $ai + 1
                            ]);
                        } else {
                            $oldinvoice = DB::connection('dynamic_connection')
                                ->table('invoices')
                                ->where('customer_id', $customerid)
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
                        $existingInvoice = DB::connection('dynamic_connection')->table('invoices')->where('inv_no', $inv_no)->where('is_deleted', 0)->exists();
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
                    'payment_type' => $data['payment_mode'],
                    'account_id' => $data['bank_account'],
                    'company_id' => $this->companyId,
                    'company_details_id' => $company_details_id,
                    'created_by' => $data['user_id'],
                    'show_col' => $showcolumnstring,
                    'gstsettings' => json_encode($data['gstsettings']),
                    'overdue_date' => $othersetting->overdue_day,
                    'pattern_type' => $patterntype
                ];

                if ($data['invoice_date']) {
                    $invoicerec['inv_date'] = $data['invoice_date'];
                }

                if ($data['inv_number']) {
                    $invoicerec['inv_number_type'] = 'm';  // set flag if user entered manully inv number default(a)
                }

                if (isset($cidai) && $cidai != '') {
                    $invoicerec['last_increment_number'] = $cidai;
                    $invoicerec['increment_type'] = 2;
                } else {
                    $invoicerec['increment_type'] = 1;
                }

                if ($data['tax_type'] != 2) {
                    if (isset($data['gst'])) {
                        $invoicerec['gst'] = $data['gst'];
                    } else {
                        $invoicerec['sgst'] = $data['sgst'];
                        $invoicerec['cgst'] = $data['cgst'];
                    }
                }

                // get terms and conditions id
                $tclastrec = DB::connection('dynamic_connection')->table('invoice_terms_and_conditions')->select('id')->where('is_deleted', 0)->where('is_active', 1)->orderBy('id', 'desc')->first();


                if ($tclastrec) {
                    $invoicerec['t_and_c_id'] = $tclastrec->id;
                }

                $invoice = DB::connection('dynamic_connection')->table('invoices')->insertGetId($invoicerec); // insert invoice record 

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

                        // Insert the record into the database
                        $mng_col = DB::connection('dynamic_connection')->table('mng_col')->insert($dynamicdata); // insert product record line by line
                    }

                    if ($mng_col) {
                        return $this->successresponse(200, 'message', 'invoice  succesfully created');
                    } else {
                        $id = $invoice;
                        $record = $this->invoiceModel::find($id);
                        // Check if the record exists
                        if ($record) {
                            // Delete the record
                            $record->delete();
                        }
                        return $this->successresponse(500, 'message', 'invoice details not succesfully created');
                    }
                } else {
                    return $this->successresponse(500, 'message', 'invoice not succesfully created');
                }
            } else {
                return $this->successresponse(500, 'message', 'company Details not found');
            }

        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        if ($this->rp['invoicemodule']['invoice']['view'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $invoice = DB::connection('dynamic_connection')->table('invoices')
            ->join('customers', 'invoices.customer_id', '=', 'customers.id')
            ->join('mng_col', 'invoices.id', '=', 'mng_col.invoice_id')
            ->join('products', 'mng_col.product_id', '=', 'products.id')
            ->select('invoices.*', 'customers.firstname', 'customers.lastname', 'mng_col.item_description', 'mng_col.price', 'products.price_per_unit')
            ->where('invoices.is_deleted', 0)->where('invoices.is_active', 1)->where('id', $id)->get();

        if ($this->rp['adminmodule']['user']['alldata'] != 1) {
            if ($invoice[0]->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $invdetails = $this->invoiceModel::where('id', $id)
            ->select('invoices.*', DB::raw("DATE_FORMAT(invoices.inv_date, '%Y-%m-%d %H:%i') as inv_date_formatted"))
            ->first();
        $productdetails = DB::connection('dynamic_connection')->table('mng_col')->where('invoice_id', $id)->where('is_active', 1)->where('is_deleted', 0)->get();

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

        $data = $request->data; // invoice data

        // validate incoming request data
        $validator = Validator::make($data, [
            "payment_mode" => 'required',
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

            if ($this->rp['invoicemodule']['invoice']['edit'] != 1) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }


            $oldinvoice = $this->invoiceModel::find($id);

            // update old product data
            $columanname = [];
            if ($request->old_iteam_data) {
                $oldproductdata = $request->old_iteam_data;
                foreach ($oldproductdata as $val) {
                    foreach ($val as $productid => $productvalue) {
                        DB::connection('dynamic_connection')->table('mng_col')->where('id', $productid)->update(
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
                DB::connection('dynamic_connection')->table('mng_col')->whereIn('id', $deletedproduct)->update([
                    'is_deleted' => 1,
                    'is_active' => 0,
                ]);
            }


            // update in invoice table
            $invoicerec = [
                'customer_id' => $data['customer'],
                'notes' => $data['notes'],
                'total' => $data['total_amount'],
                'grand_total' => $data['grandtotal'],
                'currency_id' => $data['currency'],
                'payment_type' => $data['payment_mode'],
                'account_id' => $data['bank_account'],
                'updated_by' => $data['user_id'],
            ];

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

            $invoice = DB::connection('dynamic_connection')->table('invoices')->where('id', $id)->update($invoicerec);

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

                    // Insert the record into the database
                    $mng_col = DB::connection('dynamic_connection')->table('mng_col')->insert($dynamicdata);
                }

            }

            return $this->successresponse(200, 'message', 'Invoice successfully updated');

        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
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
        $invoices = DB::connection('dynamic_connection')->table('invoices')
            ->where('id', $id)
            ->update(['is_deleted' => 1]);

        if ($invoices) {
            $mng_col = DB::connection('dynamic_connection')->table('mng_col')
                ->where('invoice_id', $id)
                ->update(['is_deleted' => 1]);

            if ($mng_col) {
                return $this->successresponse(200, 'message', 'Invoice successfully deleted');
            }
        }

        return $this->successresponse(404, 'message', 'Invoice not successfully deleted!');
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
            ->select('column_name', 'column_type','column_width')->orderBy('column_order')->where('is_deleted',0)->get();

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
            return $this->successresponse(404, 'invoice', 'No Records Found');
        }

        $invoice = DB::connection('dynamic_connection')->table('mng_col')->select($columnarray)
            ->where('invoice_id', $id)->where('is_deleted', 0)->where('is_active', 1)->get();

        $gstsettingsdetails = DB::connection('dynamic_connection')->table('invoices')
            ->select('gstsettings')->where('id', $id)
            ->get();

        if ($invoice->count() > 0) {
            return response()->json([
                'status' => 200,
                'invoice' => $invoice,
                'columns' => $columnarray,
                'othersettings' => $gstsettingsdetails,
                'columnswithtype' => $columnwithtypeArray
            ]);
        } else {
            return $this->successresponse(404, 'invoice', 'No Records Found');
        }
    }

    public function status(Request $request, string $id)
    {
        $invoices = DB::connection('dynamic_connection')->table('invoices')
            ->where('id', $id)
            ->update([
                'status' => $request->status
            ]);
        if ($invoices) {
            return $this->successresponse(200, 'message', 'status updated');
        } else {
            return $this->successresponse(404, 'message', 'invoice  status not succesfully updated!');
        }
    }


    public function reportlogsdetails(Request $request)
    {
        if ($this->rp['reportmodule']['report']['log'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $reports = DB::connection('dynamic_connection')->table('reportlogs')
            ->join($this->masterdbname . '.users', 'reportlogs.created_by', '=', $this->masterdbname . '.users.id')
            ->select('reportlogs.*', DB::raw("DATE_FORMAT(reportlogs.from_date, '%d-%m-%Y') as from_date_formatted"), DB::raw("DATE_FORMAT(reportlogs.to_date, '%d-%m-%Y') as to_date_formatted"), DB::raw("DATE_FORMAT(reportlogs.created_at, '%d-%m-%Y %h:%i:%s %p') as created_at_formatted"), 'users.firstname', 'users.lastname')
            ->where('reportlogs.is_deleted', 0)->orderBy('id', 'desc')->get();

        if ($reports->isNotEmpty()) {
            return $this->successresponse(200, 'reports', $reports);
        } else {
            return $this->successresponse(404, 'reports', 'No Records Found');
        }


    }
    public function reportlogdestroy(Request $request, string $id)
    {
        if ($this->rp['reportmodule']['report']['delete'] == 1) {
            $reportlog = DB::connection('dynamic_connection')
                ->table('reportlogs')
                ->where('id', $id)
                ->update(['is_deleted' => 1]);
        } else {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        if ($reportlog) {
            return $this->successresponse(200, 'message', 'reportlog succesfully deleted');

        } else {
            return $this->successresponse(404, 'message', 'No Such Log Found!');
        }
    }

    // check invoice number exist or not
    public function checkinvoicenumber(Request $request)
    {
        $existsinvoice = DB::connection('dynamic_connection')->table('invoices')->where('inv_no', $request->inv_number)->where('is_deleted', 0)->exists();
        if ($existsinvoice) {
            return $this->errorresponse(422, ["inv_number" => ['This number already exists!']]);
        }else{
            return true;
        }
    }
}
