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
    public $userId, $companyId, $masterdbname, $rp, $brokerpurchaseModel, $order_detailModel, $gradenModel, $brokerbillinvoiceModel;

    public function __construct(Request $request)
    {

        $this->companyId = $request->company_id;
        $this->userId = $request->user_id;

        $this->dbname($this->companyId);
        $user_rp = DB::connection('dynamic_connection')->table('user_permissions')->where('user_id', $this->userId)->value('rp');

        if (empty($user_rp)) {
            $this->customerrorresponse();
        }

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
    public function brokeragebillpdflist()
    {
        if ($this->rp['teamodule']['brokeragebill']['view'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }
        $list = $this->brokerbillinvoiceModel
            ::leftJoin('broker_bill_payment_details', function ($join) {
                $join->on('broker_bill_invoice.id', '=', 'broker_bill_payment_details.inv_id')
                    ->whereRaw('broker_bill_payment_details.id = (SELECT id FROM broker_bill_payment_details WHERE inv_id = broker_bill_invoice.id and is_deleted = 0 ORDER BY id DESC LIMIT 1)');
            })
            ->where('broker_bill_invoice.is_deleted', 0)
            ->select(
                'broker_bill_invoice.*',
                'broker_bill_payment_details.id as paymentid',
                'broker_bill_payment_details.part_payment',
                'broker_bill_payment_details.pending_amount'
            )
            ->get();
        // dd($list);
        if ($list->isEmpty()) {
            return DataTables::of($list)
                ->with([
                    'status' => 404,
                    'message' => 'No Data Found',
                ])
                ->make(true);
        }
        return DataTables::of($list)
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
        $updated = false;

        foreach ($request->rows as $row) {

            if ($row['brokerage'] === null) {
                continue;
            }

            $result = $this->brokerpurchaseModel
                ::where('id', $row['id'])
                ->update([
                    'brokerage'      => $row['brokerage'],
                    'brokerage_date' => $row['brokerage_date'],
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
            return $this->successresponse(500, 'message', 'Broker Purchase not found !');
        }
        $brokerpurchase->update(
            [
                "is_deleted" => 1
            ]
        );

        return $this->successresponse(200, 'message', 'Broker Purchase succesfully deleted');
    }

    public function brokeragebillpdf(Request $request)
    {

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

        $gardenCompanyData = $this->brokerpurchaseModel
            ::where('broker_purchases.is_deleted', 0)
            ->where('broker_purchases.garden_id', $request->garden_id)
            ->leftJoin('company_garden', 'company_garden.garden_id', '=', 'broker_purchases.garden_id')
            ->leftJoin('companymasters', 'companymasters.id', '=', 'company_garden.company_id')
            ->select(
                'company_garden.company_id as garden_company_id',
                'companymasters.*',
            )
            ->first();
        $usedInvoices = $this->brokerpurchaseModel
            ::where('broker_purchases.is_deleted', 0)
            ->where('broker_purchases.garden_id', $request->garden_id)
            ->leftJoin('gardens', 'gardens.id', '=', 'broker_purchases.garden_id')
            ->leftJoin('grades', 'grades.id', '=', 'broker_purchases.grade')
            ->select(
                'broker_purchases.*',
                'gardens.garden_name as garden_name',
                'grades.grade as grade'
            )
            ->whereBetween('broker_purchases.brokerage_date', [$request->from_date, $request->to_date])
            ->get();
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
        foreach ($linedata as $invoice) {
            $brokrage = $invoice['net_kg'] * $invoice['rate'] * $invoice['brokerage'] / 100;
            $totalAmount += $brokrage;
        }
        $igst = $totalAmount * 18 / 100;
        $grandTotal = round($totalAmount - $igst);
        $garden_id = $data['usedInvoices'][0]['garden_id'];
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
            'garden_id' => $garden_id,
            'company_id' => $company_id,
            'garden_company_id' => $garden_company_id,
            'totalamount' => $totalAmount,
            'igst' => $igst,
            'grand_total' => $grandTotal,
            'status' => "pending",
            'invoice_date' => $today->format('Y-m-d'),
            'created_by' => $request->user_id,
            'from_date' => $request->from_date,
            'to_date' => $request->to_date,
        ]);
        $create = $this->brokerbillinvoiceModel
            ::where('id', $data->id)
            ->update([
                'invoice_no'   => "KMD/{$data->id}/{$financialYear}",
            ]);


        foreach ($linedata as $invoice) {
            $brokrage = $invoice->id;  // use ->id for model instance

            $update =  $this->brokerpurchaseModel::where('id', $brokrage)->update([
                'brokerbill_no' => $data->id,
            ]);
        }

        if ($create) {
            return $this->successresponse(200, 'message', 'Broker Bill Pdf  succesfully Created');
        } else {
            return $this->successresponse(500, 'message', 'Broker Bill Pdf not succesfully Created !');
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
