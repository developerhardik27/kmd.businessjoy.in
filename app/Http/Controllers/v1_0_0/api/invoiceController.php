<?php

namespace App\Http\Controllers\v1_0_0\api;


use App\Models\company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class invoiceController extends commonController
{

    public $userId, $companyId, $masterdbname, $rp, $invoiceModel, $tbl_invoice_columnModel, $invoice_other_settingModel;

    public function __construct(Request $request)
    {
        if (session()->get('company_id')) {
            $this->dbname(session()->get('company_id'));
        } else {
            $this->dbname($request->company_id);
        }
        if (session()->get('user_id')) {
            $this->userId = session()->get('user_id');
        } else {
            $this->userId = $request->user_id;
        }

        $this->companyId = $request->company_id;
        $this->masterdbname = DB::connection()->getDatabaseName();

        $user_rp = DB::connection('dynamic_connection')->table('user_permissions')->select('rp')->where('user_id', $this->userId)->get();
        $permissions = json_decode($user_rp, true);
        $this->rp = json_decode($permissions[0]['rp'], true);

        $this->invoiceModel = $this->getmodel('invoice');
        $this->invoice_other_settingModel = $this->getmodel('invoice_other_setting');
        $this->tbl_invoice_columnModel = $this->getmodel('tbl_invoice_column');

    }

    // chart monthly invoice counting
    public function monthlyInvoiceChart(Request $request)
    {
        $invoices = DB::connection('dynamic_connection')->table('invoices')
            ->select(DB::raw("MONTH(created_at) as month, COUNT(*) as total_invoices, SUM(CASE WHEN status = 'paid' THEN 1 ELSE 0 END) as paid_invoices"))
            ->groupBy(DB::raw("MONTH(created_at)"))->where('created_by', $this->userId)
            ->get();

        return $invoices;
    }

    //status vise invoice list
    public function status_list(Request $request)
    {
        $currentMonth = Carbon::now()->format('Y-m');

        $invoices = DB::connection('dynamic_connection')->table('invoices')->whereYear('created_at', Carbon::now()->year)
            ->whereMonth('created_at', Carbon::now()->month)->where('created_by', $this->userId)->where('is_deleted',0)
            ->get();
        $groupedInvoices = $invoices->groupBy('status');
        return $groupedInvoices;
    }

    // currency list
    public function currency()
    {
        $currency = DB::table('currency')->orderBy('country')->get();

        if ($currency->count() > 0) {
            return response()->json([
                'status' => 200,
                'currency' => $currency
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'currency' => 'No Records Found'
            ], 404);
        }
    }
    //get bank details
    public function bdetails(Request $request)
    {
        $bank = DB::connection('dynamic_connection')->table('bank_details')->get()->where('is_active', 1)->where('is_deleted', 0);

        if ($bank->count() > 0) {
            return response()->json([
                'status' => 200,
                'bank' => $bank
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'bank' => 'No Records Found'
            ], 404);
        }
    }

    public function inv_list(Request $request)
    {

        if ($this->rp['invoicemodule']['invoice']['view'] != 1) {
            return response()->json([
                'status' => 500,
                'message' => 'You are Unauthorized'
            ]);
        }

        $invoiceres = DB::connection('dynamic_connection')->table('invoices')->join('customers', 'invoices.customer_id', '=', 'customers.id')
            ->join($this->masterdbname . '.country', 'customers.country_id', '=', $this->masterdbname . '.country.id')
            ->join($this->masterdbname . '.state', 'customers.state_id', '=', $this->masterdbname . '.state.id')
            ->join($this->masterdbname . '.city', 'customers.city_id', '=', $this->masterdbname . '.city.id')
            ->leftJoin('payment_details', function ($join) {
                $join->on('invoices.id', '=', 'payment_details.inv_id')
                    ->whereRaw('payment_details.id = (SELECT id FROM payment_details WHERE inv_id = invoices.id ORDER BY id DESC LIMIT 1)');
            })
            ->join($this->masterdbname . '.country as country_details', 'invoices.currency_id', '=', 'country_details.id')
            ->select('invoices.*', 'payment_details.part_payment','payment_details.pending_amount', 'customers.address', 'customers.firstname', 'customers.lastname', 'country.country_name','country_details.currency_symbol', 'state.state_name', 'city.city_name')
            ->where('invoices.is_deleted', 0)
            ->orderBy('invoices.id','desc');

        if ($this->rp['invoicemodule']['invoice']['alldata'] != 1) {
            $invoiceres->where('invoices.created_by', $this->userId);
        }
        $invoice = $invoiceres->get();

        if ($invoice->count() > 0) {
            return response()->json([
                'status' => 200,
                'invoice' => $invoice
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'invoice' => 'No Records Found'
            ]);
        }
    }



    //get dynamic column name
    public function columnname(Request $request)
    {

        if ($this->rp['invoicemodule']['invoice']['add'] != 1) {
            return response()->json([
                'status' => 500,
                'message' => 'You are Unauthorized'
            ]);
        }

        $columnname = DB::connection('dynamic_connection')->table('tbl_invoice_columns')->select('id', 'column_name', 'column_type', 'is_hide')->where('is_active', 1)->where('is_deleted', 0)->orderBy('column_order')->get();

        if ($columnname->count() > 0) {
            return response()->json([
                'status' => 200,
                'columnname' => $columnname
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'columnname' => 'No Records Found'
            ], 404);
        }
    }


    //get column name whose data type nubmer
    public function numbercolumnname(Request $request)
    {
        if ($this->rp['invoicemodule']['invoice']['add'] != 1) {
            return response()->json([
                'status' => 500,
                'message' => 'You are Unauthorized'
            ]);
        }

        $columnname = DB::connection('dynamic_connection')->table('tbl_invoice_columns')->select('column_name')->whereIn('column_type', ['number', 'decimal', 'percentage'])->where('is_active', 1)->where('is_deleted', 0)->get();

        if ($columnname->count() > 0) {
            return response()->json([
                'status' => 200,
                'columnname' => $columnname
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'columnname' => 'No Records Found'
            ], 404);
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index(string $id)
    {
        if ($this->rp['invoicemodule']['invoice']['view'] != 1) {
            return response()->json([
                'status' => 500,
                'message' => 'You are Unauthorized'
            ]);
        }

        $invoiceres = DB::connection('dynamic_connection')->table('invoices')
            ->join('customers', 'invoices.customer_id', '=', 'customers.id')
            ->join('mng_col', 'invoices.id', '=', 'mng_col.invoice_id')
            ->join($this->masterdbname . '.country', 'customers.country_id', '=', $this->masterdbname . '.country.id')
            ->join($this->masterdbname . '.state', 'customers.state_id', '=', $this->masterdbname . '.state.id')
            ->join($this->masterdbname . '.city', 'customers.city_id', '=', $this->masterdbname . '.city.id')
            ->leftjoin('invoice_terms_and_conditions', 'invoices.t_and_c_id', '=', 'invoice_terms_and_conditions.id')
            ->join($this->masterdbname . '.country as country_details', 'invoices.currency_id', '=', 'country_details.id')
            ->select('invoice_terms_and_conditions.t_and_c', 'invoices.id', 'invoices.inv_no', 'invoices.inv_date', 'invoices.notes', 'invoices.total', 'invoices.status', 'invoices.sgst', 'invoices.cgst', 'invoices.gst', 'invoices.grand_total', 'invoices.payment_type', 'invoices.is_active', 'invoices.is_deleted', 'customers.id as cid', 'customers.firstname', 'customers.lastname', 'customers.company_name', 'customers.email', 'customers.contact_no', 'customers.address', 'customers.pincode', 'customers.gst_no', 'country.country_name','country_details.currency','country_details.currency_symbol', 'state.state_name', 'city.city_name')
            ->groupBy('invoice_terms_and_conditions.t_and_c', 'invoices.id', 'invoices.inv_no', 'invoices.inv_date', 'invoices.notes', 'invoices.total', 'invoices.status', 'invoices.sgst', 'invoices.cgst', 'invoices.gst', 'invoices.grand_total', 'invoices.payment_type', 'invoices.is_active', 'invoices.is_deleted', 'customers.id', 'customers.firstname', 'customers.lastname', 'customers.company_name', 'customers.email', 'customers.contact_no', 'customers.address', 'customers.pincode', 'customers.gst_no', 'country.country_name','country_details.currency','country_details.currency_symbol', 'state.state_name', 'city.city_name', 'mng_col.invoice_id')
            ->where('invoices.is_active', 1)->where('invoices.is_deleted', 0)->where('invoices.id', $id);

        if ($this->rp['invoicemodule']['invoice']['alldata'] != 1) {
            $invoiceres->where('invoices.created_by', $this->userId);
        }

        $invoice = $invoiceres->get();
        if ($invoice->count() > 0) {
            return response()->json([
                'status' => 200,
                'invoice' => $invoice
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'invoice' => 'No Records Found'
            ]);
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

        $data = $request->data;
        $itemdata = $request->iteam_data;

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
            "user_id" ,
            'notes',
            'updated_by',
            'created_at',
            'updated_at',
            'is_active',
            'is_deleted'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->messages()
            ], 422);
        } else {

            if ($this->rp['invoicemodule']['invoice']['add'] != 1) {
                return response()->json([
                    'status' => 500,
                    'message' => 'You are Unauthorized'
                ]);
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

            $showcolumnstring = implode(',', $columnwithunderscore); // make coma separate string for hidden column

            // fetch last record from invoice tbl for generate dynamic inv no
            $lastrec = DB::connection('dynamic_connection')->table('invoices')->orderBy('id', 'desc')->first();

            $othersetting = $this->invoice_other_settingModel::find(1);

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


            if ($lastrec) {
                $lastinv_no = explode('-', $lastrec->inv_no);
                $lastinv_no_id = $lastinv_no[2];
                $inv_no = '';
                if ($data['country_id'] == 101) {
                    $inv_no = "IND-" . $year . "-" . ($lastinv_no_id + 1);
                } else {
                    $inv_no = "EXP-" . $year . "-" . ($lastinv_no_id + 1);
                }
            } else {
                if ($data['country_id'] == 101) {
                    $inv_no = "IND-" . $year . "-1";
                } else {
                    $inv_no = "EXP-" . $year . "-1";
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
                    'overdue_date' => $othersetting->overdue_day
                ];

                if(isset($data['gst'])){
                    $invoicerec['gst'] = $data['gst'];
                }else{
                    $invoicerec['sgst'] = $data['sgst'];
                    $invoicerec['cgst'] = $data['cgst'];
                }
                

                $tclastrec = DB::connection('dynamic_connection')->table('invoice_terms_and_conditions')->select('id')->where('is_deleted', 0)->where('is_active',1)->orderBy('id', 'desc')->first();
 

                if ($tclastrec) {
                    $invoicerec['t_and_c_id'] = $tclastrec->id;
                }

                $invoice = DB::connection('dynamic_connection')->table('invoices')->insertGetId( $invoicerec);

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
                        $mng_col = DB::connection('dynamic_connection')->table('mng_col')->insert($dynamicdata);
                    }

                    if ($mng_col) {
                        return response()->json([
                            'status' => 200,
                            'message' => 'invoice  succesfully created'
                        ]);
                    } else {
                        $id = $invoice;
                        $record = $this->invoiceModel::find($id);
                        // Check if the record exists
                        if ($record) {
                            // Delete the record
                            $record->delete();
                        }
                        return response()->json([
                            'status' => 500,
                            'message' => 'invoice details not succesfully created'
                        ]);
                    }
                } else {
                    return response()->json([
                        'status' => 500,
                        'message' => 'invoice not succesfully created'
                    ], 500);
                }
            } else {
                return response()->json([
                    'status' => 500,
                    'message' => 'company Details not found'
                ], 500);
            }


        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        if ($this->rp['invoicemodule']['invoice']['view'] != 1) {
            return response()->json([
                'status' => 500,
                'message' => 'You are Unauthorized'
            ]);
        }

        $invoice = DB::connection('dynamic_connection')->table('invoices')
            ->join('customers', 'invoices.customer_id', '=', 'customers.id')
            ->join('mng_col', 'invoices.id', '=', 'mng_col.invoice_id')
            ->join('products', 'mng_col.product_id', '=', 'products.id')
            ->select('invoices.*', 'customers.firstname', 'customers.lastname', 'mng_col.item_description', 'mng_col.price', 'products.price_per_unit')
            ->where('invoices.is_deleted', 0)->where('invoices.is_active', 1)->where('id', $id)->get();

        if ($this->rp['adminmodule']['user']['alldata'] != 1) {
            if ($invoice[0]->created_by != $this->userId) {
                return response()->json([
                    'status' => 500,
                    'message' => "You are Unauthorized!"
                ]);
            }
        }

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        if ($this->rp['invoicemodule']['invoice']['delete'] != 1) {
            return response()->json([
                'status' => 500,
                'message' => 'You are Unauthorized'
            ]);
        }

        $invoices = DB::connection('dynamic_connection')->table('invoices')
            ->where('id', $id)
            ->update([
                'is_deleted' => 1
            ]);
        if ($invoices) {
            $mng_col = DB::connection('dynamic_connection')->table('mng_col')
                ->where('invoice_id', $id)
                ->update([
                    'is_deleted' => 1
                ]);


            if ($mng_col) {
                return response()->json([
                    'status' => 200,
                    'message' => 'invoice succesfully deleted'
                ], 200);
            } else {
                return response()->json([
                    'status' => 404,
                    'message' => 'invoice not succesfully delete!'
                ]);
            }
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'invoice not succesfully delete!'
            ]);
        }
    }


    public function inv_details(string $id)
    {

        $columnname = $this->invoiceModel::find($id);
        $column = explode(',', $columnname->show_col);


        $columnarray = array_merge($column, ['amount']);

        $invoice = DB::connection('dynamic_connection')->table('mng_col')->select($columnarray)
            ->where('invoice_id', $id)->get();

        $othersettingsdetails = DB::connection('dynamic_connection')->table('invoice_other_settings')
                                ->select('sgst','cgst','gst')
                                ->get(); 

        if ($invoice->count() > 0) {
            return response()->json([
                'status' => 200,
                'invoice' => $invoice,
                'columns' => $columnarray,
                'othersettings' => $othersettingsdetails
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'invoice' => 'No Records Found'
            ]);
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
            return response()->json([
                'status' => 200,
                'message' => 'status updated'
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'invoice  status not succesfully updated!'
            ]);
        }
    }
}
