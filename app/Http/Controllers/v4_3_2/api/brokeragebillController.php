<?php

namespace App\Http\Controllers\v4_3_2\api;

use Carbon\Carbon;
use App\Models\company;
use Illuminate\Http\Request;
use App\Models\company_detail;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class brokeragebillController extends commonController
{
    public $version, $userId, $companyId, $masterdbname, $rp, $brokerpurchaseModel, $order_detailModel, $gradenModel, $brokerbillinvoiceModel;

    public function __construct(Request $request)
    {

        $this->companyId = $request->company_id;
        $this->userId = $request->user_id;

        $this->dbname($this->companyId);
        $user_rp = DB::connection('dynamic_connection')->table('user_permissions')->where('user_id', $this->userId)->value('rp');

        if (empty($user_rp)) {
            $this->customerrorresponse();
        }
        $this->version = 'v4_3_2';

        $this->rp = json_decode($user_rp, true);

        $this->masterdbname = DB::connection()->getDatabaseName();

        $this->brokerpurchaseModel = $this->getmodel('broker_purchase');
        $this->order_detailModel = $this->getmodel('order_detail');
        $this->gradenModel = $this->getmodel('graden');
        $this->brokerbillinvoiceModel = $this->getmodel('broker_bill_invoice');
    }
    public function getGardens()
    {
        if ($this->rp['teamodule']['brokeragebill']['view'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }
        $gardens = $this->order_detailModel
            ::join('gardens', 'gardens.id', '=', 'order_details.garden_id')
            ->where('order_details.is_deleted', 0)
            ->select(
                'gardens.id as garden_id',
                'gardens.garden_name as garden_name'
            )
            ->distinct()
            ->orderBy('gardens.garden_name', 'ASC')
            ->get();

        return $this->successresponse(200, 'data', $gardens);
    }


    public function getOtherData(Request $request)
    {
        if ($this->rp['teamodule']['brokeragebill']['view'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }
        $usedInvoices = $this->brokerpurchaseModel
            ::where('broker_purchases.is_deleted', 0)
            ->where('broker_purchases.garden_id', $request->garden_id)
            ->leftJoin('gardens', 'gardens.id', '=', 'broker_purchases.garden_id')
            ->leftJoin('grades', 'grades.id', '=', 'broker_purchases.grade')
            ->leftJoin('invoices', 'invoices.id', '=', 'broker_purchases.invoice_id')
            ->leftJoin('mng_col as mc', function ($join) {
                $join->on('mc.invoice_id', '=', 'invoices.id')
                    ->on('mc.Invoice_no', '=', 'broker_purchases.invoice_no');
            })
            ->select(
                'broker_purchases.*',
                'gardens.garden_name',
                'grades.grade',
                'invoices.inv_no',
                'invoices.inv_date',
                DB::raw("DATE_FORMAT(invoices.inv_date, '%d-%m-%Y') as inv_date"),
                DB::raw("DATE_FORMAT(broker_purchases.brokerage_date, '%d-%m-%Y') as brokerage_date"),
                // DB::raw("DATE_FORMAT(broker_bill_invoice.to_date, '%d-%m-%Y') as to_date"),
                'mc.Net_Weight_Kgs',
                'mc.shortage',
                'mc.amount',
            )
            ->get();

        return $this->successresponse(200, 'data', $usedInvoices);
    }
    public function getOtherDatanull(Request $request)
    {
        if ($this->rp['teamodule']['brokeragebill']['view'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }
        $usedInvoices = $this->brokerpurchaseModel
            ::where('broker_purchases.is_deleted', 0)
            ->where('broker_purchases.garden_id', $request->garden_id)
            ->leftJoin('gardens', 'gardens.id', '=', 'broker_purchases.garden_id')
            ->leftJoin('grades', 'grades.id', '=', 'broker_purchases.grade')
            ->leftJoin('invoices', 'invoices.id', '=', 'broker_purchases.invoice_id')
            ->leftJoin('mng_col as mc', function ($join) {
                $join->on('mc.invoice_id', '=', 'invoices.id')
                    ->on('mc.Invoice_no', '=', 'broker_purchases.invoice_no');
            })
            ->select(
                'broker_purchases.*',
                'gardens.garden_name',
                'grades.grade',
                'invoices.inv_no',
                'invoices.inv_date',
                'mc.Net_Weight_Kgs',
                'mc.shortage',
                'mc.amount',
            )
            ->where('broker_purchases.brokerage', '=', null)
            ->get();

        return $this->successresponse(200, 'data', $usedInvoices);
    }
    public function getOtherDatanotnull(Request $request)
    {
        if ($this->rp['teamodule']['brokeragebill']['view'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }
        $usedInvoices = $this->brokerpurchaseModel
            ::where('broker_purchases.is_deleted', 0)
            ->where('broker_purchases.garden_id', $request->garden_id)
            ->leftJoin('gardens', 'gardens.id', '=', 'broker_purchases.garden_id')
            ->leftJoin('grades', 'grades.id', '=', 'broker_purchases.grade')
            ->leftJoin('invoices', 'invoices.id', '=', 'broker_purchases.invoice_id')
            ->leftJoin('mng_col as mc', function ($join) {
                $join->on('mc.invoice_id', '=', 'invoices.id')
                    ->on('mc.Invoice_no', '=', 'broker_purchases.invoice_no');
            })
            ->select(
                'broker_purchases.*',
                'gardens.garden_name',
                'grades.grade',
                'invoices.inv_no',
                'invoices.inv_date',
                'mc.Net_Weight_Kgs',
                'mc.shortage',
                'mc.amount',
            )
            ->where('broker_purchases.brokerage', '!=', null)
            ->get();

        return $this->successresponse(200, 'data', $usedInvoices);
    }
    public function brokeragebillpdflist(Request $request)
    {
        if ($this->rp['teamodule']['brokeragebill']['view'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }
        $list = $this->brokerbillinvoiceModel
            ::leftJoin('broker_bill_payment_details', function ($join) {
                $join->on('broker_bill_invoice.id', '=', 'broker_bill_payment_details.inv_id')
                    ->whereRaw('broker_bill_payment_details.id = (SELECT id FROM broker_bill_payment_details WHERE inv_id = broker_bill_invoice.id and is_deleted = 0 ORDER BY id DESC LIMIT 1)');
            })
            ->leftJoin('companymasters', 'companymasters.id', '=', 'broker_bill_invoice.garden_company_id')
            ->where('broker_bill_invoice.is_deleted', 0);

        $filters = [
            'filter_payment_status' => 'broker_bill_invoice.status',
            'filter_garden' => 'broker_bill_invoice.garden_id',
            'filter_company' => 'broker_bill_invoice.garden_company_id',
        ];
        foreach ($filters as $requestKey => $column) {
            $value = $request->$requestKey ?? null;

            if ($value !== null) {
                if (in_array($requestKey, [
                    'filter_credit_days_from',
                    'filter_credit_days_to',
                    'filter_final_amount_from',
                    'filter_final_amount_to'
                ])) {
                    $operator = strpos($requestKey, 'from') !== false ? '>=' : '<=';
                    $list->where($column, $operator, $value);
                } else if (strpos($requestKey, 'from') !== false || strpos($requestKey, 'to') !== false) {
                    $operator = strpos($requestKey, 'from') !== false ? '>=' : '<=';
                    $list->whereDate($column, $operator, $value);
                } else {
                    $list->whereIn($column, (array)$value);
                }
            }
        }
        $data = $list
            ->select(
                'broker_bill_invoice.*',
                DB::raw("DATE_FORMAT(broker_bill_invoice.invoice_date, '%d-%m-%Y') as invoice_date"),
                DB::raw("DATE_FORMAT(broker_bill_invoice.from_date, '%d-%m-%Y') as from_date"),
                DB::raw("DATE_FORMAT(broker_bill_invoice.to_date, '%d-%m-%Y') as to_date"),
                'broker_bill_payment_details.id as paymentid',
                'broker_bill_payment_details.part_payment',
                'broker_bill_payment_details.pending_amount',
                'companymasters.company_name'
            )
            ->get();
        // dd($data);
        if ($data->isEmpty()) {
            return DataTables::of($data)
                ->with([
                    'status' => 404,
                    'message' => 'No Data Found',
                ])
                ->make(true);
        }
        return DataTables::of($data)
            ->with([
                'status' => 200,
            ])
            ->make(true);
    }
    public function index()
    {
        if ($this->rp['teamodule']['brokeragebill']['view'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }
        $brokerpurchase = $this->brokerpurchaseModel
            ::join('gardens', 'gardens.id', '=', 'broker_purchases.garden_id')
            ->leftjoin('broker_bill_invoice', 'broker_bill_invoice.garden_id', '=', 'broker_purchases.garden_id')
            ->select(
                'broker_purchases.garden_id',
                'gardens.garden_name',
                'broker_bill_invoice.garden_id as invoice_created',
                DB::raw('SUM(broker_purchases.bags) as total_bags'),
                DB::raw('SUM(broker_purchases.net_kg) as total_net_kg'),
                DB::raw('SUM(broker_purchases.brokerage) as total_brokerage')
            )
            ->where('broker_purchases.is_deleted', 0)
            ->where('broker_purchases.brokerage', '!=', 0)
            ->groupBy('broker_purchases.garden_id', 'gardens.garden_name', 'broker_bill_invoice.garden_id')
            ->get();
        // dd($brokerpurchase);
        if ($brokerpurchase->isEmpty()) {
            return DataTables::of($brokerpurchase)
                ->with([
                    'status' => 404,
                    'message' => 'No Data Found',
                ])
                ->make(true);
        }
        return DataTables::of($brokerpurchase)
            ->with([
                'status' => 200,
            ])
            ->make(true);
    }

    public function store(Request $request)
    {
        if ($this->rp['teamodule']['brokeragebill']['add'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }
        $data = $request->all();
        $rules = [
            'user_id' => 'required|integer',
            'form_type' => 'required|in:add,edit',
            'rows' => 'required|array|min:1',
        ];
        if ($data['form_type'] === 'edit') {
            $rules['rows.*.brokerage'] = 'required|numeric|min:0|max:100';
            $rules['rows.*.brokerage_date'] = 'required|date';
        } else {
            $rules['rows.*.brokerage'] = 'nullable|numeric|min:0|max:100';
            $rules['rows.*.brokerage_date'] = 'nullable|date';
        }

        $validator = Validator::make($data, $rules, [
            'rows.*.brokerage.required' => 'Brokerage is required.',
            'rows.*.brokerage.numeric' => 'Brokerage must be a number.',
            'rows.*.brokerage.min' => 'Brokerage must be at least 0.',
            'rows.*.brokerage.max' => 'Brokerage must not be greater than 100.',
            'rows.*.brokerage_date.required' => 'Brokerage date is required.',
            'rows.*.brokerage_date.date' => 'Brokerage date must be a valid date.',
        ]);

        if ($validator->fails()) {
            return $this->errorresponse(422, $validator->messages());
        }

        $updated = false;

        foreach ($request->rows as $row) {

            // if ($request->form_type === 'add' && $row['brokerage'] === null) {
            //     continue;
            // }
            if ($request->form_type === 'add') {
                $brokerage = $row['brokerage'] ?? null;
                $brokerage_date = $row['brokerage'] ?? null ? $row['brokerage_date'] : null;
            } else {
                // edit mode: all required, already validated
                $brokerage = $row['brokerage'];
                $brokerage_date = $row['brokerage_date'];
            }
            $result = $this->brokerpurchaseModel
                ::where('id', $row['id'])
                ->update([
                    'brokerage'      => $brokerage,
                    'brokerage_date' => $brokerage_date,
                    'updated_by'     => $request->user_id,
                ]);

            if ($result) {
                $updated = true;
            }
        }

        if ($updated) {
            return $this->successresponse(200, 'message', 'Broker Bill successfully');
        }
        return $this->successresponse(500, 'message', 'No brokerage data to update');
    }

    public function edit($id)
    {
        if ($this->rp['teamodule']['brokeragebill']['edit'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }
        $brokerpurchase = $this->brokerpurchaseModel::find($id);
        if ($this->rp['teamodule']['brokeragebill']['alldata'] != 1) {
            if ($brokerpurchase->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }
        if (!$brokerpurchase) {
            return $this->successresponse(500, 'message', 'Broker Purchase not found !');
        }
        return $this->successresponse(200, 'brokerpurchase', $brokerpurchase);
    }

    public function destroy($id)
    {
        if ($this->rp['teamodule']['brokerpurchase']['delete'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }
        $brokerpurchase = $this->brokerpurchaseModel::find($id);
        if ($this->rp['teamodule']['brokerpurchase']['alldata'] != 1) {
            if ($brokerpurchase->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }
        if (!$brokerpurchase) {
            return $this->successresponse(500, 'message', 'Commission Bill not found !');
        }
        $brokerpurchase->update(
            [
                "is_deleted" => 1
            ]
        );

        return $this->successresponse(200, 'message', 'Commission Bill succesfully deleted');
    }

    public function brokeragebillpdf(Request $request)
    {
        // dd($request->all());
        $company_id = $request->company_id;
        $bankdetailsController = "App\\Http\\Controllers\\" . $this->version . "\\api\\bankdetailsController";
        $jsonbankdetails = app($bankdetailsController)->bank_details($company_id);
        $bdetailscontent = $jsonbankdetails->getContent();
        $bdetails = json_decode($bdetailscontent);
        if ($bdetails->status != 200) {
            return $this->successresponse(500, 'message', 'No bank details are available for your company. Please add bank details to continue.');
        }

        $dbname = company::find($request->company_id);
        $mainCompanyData = company_detail::where('company_details.id', $dbname->company_details_id)
            ->join('country', 'country.id', '=', 'company_details.country_id')
            ->join('state', 'state.id', '=', 'company_details.state_id')
            ->join('city', 'city.id', '=', 'company_details.city_id')
            ->select(
                'company_details.*',
                'country.country_name as country_name',
                'state.state_name as state_name',
                'city.city_name as city_name'
            )
            ->first();

        config(['database.connections.dynamic_connection.database' => $dbname->dbname]);

        // Establish connection to the dynamic database
        DB::purge('dynamic_connection');
        DB::reconnect('dynamic_connection');
        $garden_id_array = explode(",",$request->garden_id);
        // dd($garden_id_array[0]);
        $gardenCompanyData = $this->brokerpurchaseModel
            ::where('broker_purchases.is_deleted', 0)
            ->where('broker_purchases.garden_id', $garden_id_array[0])
            ->leftJoin('company_garden', 'company_garden.garden_id', '=', 'broker_purchases.garden_id')
            ->leftJoin('companymasters', 'companymasters.id', '=', 'company_garden.company_id')
            ->select(
                'company_garden.company_id as garden_company_id',
                'companymasters.*',
            )
            ->first();
        $usedInvoices = $this->brokerpurchaseModel
            ::where('broker_purchases.is_deleted', 0)
            ->whereIn('broker_purchases.garden_id', [$request->garden_id])
            ->leftJoin('gardens', 'gardens.id', '=', 'broker_purchases.garden_id')
            ->leftJoin('grades', 'grades.id', '=', 'broker_purchases.grade')
            ->select(
                'broker_purchases.*',
                'gardens.garden_name as garden_name',
                'grades.grade as grade'
            )
            ->where('broker_purchases.invoice_id', $request->invoice_id)
            ->get();
        // dd($usedInvoices);
        if ($usedInvoices->isEmpty()) {
            return $this->successresponse(500, 'message', 'Brokrage not genrated selected date');
        }
        $data = [
            "mainCompanyData" => $mainCompanyData,
            "gardenCompanyData" => $gardenCompanyData,
            "usedInvoices" => $usedInvoices
        ];

        $totalAmount = 0;
        $linedata = $data['usedInvoices'];
      
       
        $brokrage = $request->line_total * $request->brokerage / 100;
        $totalAmount = $totalAmount + $brokrage;
          
        $company_state_id = $data['mainCompanyData']['state_id'];
        $garden_company_state_id  = $data['gardenCompanyData']['state_id'];
        $igst = $cgst = $sgst = 0;
        
        if ($company_state_id === $garden_company_state_id) {
            $cgst = $totalAmount * 9 / 100;
            $sgst = $totalAmount * 9 / 100;
            $igst = 0;
        } else {
            $igst = $totalAmount * 18 / 100;
            $cgst = 0;
            $sgst = 0;
        }
       
        $grandTotal = round($totalAmount + $igst + $cgst + $sgst);
        // dd($grandTotal);
        $company_id = $data['mainCompanyData']['company_id'];
        $garden_company_id  = $data['gardenCompanyData']['id'];
        $pandding_Amt = 0;
        $today = Carbon::now();

        // Calculate Financial Year
        if ($today->month >= 4) {
            $fyStart = $today->format('y');
            $fyEnd   = $today->copy()->addYear()->format('y');
        } else {
            $fyStart = $today->copy()->subYear()->format('y');
            $fyEnd   = $today->format('y');
        }

        $financialYear = $fyStart . '-' . $fyEnd;
    
        $data = $this->brokerbillinvoiceModel::create([
            'garden_id' => 0,
            'company_id' => $company_id,
            'garden_company_id' => $garden_company_id,
            'totalamount' => $totalAmount,
            'igst' => $igst,
            'sgst' => $sgst,
            'cgst' => $cgst,
            'grand_total' => $grandTotal,
            'status' => "pending",
            'invoice_date' => $today->format('Y-m-d'),
            'created_by' => $request->user_id,
            'from_date' => Carbon::yesterday()->format('Y-m-d'),
            'to_date'   => Carbon::tomorrow()->format('Y-m-d'),
        ]);
        $create = $this->brokerbillinvoiceModel
            ::where('id', $data->id)
            ->update([
                'invoice_no'   => "KMD/{$financialYear}/{$data->id}",
            ]);

        $brokrage_per = $request->brokerage;
       
        foreach ($linedata as $invoice) {
            $brokrage_invoice = $invoice->invoice_id;  // use ->id for model instance
            //  dd($brokrage_invoice);
            //  dd($brokrage_per);
            $update =  $this->brokerpurchaseModel::where('invoice_id', $brokrage_invoice)->update([
                'brokerbill_no' => $data->id,
                'brokerage' => $brokrage_per,
                'brokerage_date' => $today->format('Y-m-d'),
            ]);
        }

        if ($create) {
            return $this->successresponse(200, 'message', 'Commission Bill Pdf  succesfully Created');
        } else {
            return $this->successresponse(500, 'message', 'Commission Bill Pdf not succesfully Created !');
        }
    }
    public function getpanddingpayment($id)
    {
        if ($this->rp['teamodule']['brokeragebill']['edit'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }
        $payment = $this->brokerbillinvoiceModel::find($id);

        if ($this->rp['teamodule']['brokeragebill']['alldata'] != 1) {
            if ($payment->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }
        if (!$payment) {
            return $this->successresponse(500, 'message', 'payment not found !');
        }
        return $this->successresponse(200, 'payment', $payment);
    }
}
