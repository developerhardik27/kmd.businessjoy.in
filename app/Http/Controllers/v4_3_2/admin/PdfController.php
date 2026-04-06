<?php

namespace App\Http\Controllers\v4_3_2\admin;

use Exception;

use ZipArchive;
use App\Models\company;
use App\Models\company_detail;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

use Barryvdh\DomPDF\Facade\Pdf;
// use Mpdf\Config\ConfigVariables;
// use Mpdf\Config\FontVariables;
// use Mpdf\Mpdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\v4_3_2\api\commonController;
use Dompdf\Options;

class PdfController extends commonController
{
   public $version, $masterdbname, $invoiceModel, $paymentdetailsModel, $quotationModel, $consignor_copyModel, $brokerpurchaseModel, $bank_detailsModel, $broker_bill_invoiceModel, $broker_payment_detailsModel, $company_gardenModel, $orderModel, $order_detailModel, $brokerbillinvoiceModel, $companymasterModel, $partyModel;
   public function __construct()
   {
      if (session_status() !== PHP_SESSION_ACTIVE)
         session_start();
      if (isset($_SESSION['folder_name'])) {
         $this->version = $_SESSION['folder_name'];
      } else {
         $this->version = "v4_3_2";
      }
      $this->invoiceModel = 'App\\Models\\' . $this->version . "\\invoice";
      $this->paymentdetailsModel = 'App\\Models\\' . $this->version . "\\payment_details";
      $this->quotationModel = 'App\\Models\\' . $this->version . "\\quotation";
      $this->consignor_copyModel = 'App\\Models\\' . $this->version . "\\consignor_copy";
      $this->brokerpurchaseModel = 'App\\Models\\' . $this->version . "\\broker_purchase";
      $this->bank_detailsModel = 'App\\Models\\' . $this->version . "\\bank_detail";
      $this->broker_bill_invoiceModel = 'App\\Models\\' . $this->version . "\\broker_bill_invoice";
      $this->broker_payment_detailsModel = 'App\\Models\\' . $this->version . "\\broker_bill_payment_detail";
      $this->company_gardenModel = 'App\\Models\\' . $this->version . "\\company_garden";
      $this->orderModel = 'App\\Models\\' . $this->version . "\\order";
      $this->order_detailModel = 'App\\Models\\' . $this->version . "\\order_detail";
      $this->brokerbillinvoiceModel = 'App\\Models\\' . $this->version . "\\broker_bill_invoice";
      $this->companymasterModel = 'App\\Models\\' . $this->version . "\\companymaster";
      $this->partyModel = 'App\\Models\\' . $this->version . "\\party";

      $this->masterdbname = DB::connection()->getDatabaseName();
   }



   //this for testing
   public function generatepdf(string $id)
   {

      $dbname = company::find(Session::get('company_id'));
      config(['database.connections.dynamic_connection.database' => $dbname->dbname]);

      // Establish connection to the dynamic database
      DB::purge('dynamic_connection');
      DB::reconnect('dynamic_connection');

      $invoice = $this->invoiceModel::findOrFail($id);

      $this->authorize('view', $invoice);

      $data = $this->prepareDataForPDF($invoice);

      $options = [
         'isPhpEnabled' => true,
         'isHtml5ParserEnabled' => true,
         'isRemoteEnabled' => true,
         'margin_top' => 0,
         'margin_right' => 0,
         'margin_bottom' => 0,
         'margin_left' => 0,
         'defaultFont' => 'Helvetica'
      ];

      // dd($data);
      $companyname = $data['invdata']['name']; // if customer company name is not set
      if ($data['invdata']['name'] != '') {
         $companyname = $data['invdata']['name'];
      }

      // return view($this->version . '.admin.PDF.invoicetemplate', $data);
      $pdfname = $data['invdata']['inv_no'] . ' ' . $companyname . ' ' . date('d-M-y') . '.pdf';

      $pdf = PDF::setOptions($options)->loadView($this->version . '.admin.PDF.invoicetemplate', $data)->setPaper('a4', 'portrait');

      return $pdf->stream($pdfname);
   }
   public function generatebrokragebillpdf(string $id)
   {
      $dbname = company::find(Session::get('company_id'));
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
      $invoice = $this->broker_bill_invoiceModel::findOrFail($id);

      $gardenCompanyData = $this->companymasterModel
         ::where('companymasters.is_deleted', 0)->where('companymasters.id', $invoice->garden_company_id)
         ->leftjoin($this->masterdbname . '.country', 'companymasters.country_id', '=', $this->masterdbname . '.country.id')
         ->leftjoin($this->masterdbname . '.state', 'companymasters.state_id', '=', $this->masterdbname . '.state.id')
         ->leftjoin($this->masterdbname . '.city', 'companymasters.city_id', '=', $this->masterdbname . '.city.id')
         ->select(
            'companymasters.*',
            'country.country_name as country_name',
            'state.state_name as state_name',
            'city.city_name as city_name'
         )
         ->first();
      // dd($gardenCompanyData);
      $bank_details  = $this->bank_detailsModel::first();
      // dd($invoice->id);
    $usedInvoices = $this->brokerpurchaseModel
      ::leftJoin('gardens', 'gardens.id', '=', 'broker_purchases.garden_id')
      ->leftJoin('grades', 'grades.id', '=', 'broker_purchases.grade')
      ->join('order_details', function ($join) {
         $join->on('order_details.id', '=', 'broker_purchases.order_detail_id');
      })
      ->leftJoin('company_garden', 'company_garden.garden_id', '=', 'broker_purchases.garden_id')
      ->leftJoin('companymasters', 'companymasters.id', '=', 'company_garden.company_id')
      ->join('orders', 'orders.id', '=', 'order_details.order_id')
      ->leftJoin('partys as buyer', 'buyer.id', '=', 'orders.buyer_party')
      ->leftJoin('partys as transporter', 'transporter.id', '=', 'orders.transport')
      ->leftJoin('invoices', function ($join) {
         $join->on('invoices.id', '=', 'broker_purchases.invoice_id')
               ->whereRaw('FIND_IN_SET(broker_purchases.id, REPLACE(invoices.sample_ids, \'"\' , \'\'))')
               ->where('invoices.is_deleted', 0);
      })
      ->where('broker_purchases.is_deleted', 0)
      ->where('broker_purchases.brokerbill_no', $invoice->id)
      ->select(
         'broker_purchases.*',
         'gardens.garden_name as garden_name',
         'grades.grade as grade',
         'orders.buyer_party',
         'orders.discount',
         'buyer.name as buyer_name',          // ← add this!
         'invoices.inv_no',
         'invoices.inv_date',
         DB::raw("DATE_FORMAT(invoices.inv_date, '%d-%m-%Y') as inv_date"),
         'invoices.sample_ids',
         'invoices.consignment_number',
         'invoices.consignment_date',
         'companymasters.company_name'
      )
      ->get()
      ->groupBy('invoice_id')
      ->map(function ($rows, $invoiceId) {
         $gardenNames = $rows->pluck('garden_name')->unique()->implode(', ');

         $totalBags = $rows->sum('bags');
         $totalNetKg = $rows->sum('net_kg');
         // $totalDiscount = $rows->sum('discount');
         $totalInvoice = $rows->sum('invoice_grand_total');
         $totalBrokerage = $rows->sum(fn($row) => (($row->invoice_grand_total ?? 0) * ($row->brokerage ?? 0)) / 100);

         $first = $rows->first();

         return [
               'invoice_id' => $invoiceId,
               'inv_no' => $first->inv_no,
               'inv_date' => $first->inv_date,
               'garden_names' => $gardenNames,
               'bags' => $totalBags,
               'net_kg' => $totalNetKg,
               'discount' => $first->discount,
               'invoice_grand_total' => $totalInvoice,
               'brokerage_total' => $totalBrokerage,
               'buyer_name' => $first->buyer_name,  // ← now this will work
               'company_name' => $first->company_name,
               'brokerage'=>$first->brokerage,
         ];
      });
      // dd($usedInvoices);
      $data = [
         "mainCompanyData" => $mainCompanyData,
         "gardenCompanyData" => $gardenCompanyData,
         "usedInvoices" => $usedInvoices,
         "bank_details" => $bank_details,
         "invoice" => $invoice,
      ];

      $options = [
         'isPhpEnabled' => true,
         'isHtml5ParserEnabled' => true,
         'isRemoteEnabled' => true,
         'margin_top' => 0,
         'margin_right' => 0,
         'margin_bottom' => 0,
         'margin_left' => 0,
         'defaultFont' => 'Helvetica'
      ];

      $companyname = $data['mainCompanyData']['name']; // if customer company name is not set
      if ($data['mainCompanyData']['name'] != '') {
         $companyname = $data['mainCompanyData']['name'];
      }
      $gardencompanyname = $data['gardenCompanyData']['company_name'];
      // return view($this->version . '.admin.PDF.brokragebilltemplate', $data);
      $pdfname = $gardencompanyname . ' ' . $companyname . ' ' . date('d-M-y') . '.pdf';

      $pdf = PDF::setOptions($options)->loadView($this->version . '.admin.PDF.brokragebilltemplate', ["data" => $data])->setPaper('a4', 'portrait');
      return $pdf->stream($pdfname);
   }
   public function brokerBillgeneraterecieptall(string $id)
   {

      $dbname = company::find(Session::get('company_id'));
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
      $invoice = $this->broker_bill_invoiceModel::findOrFail($id);
      $gardenCompanyData = $this->companymasterModel
         ::where('companymasters.is_deleted', 0)->where('companymasters.id', $invoice->garden_company_id)
         ->leftjoin($this->masterdbname . '.country', 'companymasters.country_id', '=', $this->masterdbname . '.country.id')
         ->leftjoin($this->masterdbname . '.state', 'companymasters.state_id', '=', $this->masterdbname . '.state.id')
         ->leftjoin($this->masterdbname . '.city', 'companymasters.city_id', '=', $this->masterdbname . '.city.id')
         ->select(
            'companymasters.*',
            'country.country_name as country_name',
            'state.state_name as state_name',
            'city.city_name as city_name'
         )
         ->first();
      $bank_details  = $this->bank_detailsModel::first();
      $paymentdetail = $this->broker_payment_detailsModel::where('inv_id', $id)->where('is_deleted', 0)->get();
       $usedInvoices = $this->brokerpurchaseModel
      ::leftJoin('gardens', 'gardens.id', '=', 'broker_purchases.garden_id')
      ->leftJoin('grades', 'grades.id', '=', 'broker_purchases.grade')
      ->join('order_details', function ($join) {
         $join->on('order_details.id', '=', 'broker_purchases.order_detail_id');
      })
      ->leftJoin('company_garden', 'company_garden.garden_id', '=', 'broker_purchases.garden_id')
      ->leftJoin('companymasters', 'companymasters.id', '=', 'company_garden.company_id')
      ->join('orders', 'orders.id', '=', 'order_details.order_id')
      ->leftJoin('partys as buyer', 'buyer.id', '=', 'orders.buyer_party')
      ->leftJoin('partys as transporter', 'transporter.id', '=', 'orders.transport')
      ->leftJoin('invoices', function ($join) {
         $join->on('invoices.id', '=', 'broker_purchases.invoice_id')
               ->whereRaw('FIND_IN_SET(broker_purchases.id, REPLACE(invoices.sample_ids, \'"\' , \'\'))')
               ->where('invoices.is_deleted', 0);
      })
      ->where('broker_purchases.is_deleted', 0)
      ->where('broker_purchases.brokerbill_no', $invoice->id)
      ->select(
         'broker_purchases.*',
         'gardens.garden_name as garden_name',
         'grades.grade as grade',
         'orders.buyer_party',
         'orders.discount',
         'buyer.name as buyer_name',          // ← add this!
         'invoices.inv_no',
         'invoices.inv_date',
         DB::raw("DATE_FORMAT(invoices.inv_date, '%d-%m-%Y') as inv_date"),
         'invoices.sample_ids',
         'invoices.consignment_number',
         'invoices.consignment_date',
         'companymasters.company_name'
      )
      ->get()
      ->groupBy('invoice_id')
      ->map(function ($rows, $invoiceId) {
         $gardenNames = $rows->pluck('garden_name')->unique()->implode(', ');

         $totalBags = $rows->sum('bags');
         $totalNetKg = $rows->sum('net_kg');
         // $totalDiscount = $rows->sum('discount');
         $totalInvoice = $rows->sum('invoice_grand_total');
         $totalBrokerage = $rows->sum(fn($row) => (($row->invoice_grand_total ?? 0) * ($row->brokerage ?? 0)) / 100);

         $first = $rows->first();

         return [
               'invoice_id' => $invoiceId,
               'inv_no' => $first->inv_no,
               'inv_date' => $first->inv_date,
               'garden_names' => $gardenNames,
               'bags' => $totalBags,
               'net_kg' => $totalNetKg,
               'discount' => $first->discount,
               'invoice_grand_total' => $totalInvoice,
               'brokerage_total' => $totalBrokerage,
               'buyer_name' => $first->buyer_name,  // ← now this will work
               'company_name' => $first->company_name,
               'brokerage'=>$first->brokerage,
         ];
      });
      $data = [
         "mainCompanyData" => $mainCompanyData,
         "gardenCompanyData" => $gardenCompanyData,
         "usedInvoices" => $usedInvoices,
         "bank_details" => $bank_details,
         "invoice" => $invoice,
         'paymentdetail' => $paymentdetail,
      ];

      $options = [
         'isPhpEnabled' => true,
         'isHtml5ParserEnabled' => true,
         'margin_top' => 0,
         'margin_right' => 0,
         'margin_bottom' => 0,
         'margin_left' => 0,
      ];

      $pdf = PDF::setOptions($options)->loadView($this->version . '.admin.PDF.brokrageBillpaymentpaidrecieptall', ["data" => $data])->setPaper('a4', 'portrait');

      $name = 'Receipt ' . $data['paymentdetail'][0]['receipt_number'] . '.pdf';

      if (count($data['paymentdetail']) > 1) {
         $name = 'PaymentHistory ' . $data['invoice']['inv_no'] . '.pdf';
      }

      // return view($this->version . '.admin.brokrageBillpaymentpaidreciept', $data);
      return $pdf->stream($name);
   }

   public function brokerBillgeneratereciept(string $id)
   {

      $dbname = company::find(Session::get('company_id'));
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
      $paymentdetail = $this->broker_payment_detailsModel::findOrFail($id);

      $invoice = $this->broker_bill_invoiceModel::findOrFail($paymentdetail->inv_id);

      $gardenCompanyData = $this->companymasterModel
         ::where('companymasters.is_deleted', 0)->where('companymasters.id', $invoice->garden_company_id)
         ->leftjoin($this->masterdbname . '.country', 'companymasters.country_id', '=', $this->masterdbname . '.country.id')
         ->leftjoin($this->masterdbname . '.state', 'companymasters.state_id', '=', $this->masterdbname . '.state.id')
         ->leftjoin($this->masterdbname . '.city', 'companymasters.city_id', '=', $this->masterdbname . '.city.id')
         ->select(
            'companymasters.*',
            'country.country_name as country_name',
            'state.state_name as state_name',
            'city.city_name as city_name'
         )
         ->first();
      $bank_details  = $this->bank_detailsModel::first();
      $paymentdetail = $this->broker_payment_detailsModel::where('id', $id)->where('is_deleted', 0)->get();
      // dd($invoice->from_date, $invoice->to_date);
       $usedInvoices = $this->brokerpurchaseModel
      ::leftJoin('gardens', 'gardens.id', '=', 'broker_purchases.garden_id')
      ->leftJoin('grades', 'grades.id', '=', 'broker_purchases.grade')
      ->join('order_details', function ($join) {
         $join->on('order_details.id', '=', 'broker_purchases.order_detail_id');
      })
      ->leftJoin('company_garden', 'company_garden.garden_id', '=', 'broker_purchases.garden_id')
      ->leftJoin('companymasters', 'companymasters.id', '=', 'company_garden.company_id')
      ->join('orders', 'orders.id', '=', 'order_details.order_id')
      ->leftJoin('partys as buyer', 'buyer.id', '=', 'orders.buyer_party')
      ->leftJoin('partys as transporter', 'transporter.id', '=', 'orders.transport')
      ->leftJoin('invoices', function ($join) {
         $join->on('invoices.id', '=', 'broker_purchases.invoice_id')
               ->whereRaw('FIND_IN_SET(broker_purchases.id, REPLACE(invoices.sample_ids, \'"\' , \'\'))')
               ->where('invoices.is_deleted', 0);
      })
      ->where('broker_purchases.is_deleted', 0)
      ->where('broker_purchases.brokerbill_no', $invoice->id)
      ->select(
         'broker_purchases.*',
         'gardens.garden_name as garden_name',
         'grades.grade as grade',
         'orders.buyer_party',
         'orders.discount',
         'buyer.name as buyer_name',          // ← add this!
         'invoices.inv_no',
         'invoices.inv_date',
         DB::raw("DATE_FORMAT(invoices.inv_date, '%d-%m-%Y') as inv_date"),
         'invoices.sample_ids',
         'invoices.consignment_number',
         'invoices.consignment_date',
         'companymasters.company_name'
      )
      ->get()
      ->groupBy('invoice_id')
      ->map(function ($rows, $invoiceId) {
         $gardenNames = $rows->pluck('garden_name')->unique()->implode(', ');

         $totalBags = $rows->sum('bags');
         $totalNetKg = $rows->sum('net_kg');
         // $totalDiscount = $rows->sum('discount');
         $totalInvoice = $rows->sum('invoice_grand_total');
         $totalBrokerage = $rows->sum(fn($row) => (($row->invoice_grand_total ?? 0) * ($row->brokerage ?? 0)) / 100);

         $first = $rows->first();

         return [
               'invoice_id' => $invoiceId,
               'inv_no' => $first->inv_no,
               'inv_date' => $first->inv_date,
               'garden_names' => $gardenNames,
               'bags' => $totalBags,
               'net_kg' => $totalNetKg,
               'discount' => $first->discount,
               'invoice_grand_total' => $totalInvoice,
               'brokerage_total' => $totalBrokerage,
               'buyer_name' => $first->buyer_name,  // ← now this will work
               'company_name' => $first->company_name,
               'brokerage'=>$first->brokerage,
         ];
      });
      $data = [
         "mainCompanyData" => $mainCompanyData,
         "gardenCompanyData" => $gardenCompanyData,
         "usedInvoices" => $usedInvoices,
         "bank_details" => $bank_details,
         "invoice" => $invoice,
         'paymentdetail' => $paymentdetail,
      ];
      //  dd($data);
      $options = [
         'isPhpEnabled' => true,
         'isHtml5ParserEnabled' => true,
         'margin_top' => 0,
         'margin_right' => 0,
         'margin_bottom' => 0,
         'margin_left' => 0,
         'defaultFont' => 'Helvetica'
      ];

      //return view($this->version . '.admin.PDF.brokrageBillpaymentpaidreciept', $data);
      $pdf = PDF::setOptions($options)->loadView($this->version . '.admin.PDF.brokrageBillpaymentpaidreciept', ["data" => $data])->setPaper('a4', 'portrait');

      $name = 'Receipt ' . $data['paymentdetail'][0]['receipt_number'] . '.pdf';
      // return view($this->version . '.admin.brokrageBillpaymentpaidreciept', $data);
      return $pdf->stream($name);
   }
   // generate part partpayment single receipt (id is considering payment details id)
   public function generatereciept(string $id)
   {
      request()->merge([
         'company_id' => session('company_id'),
         'user_id' => session('user_id')
      ]);

      $dbname = company::find(Session::get('company_id'));
      config(['database.connections.dynamic_connection.database' => $dbname->dbname]);

      // Establish connection to the dynamic database
      DB::purge('dynamic_connection');
      DB::reconnect('dynamic_connection');

      $paymentdetail = $this->paymentdetailsModel::where('id', $id)->get();

      $invoice = $this->invoiceModel::findOrFail($paymentdetail[0]->inv_id);
      $this->authorize('view', $invoice);


      $jsonproductdata = app('App\Http\Controllers\\' . $this->version . '\api\invoiceController')->inv_details($invoice->id);
      $jsoninvdata = app('App\Http\Controllers\\' . $this->version . '\api\invoiceController')->index($invoice->id);
      $jsoncompanydetailsdata = app('App\Http\Controllers\\' . $this->version . '\api\companyController')->companydetailspdf($invoice->company_details_id);
      $jsontransportdata = app('App\Http\Controllers\\' . $this->version . '\api\partyController')->partydetailspdf($invoice->transport_id);
      if (!empty($invoice->account_id)) {
         $jsonbankdetailsdata = app('App\Http\Controllers\\' . $this->version . '\api\companymasterController')->bankdetailspdf($invoice->account_id);
         $jsonbankContent = $jsonbankdetailsdata->getContent();
         $bankdetailsdata = json_decode($jsonbankContent, true);
      } else {
         $bankdetailsdata = null;
      }
      // this get form data is product data
      $jsonproductContent = $jsonproductdata->getContent();

      //this form data is invoice data
      $jsoninvformdata = $jsoninvdata->getContent();
      //this get copmany details data
      $jsoncompanymasterContent = $jsoncompanydetailsdata->getContent();

      //this get transport details data
      $jsontransportContent = $jsontransportdata->getContent();
      // this get bank details data

      // Decode the JSON data
      $productdata = json_decode($jsonproductContent, true);
      $invdata = json_decode($jsoninvformdata, true);
      $companydetailsdata = json_decode($jsoncompanymasterContent, true);
      $transportdata = json_decode($jsontransportContent, true);




      if ($productdata['status'] == 404) {
         session()->flash('custom_error_message', 'Product column not found');
         abort('404');
      }
      if ($transportdata['status'] == 404) {
         session()->flash('custom_error_message', 'Transport details not found');
         abort('404');
      }
      if ($bankdetailsdata && $bankdetailsdata['status'] == 404) {
         session()->flash('custom_error_message', 'Bank details not found');
         abort('404');
      }

      if ($bankdetailsdata && $bankdetailsdata['status'] == 500) {
         session()->flash('custom_error_message', 'Bank details Unauthorized');
         abort('404');
      }

      if ($invdata['status'] == 404) {
         session()->flash('custom_error_message', 'Invoice data not found');
         abort('404');
      }

      if ($companydetailsdata['status'] == 404) {
         session()->flash('custom_error_message', 'Company details not found');
         abort('404');
      }

      $data = [
         'productscolumn' => $productdata['columnswithtype'],
         'products' => $productdata['invoice'],
         'othersettings' => $productdata['othersettings'][0],
         'invoiceothersettings' => $productdata['invoiceothersettings'],
         'invdata' => $invdata['invoice'][0],
         'companydetails' => $companydetailsdata['companydetails'][0],
         'transportdetails' => $transportdata['party'] ?? null,
         'bankdetails' => $bankdetailsdata['bankdetail'][0] ?? null,
         'paymentdetail' => $paymentdetail
      ];

      if (isset($paymentdetails)) {
         $jsonpaymentdata = app('App\Http\Controllers\\' . $this->version . '\api\PaymentController')->index($invoice->id);
         $jsonpaymentContent = $jsonpaymentdata->getContent();
         $paymentdata = json_decode($jsonpaymentContent, true);

         if ($paymentdata['status'] == 404) {
            session()->flash('custom_error_message', 'Payment data not found');
            abort('404');
         }

         $data['payment'] = $paymentdata['payment'];
      }

      $options = [
         'isPhpEnabled' => true,
         'isHtml5ParserEnabled' => true,
         'margin_top' => 0,
         'margin_right' => 0,
         'margin_bottom' => 0,
         'margin_left' => 0,
         'defaultFont' => 'Helvetica'
      ];

      //return view($this->version . '.admin.PDF.paymentreciept', $data);
      $pdf = PDF::setOptions($options)->loadView($this->version . '.admin.PDF.paymentreciept', ["data" => $data])->setPaper('a4', 'portrait');

      $name = 'Reciept ' . $paymentdetail[0]['receipt_number'] . '.pdf';
      // return view($this->version . '.admin.paymentreciept', $data);
      return $pdf->stream($name);
   }

   /**
    * Summary of generaterecieptall - generate full payment history
    * @param string $id
    * @return \Illuminate\Http\Response
    */
   public function generaterecieptall(string $id)
   {

      $dbname = company::find(Session::get('company_id'));
      config(['database.connections.dynamic_connection.database' => $dbname->dbname]);

      // Establish connection to the dynamic database
      DB::purge('dynamic_connection');
      DB::reconnect('dynamic_connection');


      $invoice = $this->invoiceModel::findOrFail($id);

      $this->authorize('view', $invoice);



      $data = $this->prepareDataForPDF($invoice, 'paymentdetails');
      $options = [
         'isPhpEnabled' => true,
         'isHtml5ParserEnabled' => true,
         'margin_top' => 0,
         'margin_right' => 0,
         'margin_bottom' => 0,
         'margin_left' => 0,
      ];

      $pdf = PDF::setOptions($options)->loadView($this->version . '.admin.PDF.paymentpaidreciept', $data)->setPaper('a4', 'portrait');

      $name = 'Receipt ' . $data['payment'][0]['receipt_number'] . '.pdf';

      if (count($data['payment']) > 1) {
         $name = 'PaymentHistory ' . $data['invdata']['inv_no'] . '.pdf';
      }

      // return view($this->version . '.admin.paymentpaidreciept', $data);
      return $pdf->stream($name);
   }

   public function generatepdfzip(Request $request)
   {

      set_time_limit(120);
      try {
         // Your existing code for generating PDFs and creating the zip file
         $dbname = company::find(Session::get('company_id'));
         config(['database.connections.dynamic_connection.database' => $dbname->dbname]);

         // Establish connection to the dynamic database
         DB::purge('dynamic_connection');
         DB::reconnect('dynamic_connection');

         $user_rp = DB::connection('dynamic_connection')->table('user_permissions')->select('rp')->where('user_id', $request->user_id)->get();
         $permissions = json_decode($user_rp, true);
         $rp = json_decode($permissions[0]['rp'], true);
         $reportuserlist = $rp['reportmodule']['report']['alldata'];

         if (!$reportuserlist) {
            return response()->json([
               'status' => 'error',
               'message' => "You have not access to report any user's data"
            ]);
         }

         $startDate = $request->fromdate;
         $endDate = Carbon::parse($request->todate);

         $invoices = $this->invoiceModel::whereBetween('inv_date', [$startDate, $endDate->addDay()])
            ->where([
               'is_deleted' => 0,
            ])
            ->whereIn('created_by', [$reportuserlist])
            ->get();

         if (count($invoices) == 0) {
            return response()->json([
               'status' => 'error',
               'message' => 'Not any invoice exists between this  date'
            ]);
         }

         $tempDir = storage_path('app/temp_pdf');
         if (!file_exists($tempDir)) {
            mkdir($tempDir, 0777, true);
         }

         foreach ($invoices as $invoice) {
            $data = $this->prepareDataForPDF($invoice);
            $pdf = PDF::loadView($this->version . '.admin.PDF.invoicetemplate', $data)->setPaper('a4', 'portrait');
            $pdfFileName = $invoice->inv_no . '_' . $invoice->company_name . '_' . $invoice->created_at->format('d-M-y') . '.pdf';
            $pdf->save($tempDir . '/' . $pdfFileName);
         }

         $withoutextensionzipFileName = 'invoices_' . date('Ymdhis');
         $zipFileName = $withoutextensionzipFileName . '.zip';
         $zip = new ZipArchive;
         if ($zip->open(storage_path('app/' . $zipFileName), ZipArchive::CREATE | ZipArchive::OVERWRITE)) {
            $files = Storage::files('temp_pdf');
            foreach ($files as $file) {
               $zip->addFile(storage_path('app/' . $file), basename($file));
            }
            $zip->close();
         } else {
            throw new Exception('Unable to create zip file');
         }

         Storage::deleteDirectory('temp_pdf');
         DB::connection('dynamic_connection')->table('reportlogs')->insert([
            'module_name' => 'invoice',
            'from_date' => $request->fromdate,
            'to_date' => $request->todate,
            'created_by' => $request->user_id,
         ]);

         return response()->json([
            'status' => 'success',
            'zipFileName' => route('file.download', $withoutextensionzipFileName) // Return the URL for downloading
         ]);
      } catch (Exception $e) {
         // Log the error
         Log::error($e->getMessage());

         return response()->json([
            'status' => 'error',
            'message' => 'Something went wrong while creating the zip file'
         ]);
      }
   }

   // Helper function to prepare data for invoice PDF generation
   private function prepareDataForPDF($invoice, $paymentdetails = null)
   {
      request()->merge([
         'company_id' => session('company_id'),
         'user_id' => session('user_id')
      ]);

      $jsonproductdata = app('App\Http\Controllers\\' . $this->version . '\api\invoiceController')->inv_details($invoice->id);
      $jsoninvdata = app('App\Http\Controllers\\' . $this->version . '\api\invoiceController')->index($invoice->id);
      $jsoncompanydetailsdata = app('App\Http\Controllers\\' . $this->version . '\api\companyController')->companydetailspdf($invoice->company_details_id);
      $jsontransportdata = app('App\Http\Controllers\\' . $this->version . '\api\partyController')->partydetailspdf($invoice->transport_id);
      if (!empty($invoice->account_id)) {
         $jsonbankdetailsdata = app('App\Http\Controllers\\' . $this->version . '\api\companymasterController')
            ->bankdetailspdf($invoice->account_id);

         $jsonbankContent = $jsonbankdetailsdata->getContent();
         $bankdetailsdata = json_decode($jsonbankContent, true);
      } else {
         $bankdetailsdata = null;
      }
      // this get form data is product data
      $jsonproductContent = $jsonproductdata->getContent();

      //this form data is invoice data
      $jsoninvformdata = $jsoninvdata->getContent();
      //this get copmany details data
      $jsoncompanymasterContent = $jsoncompanydetailsdata->getContent();

      //this get transport details data
      $jsontransportContent = $jsontransportdata->getContent();
      // this get bank details data

      // Decode the JSON data
      $productdata = json_decode($jsonproductContent, true);
      $invdata = json_decode($jsoninvformdata, true);
      $companydetailsdata = json_decode($jsoncompanymasterContent, true);
      $transportdata = json_decode($jsontransportContent, true);
      //   dd($transportdata);

      if ($productdata['status'] == 404) {
         session()->flash('custom_error_message', 'Product column not found');
         abort('404');
      }
      if ($transportdata['status'] == 404) {
         session()->flash('custom_error_message', 'Transport details not found');
         abort('404');
      }
      if ($bankdetailsdata && $bankdetailsdata['status'] == 404) {
         session()->flash('custom_error_message', 'Bank details not found');
         abort('404');
      }

      if ($bankdetailsdata && $bankdetailsdata['status'] == 500) {
         session()->flash('custom_error_message', 'Bank details Unauthorized');
         abort('404');
      }

      if ($invdata['status'] == 404) {
         session()->flash('custom_error_message', 'Invoice data not found');
         abort('404');
      }

      if ($companydetailsdata['status'] == 404) {
         session()->flash('custom_error_message', 'Company details not found');
         abort('404');
      }

      $data = [
         'productscolumn' => $productdata['columnswithtype'],
         'products' => $productdata['invoice'],
         'othersettings' => $productdata['othersettings'][0],
         'invoiceothersettings' => $productdata['invoiceothersettings'],
         'invdata' => $invdata['invoice'][0],
         'companydetails' => $companydetailsdata['companydetails'][0],
         'transportdetails' => $transportdata['party'] ?? null,
         'bankdetails' => $bankdetailsdata['bankdetail'][0] ?? null
      ];

      if (isset($paymentdetails)) {
         $jsonpaymentdata = app('App\Http\Controllers\\' . $this->version . '\api\PaymentController')->index($invoice->id);
         $jsonpaymentContent = $jsonpaymentdata->getContent();
         $paymentdata = json_decode($jsonpaymentContent, true);

         if ($paymentdata['status'] == 404) {
            session()->flash('custom_error_message', 'Payment data not found');
            abort('404');
         }

         $data['payment'] = $paymentdata['payment'];
      }

      return $data;
   }

   public function downloadZip(string $fileName)
   {
      $filePath = storage_path('app/');
      if (file_exists($filePath)) {
         return response()->download($filePath . $fileName . '.zip')->deleteFileAfterSend(true);
      }

      return response()->json([
         'status' => 'error',
         'message' => 'File not found'
      ], 404);
   }

   /**
    * Summary of generatepdf
    * generate quotation pdf
    * @param string $id
    * @return \Illuminate\Http\Response
    */
   public function generatequotationpdf(string $id)
   {

      $dbname = company::find(Session::get('company_id'));
      config(['database.connections.dynamic_connection.database' => $dbname->dbname]);

      // Establish connection to the dynamic database
      DB::purge('dynamic_connection');
      DB::reconnect('dynamic_connection');

      $quotation = $this->quotationModel::findOrFail($id);

      $data = $this->prepareDataForQuotationPDF($quotation);


      $options = [
         'isPhpEnabled' => true,
         'isHtml5ParserEnabled' => true,
         'margin_top' => 0,
         'margin_right' => 0,
         'margin_bottom' => 0,
         'margin_left' => 0,
         'defaultFont' => 'Helvetica'
      ];


      $companyname = $data['quotationdata']['firstname'] . $data['quotationdata']['lastname']; // if customer company name is not set

      if ($data['quotationdata']['company_name'] != '') {
         $companyname = $data['quotationdata']['company_name'];
      }

      // return view($this->version . '.admin.PDF.quotationtemplate', $data);
      $pdfname = $data['quotationdata']['quotation_number'] . ' ' . $companyname . ' ' . date('d-M-y') . '.pdf';

      $pdf = PDF::setOptions($options)->loadView($this->version . '.admin.PDF.quotationtemplate', $data)->setPaper('a4', 'portrait');

      return $pdf->stream($pdfname);
   }

   // Helper function to prepare data for PDF generation
   private function prepareDataForQuotationPDF($quotation, $paymentdetails = null)
   {
      request()->merge([
         'company_id' => session('company_id'),
         'user_id' => session('user_id')
      ]);

      $jsonproductdata = app('App\Http\Controllers\\' . $this->version . '\api\quotationController')->quotation_details($quotation->id);
      $jsonquotationdata = app('App\Http\Controllers\\' . $this->version . '\api\quotationController')->index($quotation->id);
      $jsoncompanydetailsdata = app('App\Http\Controllers\\' . $this->version . '\api\companyController')->companydetailspdf($quotation->company_details_id);

      $jsonproductContent = $jsonproductdata->getContent();
      $jsonquotationContent = $jsonquotationdata->getContent();
      $jsoncompanyContent = $jsoncompanydetailsdata->getContent();

      // Decode the JSON data
      $productdata = json_decode($jsonproductContent, true);
      $quotationdata = json_decode($jsonquotationContent, true);
      $companydetailsdata = json_decode($jsoncompanyContent, true);

      if ($productdata['status'] == 404) {
         return redirect()->back()->with('message', 'yes');
      }

      if ($quotationdata['status'] == 404) {
         session()->flash('custom_error_message', 'Quotation data not found');
         abort('404');
      }

      if ($companydetailsdata['status'] == 404) {
         session()->flash('custom_error_message', 'Company details not found');
         abort('404');
      }

      $data = [
         'productscolumn' => $productdata['columnswithtype'],
         'products' => $productdata['quotation'],
         'othersettings' => $productdata['othersettings'][0],
         'quotationdata' => $quotationdata['quotation'][0],
         'companydetails' => $companydetailsdata['companydetails'][0],
      ];

      return $data;
   }


   public function generateconsignorcopypdf(Request $request, int $id)
   {
      if (!$request->copies) {
         abort(404, 'invalid url');
      }
      // Convert the comma-separated string into an array and check if any value is invalid
      $copies = array_map('strtolower', explode(',', $request->copies));

      if (count($copies) > 3) {
         abort(404, 'invalid url');
      }

      foreach ($copies as $copy) {
         if (!in_array($copy, ['consignor', 'consignee', 'driver'])) {
            abort(404, 'Invalid URL');
         }
      }

      request()->merge([
         'company_id' => session('company_id'),
         'user_id' => session('user_id')
      ]);

      $consignor_copy = $this->consignor_copyModel::findOrFail($id);

      $jsonconsignorcopydata = app('App\Http\Controllers\\' . $this->version . '\api\consignorcopyController')->show($id);

      $jsonconsignercopyContent = $jsonconsignorcopydata->getContent();

      $consignorcopydata = json_decode($jsonconsignercopyContent, true);

      $options = [
         'isPhpEnabled' => true,
         'isFontSubsettingEnabled' => true,
         'margin_top' => 0,
         'margin_right' => 0,
         'margin_bottom' => 0,
         'margin_left' => 0,
         'padding_top' => 0,
         'padding_right' => 0,
         'padding_bottom' => 0,
         'padding_left' => 0,
         'defaultFont' => 'Helvetica',
         //    'isHtml5ParserEnabled' => true,
         //    'isRemoteEnabled' => true,
      ];

      if ($consignorcopydata['status'] != 200) {
         return redirect()->back()->with('message', 'failed');
      }

      $consignorcopydata['data']['copies'] = explode(',', $request->copies);

      // return view($this->version . '.admin.PDF.consignorcopy', $consignorcopydata);

      $pdfname = 'ConsignorCopy_' . $consignorcopydata['data']['consignorcopy']['consignment_note_no'] . '_' . $consignorcopydata['data']['consignorcopy']['consignor'] . '_' . date('d-M-y') . '.pdf';

      $pdf = PDF::setOptions($options)->loadView($this->version . '.admin.PDF.consignorcopy', $consignorcopydata)->setPaper('a4', 'portrait');

      return $pdf->stream($pdfname);
   }
   public function orderreport(Request $request)
   {
      
    $order = $this->orderModel::join('partys as buyer', 'buyer.id', 'orders.buyer_party')
            ->leftJoin('partys as transport', 'transport.id', 'orders.transport')
            ->join('order_details', 'order_details.order_id', 'orders.id')
            ->join('gardens', 'gardens.id', 'order_details.garden_id')
            ->leftJoin('grades', 'grades.id', 'order_details.grade')
            ->leftJoin('company_garden', 'company_garden.garden_id', '=', 'order_details.garden_id')
            ->leftJoin('companymasters', 'companymasters.id', '=', 'company_garden.company_id')
            ->where('orders.is_deleted', 0);

        // Filters mapping
        $filters = [
            'filter_transport'         => 'orders.transport',
            'filter_buyer'             => 'orders.buyer_party',
            'filter_garden'            => 'order_details.garden_id',
            'filter_grade'             => 'order_details.grade',
            'filter_credit_days_from'  => 'orders.credit_days',
            'filter_credit_days_to'    => 'orders.credit_days',
            'filter_final_amount_from' => 'orders.finalAmount',
            'filter_final_amount_to'   => 'orders.finalAmount',
            'filter_date_from'         => 'orders.created_at',
            'filter_date_to'           => 'orders.created_at',
        ];

        // Apply filters (except invoice status, which is handled later)
        foreach ($filters as $requestKey => $column) {
            $value = $request->$requestKey ?? null;

            if ($value !== null && $value !== '') {
                if (in_array($requestKey, [
                    'filter_credit_days_from',
                    'filter_credit_days_to',
                    'filter_final_amount_from',
                    'filter_final_amount_to',
                ])) {
                    $operator = str_contains($requestKey, 'from') ? '>=' : '<=';
                    $order->where($column, $operator, $value);

                } elseif (in_array($requestKey, ['filter_date_from', 'filter_date_to'])) {
                    $operator = str_contains($requestKey, 'from') ? '>=' : '<=';
                    $order->whereDate($column, $operator, $value);

                } else {
                    $order->whereIn($column, (array) $value);
                }
            }
        }

        // Company filter
        if (!empty($request->filter_company) && $request->filter_company !== '') {
            $companyIds = (array) $request->filter_company;

            $order->whereExists(function ($query) use ($companyIds) {
                $query->select(DB::raw(1))
                    ->from('order_details as od_sub')
                    ->join('company_garden as cg_sub', 'cg_sub.garden_id', '=', 'od_sub.garden_id')
                    ->whereColumn('od_sub.order_id', 'orders.id')
                    ->whereIn('cg_sub.company_id', $companyIds)
                    ->limit(1);
            });
        }

        // Fetch data
        $orderData = $order
            ->select(
                'orders.id as order_id',
                'buyer.name as buyer_name',
                'transport.name as transport_name',
                'orders.*',
                DB::raw("DATE_FORMAT(orders.created_at, '%d-%m-%Y') as order_date"),
                'order_details.*',
                'gardens.garden_name as garden_name',
                'grades.grade as grade_name',
                'companymasters.id as company_id',
                'companymasters.company_name as company_name'
            )
            ->get()
            ->groupBy('order_id')
            ->map(function ($details, $orderId) {
                $first = $details->first();

                // Determine invoice status
                $invoiceIds = $details->pluck('invoice_id');
                if ($invoiceIds->every(fn($id) => empty($id))) {
                    $invoiceStatus = 'Pending';
                } elseif ($invoiceIds->contains(fn($id) => empty($id))) {
                    $invoiceStatus = 'Half Invoice';
                } else {
                    $invoiceStatus = 'Invoices Created';
                }

                return [
                    'id'             => $orderId,
                    'buyer_name'     => $first->buyer_name,
                    'transport_name' => $first->transport_name,
                    'discount'       => $first->discount,
                    'totalNetKg'     => $first->totalNetKg,
                    'credit_days'    => $first->credit_days,
                    'final_amount'   => $first->finalAmount,
                    'order_date'     => $first->order_date,
                    'invoice_status' => $invoiceStatus, // Invoice status included

                    'company_names' => $details
                        ->map(fn($item) => $item->company_name ?? '  -  ')
                        ->values()
                        ->implode(', '),

                    'garden_names'   => $details
                        ->filter(fn($item) => !empty($item->garden_name))
                        ->pluck('garden_name', 'garden_id')
                        ->values()
                        ->implode(', '),

                    'invoice_nos'    => $details
                        ->filter(fn($item) => !empty($item->invoice_no))
                        ->pluck('invoice_no')
                        ->unique()
                        ->values()
                        ->implode(', '),

                    'details' => $details->map(function ($item) {
                        return [
                            'garden_name'  => $item->garden_name,
                            'grade_name'   => $item->grade_name,
                            'invoice_no'   => $item->invoice_no,
                            'bags'         => $item->bags,
                            'kg'           => $item->kg,
                            'net_kg'       => $item->net_kg,
                            'rate'         => $item->rate,
                            'amount'       => $item->amount,
                            'company_name' => $item->company_name ?? null,
                        ];
                    })->toArray(),
                ];
            })
            ->values();

        // Apply invoice_status filter AFTER grouping
        if (!empty($request->filter_invoice_status) && $request->filter_invoice_status !== '') {
            $status = $request->filter_invoice_status;
            $orderData = $orderData->filter(fn($order) => $order['invoice_status'] === $status)->values();
        }

      if ($orderData->isEmpty()) {
         return $this->successresponse(500, 'message', 'Not genrate pdf data is Empty!');
      }
      /* ───────── PDF ───────── */
      if (($request->type ?? 'pdf') === 'pdf') {

         $options = [
            'isPhpEnabled'        => true,
            'isHtml5ParserEnabled'=> true,
            'margin_top'          => 0,
            'margin_right'        => 0,
            'margin_bottom'       => 0,
            'margin_left'         => 0,
         ];

         $pdf = PDF::setOptions($options)
                     ->loadView($this->version . '.admin.PDF.orderreport', ['order' => $orderData])
                     ->setPaper('a4', 'portrait');

         return $pdf->stream('Order-Report.pdf');
      }

      /* ───────── EXCEL ───────── */

      if ($request->type === 'excel') {

         $filename = 'Sauda-Register-' . date('Y-m-d') . '.xls';

         // ── Get company info from first order ──
         $firstOrder    = $orderData->first();
         $companyName   = $firstOrder['company_names']  ?? 'KMD TEA AND AGRO';
         $todayDate     = date('d-m-Y');

         $html  = '<html xmlns:o="urn:schemas-microsoft-com:office:office"';
         $html .= ' xmlns:x="urn:schemas-microsoft-com:office:excel">';
         $html .= '<head><meta charset="UTF-8">';
         $html .= '<!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets>';
         $html .= '<x:ExcelWorksheet><x:Name>Sauda Register</x:Name>';
         $html .= '<x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions>';
         $html .= '</x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->';
         $html .= '<style>
            body  { font-family: Arial, sans-serif; font-size: 11px; }
            table { border-collapse: collapse; width: 100%; margin-bottom: 0; }

            /* ── Top header ── */
            .company-header {
                  font-size: 16px;
                  font-weight: bold;
            }
            .company-sub {
                  font-size: 10px;
                  color: #444444;
            }
            .page-title {
                  font-size: 15px;
                  font-weight: bold;
                  text-align: center;
                  background-color: #f0f0f0;
                  border: 1px solid #cccccc;
                  padding: 8px;
                  letter-spacing: 2px;
            }
            .date-cell {
                  text-align: right;
                  font-size: 11px;
                  vertical-align: top;
            }
            .spacer { height: 10px; }

            /* ── Order block ── */
            .order-block { margin-bottom: 16px; }

            .order-title-row td {
                  background-color: #f7f7f7;
                  border: 1px solid #cccccc;
                  padding: 6px 10px;
                  font-weight: bold;
                  font-size: 12px;
            }
            .order-id-cell {
                  text-align: right;
                  font-weight: bold;
                  font-size: 12px;
            }

            .order-info-row td {
                  border-left: 1px solid #cccccc;
                  border-right: 1px solid #cccccc;
                  padding: 4px 10px;
                  font-size: 10.5px;
                  background-color: #fbfbfb;
            }

            /* ── Detail table header ── */
            .detail-th {
                  background-color: #253566;
                  color: #ffffff;
                  font-weight: bold;
                  border: 1px solid #cccccc;
                  padding: 5px 8px;
                  text-align: center;
                  font-size: 10px;
                  white-space: nowrap;
                  letter-spacing: 0.5px;
            }

            /* ── Detail rows ── */
            .detail-td {
                  border: 1px solid #dddddd;
                  padding: 4px 8px;
                  vertical-align: middle;
                  font-size: 11px;
            }
            .detail-td-num {
                  border: 1px solid #dddddd;
                  padding: 4px 8px;
                  text-align: right;
                  vertical-align: middle;
                  font-size: 11px;
            }
            .row-even td { background-color: #f5f8ff; }
            .row-odd  td { background-color: #ffffff; }

            /* ── Final amount ── */
            .final-row td {
                  border: 1px solid #cccccc;
                  padding: 5px 10px;
                  text-align: right;
                  font-weight: bold;
                  font-size: 12px;
                  background-color: #f0f4ff;
            }

            /* ── Spacer row ── */
            .gap-row td {
                  border: none;
                  padding: 6px;
                  background-color: #ffffff;
            }
         </style></head><body>';

         /* ════════════════════════════════
            TOP HEADER TABLE
         ════════════════════════════════ */
         $html .= '<table>';
         $html .= '<tr>
            <td class="date-cell" colspan="11">Date: ' . $todayDate . '</td>
         </tr>';
         $html .= '</table>';

         /* ── SAUDA REGISTER Title ── */
         $html .= '<table style="margin-top:11px; margin-bottom:14px;">
            <tr><td class="page-title" colspan="10">SAUDA REGISTER</td></tr>
         </table>';

         /* ════════════════════════════════
            ORDER BLOCKS
         ════════════════════════════════ */
         foreach ($orderData as $index => $order) {

            $details     = $order['details'];
            $detailCount = count($details);

            $html .= '<table style="margin-bottom:16px; border: 1px solid #cccccc;">';

            /* ── Order title row: Buyer Name | Order Id ── */
            $html .= '<tr class="order-title-row">
                  <td colspan="9">Buyer Name #' . htmlspecialchars($order['buyer_name'] ?? '-') . '</td>
                  <td colspan="2" class="order-id-cell">Order Id #' . htmlspecialchars($order['id'] ?? '-') . '</td>
            </tr>';

            /* ── Order info row ── */
            $html .= '<tr class="order-info-row">
                  <td colspan="11">
                     <b>Credit Days:</b> ' . htmlspecialchars($order['credit_days']    ?? '-') . ' &nbsp;|&nbsp;
                     <b>Discount:</b> '    . htmlspecialchars($order['discount']        ?? '-') . ' &nbsp;|&nbsp;
                     <b>Transport Name:</b> ' . htmlspecialchars($order['transport_name'] ?? '-') . ' &nbsp;|&nbsp;
                     <b>Total Net KG:</b> ' . htmlspecialchars($order['totalNetKg']    ?? '-') . ' &nbsp;|&nbsp;
                     <b>Order Date:</b> '  . htmlspecialchars($order['order_date']     ?? '-') . '
                  </td>
            </tr>';

            /* ── Invoice Status row ── */
            $html .= '<tr class="order-info-row">
                  <td colspan="11">
                     <b>Invoice Status:</b> ' . htmlspecialchars($order['invoice_status'] ?? '-') . '
                  </td>
            </tr>';

            /* ── Detail table header ── */
            $html .= '<tr>
                  <th class="detail-th">COMPANY NAME</th>
                  <th class="detail-th">GARDEN NAME</th>
                  <th class="detail-th">GRADE NAME</th>
                  <th class="detail-th">INVOICE NO</th>
                  <th class="detail-th">BAGS</th>
                  <th class="detail-th">KG</th>
                  <th class="detail-th">NET KG</th>
                  <th class="detail-th">RATE</th>
                  <th class="detail-th">DISCOUNT</th>
                  <th class="detail-th" colspan="2">AMOUNT</th>
            </tr>';

            /* ── Detail rows ── */
            foreach ($details as $i => $detail) {
                  $rowClass = ($i % 2 === 0) ? 'row-odd' : 'row-even';
                  $html .= "<tr class=\"{$rowClass}\">";
                  $html .= '<td class="detail-td">'     . htmlspecialchars($detail['company_name'] ?? '-') . '</td>';
                  $html .= '<td class="detail-td">'     . htmlspecialchars($detail['garden_name']  ?? '-') . '</td>';
                  $html .= '<td class="detail-td">'     . htmlspecialchars($detail['grade_name']   ?? '-') . '</td>';
                  $html .= '<td class="detail-td">'     . htmlspecialchars($detail['invoice_no']   ?? '-') . '</td>';
                  $html .= '<td class="detail-td-num">' . htmlspecialchars($detail['bags']         ?? '-') . '</td>';
                  $html .= '<td class="detail-td-num">' . htmlspecialchars($detail['kg']           ?? '-') . '</td>';
                  $html .= '<td class="detail-td-num">' . htmlspecialchars($detail['net_kg']       ?? '-') . '</td>';
                  $html .= '<td class="detail-td-num">' . htmlspecialchars($detail['rate']         ?? '-') . '</td>';
                  $html .= '<td class="detail-td-num">' . htmlspecialchars($detail['discount']     ?? '0') . '</td>';
                  $html .= '<td class="detail-td-num" colspan="2">' . htmlspecialchars($detail['amount'] ?? '-') . '</td>';
                  $html .= '</tr>';
            }

            /* ── Final Amount row ── */
            $html .= '<tr class="final-row">
                  <td colspan="11">Final Amount &nbsp;&nbsp; ' . htmlspecialchars($order['final_amount'] ?? '0') . '</td>
            </tr>';

            $html .= '</table>';

            /* ── Gap between orders ── */
            $html .= '<table><tr class="gap-row"><td>&nbsp;</td></tr></table>';
         }

         $html .= '</body></html>';

         return response($html, 200, [
            'Content-Type'        => 'application/vnd.ms-excel; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma'              => 'no-cache',
            'Expires'             => '0',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
         ]);
      }
   }

   public function outstanding(Request $request)
   {
      $list = $this->brokerbillinvoiceModel
         ::leftJoin('broker_bill_payment_details', function ($join) {
            $join->on('broker_bill_payment_details.inv_id', '=', 'broker_bill_invoice.id')
               ->where('broker_bill_payment_details.is_deleted', 0);
         })
         ->leftJoin('company_garden', 'company_garden.company_id', '=', 'broker_bill_invoice.garden_company_id')
         ->leftJoin('broker_purchases', function ($join) {
            $join->on('broker_purchases.brokerbill_no', '=', 'broker_bill_invoice.id')
               ->where('broker_purchases.is_deleted', 0);
         })
         ->leftJoin('gardens', 'gardens.id', '=', 'broker_purchases.garden_id')
         ->leftJoin('companymasters', 'companymasters.id', '=', 'broker_bill_invoice.garden_company_id')
         ->where('broker_bill_invoice.is_deleted', 0);

      // ── Standard column filters (garden excluded — it lives in broker_purchases) ──
      $filters = [
         'filter_payment_status' => 'broker_bill_invoice.status',
         'filter_company'        => 'broker_bill_invoice.garden_company_id',
         'filter_date_from'      => 'broker_bill_invoice.created_at',
         'filter_date_to'        => 'broker_bill_invoice.created_at',
      ];

      foreach ($filters as $requestKey => $column) {
         $value = $request->$requestKey;

         if (isset($value) && $value !== '') {
            if ($requestKey == 'filter_date_from' || $requestKey == 'filter_date_to') {
               $operator = strpos($requestKey, 'from') !== false ? '>=' : '<=';
               $list->where($column, $operator, $value);
            } elseif (strpos($requestKey, 'from') !== false || strpos($requestKey, 'to') !== false) {
               $operator = strpos($requestKey, 'from') !== false ? '>=' : '<=';
               $list->whereDate($column, $operator, $value);
            } else {
               if (is_array($value)) {
                  $list->whereIn($column, $value);
               } else {
                  $list->where($column, $value);
               }
            }
         }
      }

      // ── Garden filter — garden_id is in broker_purchases, not broker_bill_invoice ──
      // Subquery: find all broker_bill_invoice.id values that have a matching
      // broker_purchases row for the selected garden_id(s) via brokerbill_no.
      if (!empty($request->filter_garden) && $request->filter_garden !== '') {
         $gardenIds = (array) $request->filter_garden; // handles single value or array

         $list->whereIn('broker_bill_invoice.id', function ($query) use ($gardenIds) {
            $query->select('brokerbill_no')
               ->from('broker_purchases')
               ->whereIn('garden_id', $gardenIds)
               ->where('is_deleted', 0)
               ->whereNotNull('brokerbill_no');
         });
      }
      if (!empty($request->filter_buyer) && $request->filter_buyer !== '') {
         $buyerIds = (array) $request->filter_buyer;

         $list->whereIn('broker_bill_invoice.id', function ($query) use ($buyerIds) {
            $query->select('bp.brokerbill_no')
               ->from('broker_purchases as bp')
               ->leftJoin('order_details as od', 'od.id', '=', 'bp.order_detail_id')
               ->leftJoin('orders as o', 'o.id', '=', 'od.order_id')
               ->whereIn('o.buyer_party', $buyerIds)
               ->where('bp.is_deleted', 0)
               ->whereNotNull('bp.brokerbill_no');
         });
      }
      $list = $list
         ->select(
            'broker_bill_invoice.id as invoice_id',
            'broker_bill_invoice.invoice_no',
            'broker_bill_invoice.invoice_date',
            'broker_bill_invoice.totalamount',
            'broker_bill_invoice.igst',
            'broker_bill_invoice.cgst',
            'broker_bill_invoice.sgst',
            'broker_bill_invoice.grand_total',
            'broker_bill_invoice.status',
            'broker_bill_invoice.from_date',
            'broker_bill_invoice.to_date',
            'gardens.garden_name as garden_name',
            'broker_bill_payment_details.receipt_number',
            'broker_bill_payment_details.transaction_id',
            'broker_bill_payment_details.datetime',
            'broker_bill_payment_details.paid_by',
            'broker_bill_payment_details.paid_type',
            'broker_bill_payment_details.paid_amount',
            'broker_bill_payment_details.pending_amount',
            'companymasters.company_name',
            DB::raw("(SELECT GROUP_CONCAT(DISTINCT invoice_no ORDER BY id SEPARATOR ', ')
                      FROM broker_purchases
                      WHERE brokerbill_no = broker_bill_invoice.id) as lot_no"),
            DB::raw("(SELECT GROUP_CONCAT(DISTINCT g.garden_name ORDER BY bp.id SEPARATOR ', ')
                      FROM broker_purchases bp
                      LEFT JOIN gardens g ON g.id = bp.garden_id
                      WHERE bp.brokerbill_no = broker_bill_invoice.id) as garden_names"),
            DB::raw("(SELECT ROUND(SUM(net_kg), 2)
                      FROM broker_purchases
                      WHERE brokerbill_no = broker_bill_invoice.id) as net_kg"),
            DB::raw("(SELECT GROUP_CONCAT(DISTINCT brokerage ORDER BY id SEPARATOR ', ')
                      FROM broker_purchases
                      WHERE brokerbill_no = broker_bill_invoice.id) as brokerage"),
            DB::raw("(SELECT GROUP_CONCAT(DISTINCT p.name ORDER BY p.name SEPARATOR ', ')
                     FROM broker_purchases bp
                     LEFT JOIN order_details od ON od.id = bp.order_detail_id
                     LEFT JOIN orders o ON o.id = od.order_id
                     LEFT JOIN partys p ON p.id = o.buyer_party
                     WHERE bp.brokerbill_no = broker_bill_invoice.id
                     AND bp.is_deleted = 0
                  ) as buyer_names"),
            DB::raw("(SELECT GROUP_CONCAT(DISTINCT p.name ORDER BY p.name SEPARATOR ', ')
                     FROM broker_purchases bp
                     LEFT JOIN order_details od ON od.id = bp.order_detail_id
                     LEFT JOIN orders o ON o.id = od.order_id
                     LEFT JOIN partys p ON p.id = o.buyer_party
                     WHERE bp.brokerbill_no = broker_bill_invoice.id
                     AND bp.is_deleted = 0
                  ) as buyer_names"),
         )
         ->get()
         ->groupBy('invoice_id')
         ->map(function ($rows, $invoiceId) {
            $first = $rows->first();
            return [
               'id'           => $invoiceId,
               'invoice_no'   => $first->invoice_no,
               'invoice_date' => $first->invoice_date,
               'totalamount'  => $first->totalamount,
               'igst'         => $first->igst,
               'cgst'         => $first->cgst,
               'sgst'         => $first->sgst,
               'grand_total'  => $first->grand_total,
               'status'       => $first->status,
               'from_date'    => $first->from_date,
               'to_date'      => $first->to_date,
               'garden_name'  => $first->garden_names,
               'company_name' => $first->company_name,
               'net_kg'       => $first->net_kg,
               'brokerage'    => $first->brokerage,
               'buyer_names' => $first->buyer_names,
               'buyer_ids'   => $first->buyer_ids,
               'details'      => $rows->map(function ($item) {
                  return [
                     'receipt_number' => $item->receipt_number,
                     'transaction_id' => $item->transaction_id,
                     'datetime'       => $item->datetime,
                     'paid_by'        => $item->paid_by,
                     'paid_type'      => $item->paid_type,
                     'paid_amount'    => $item->paid_amount,
                     'pending_amount' => $item->pending_amount,
                  ];
               })->toArray(),
            ];
         })
         ->values();
      // dd($list);
      if ($list->isEmpty()) {
         return $this->successresponse(500, 'message', 'Not genrate pdf data is Empty!');
      }
      if (($request->type ?? 'pdf') === 'pdf') {
      $options = [
         'isPhpEnabled' => true,
         'isHtml5ParserEnabled' => true,
         'isRemoteEnabled' => true,
      ];

      $gardenNames = $list->pluck('garden_name')->unique()->values();

      $pdf = PDF::setOptions($options)->loadView($this->version . '.admin.PDF.outstanding', ["list" => $list, 'gardenNames' => $gardenNames])->setPaper('a4', 'portrait');

      if ($gardenNames->count() === 1) {
         $name = $gardenNames[0];
      } else {
         $name = $gardenNames->implode('-');
      }

      //return view($this->version . '.admin.PDF.outstanding', ["list" => $list]);
      // return $pdf->download($name . '-Garden_Outstanding_' . date('Y-m-d_H-i-s') . '.pdf');
      // return $pdf->stream($name . '-Garden_Outstanding_' . date('Y-m-d_H-i-s') . '.pdf');
      return $pdf->stream('Garden_Outstanding_' . date('Y-m-d_H-i-s') . '.pdf');
      }
     if ($request->type === 'excel') {
    $filename = 'Outstanding-Report-' . date('Y-m-d') . '.xls';

    $html  = '<html xmlns:o="urn:schemas-microsoft-com:office:office"';
    $html .= ' xmlns:x="urn:schemas-microsoft-com:office:excel">';
    $html .= '<head><meta charset="UTF-8">';
    $html .= '<!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets>';
    $html .= '<x:ExcelWorksheet><x:Name>Outstanding Report</x:Name>';
    $html .= '<x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions>';
    $html .= '</x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->';
    $html .= '<style>
        body  { font-family: Arial, sans-serif; font-size: 11px; color: #333; }
        table { border-collapse: collapse; width: 100%; margin-bottom: 0; }

        /* ── Page header ── */
        .company-name { font-size: 16px; font-weight: bold; color: #000; }
        .company-sub  { font-size: 10px; color: #444; line-height: 1.6; }
        .date-cell    { text-align: right; font-size: 11px; vertical-align: top; }

        /* ── Report title ── */
        .page-title {
            font-size: 15px; font-weight: bold; text-align: center;
            text-transform: uppercase; background-color: #f9f9f9;
            border-top: 1px solid #eeeeee; border-bottom: 1px solid #eeeeee;
            padding: 10px; letter-spacing: 1px;
        }

        /* ── Invoice box header row 1 (Company | Gardens) ── */
        .inv-header-1 td {
            background-color: #f4f7f9;
            border: 1px solid #999999;
            padding: 7px 10px;
            font-weight: bold;
            font-size: 12px;
        }

        /* ── Invoice box header row 2 (Invoice # | Buyers) ── */
        .inv-header-2 td {
            background-color: #f4f7f9;
            border: 1px solid #999999;
            border-top: none;
            padding: 6px 10px;
            font-weight: bold;
            font-size: 12px;
        }

        /* ── Invoice meta ── */
        .inv-meta td {
            background-color: #ffffff;
            border-left: 1px solid #e0e0e0;
            border-right: 1px solid #e0e0e0;
            border-bottom: 1px solid #eeeeee;
            padding: 5px 10px;
            font-size: 10px;
            color: #666666;
        }

        /* ── Column headers ── */
        .col-th {
            background-color: #fcfcfc;
            color: #555555;
            font-weight: bold;
            font-size: 9px;
            text-transform: uppercase;
            border-bottom: 1px solid #444444;
            border-top: 1px solid #e0e0e0;
            border-left: 1px solid #e0e0e0;
            border-right: 1px solid #e0e0e0;
            padding: 7px 5px;
            text-align: left;
        }
        .col-th-right {
            background-color: #fcfcfc;
            color: #555555;
            font-weight: bold;
            font-size: 9px;
            text-transform: uppercase;
            border-bottom: 1px solid #444444;
            border-top: 1px solid #e0e0e0;
            border-left: 1px solid #e0e0e0;
            border-right: 1px solid #e0e0e0;
            padding: 7px 5px;
            text-align: right;
        }

        /* ── Data rows ── */
        .data-td {
            border: 1px solid #eeeeee;
            padding: 7px 5px;
            vertical-align: top;
            font-size: 11px;
        }
        .data-td-right {
            border: 1px solid #eeeeee;
            padding: 7px 5px;
            text-align: right;
            vertical-align: top;
            font-size: 11px;
        }

        /* ── No payments ── */
        .no-payment-row td {
            text-align: center;
            border: 1px solid #eeeeee;
            padding: 7px 5px;
            color: #999999;
            font-size: 11px;
        }

        /* ── Total outstanding row ── */
        .total-row td {
            border: 1px solid #e0e0e0;
            padding: 7px 5px;
            font-size: 12px;
            font-weight: bold;
            background-color: #fafafa;
        }
        .due-pending { color: #d9534f; font-weight: bold; }
        .due-paid    { color: #2e7d32; font-weight: bold; }

        /* ── Gap between invoice blocks ── */
        .gap-row td { border: none; padding: 10px; background: #ffffff; }

        /* ── Footer ── */
        .footer-td { font-size: 10px; color: #999999; border-top: 1px solid #eeeeee; padding-top: 6px; }
    </style></head><body>';

    /* ════════════════════════════════
       PAGE HEADER
    ════════════════════════════════ */
    $html .= '<table>
        <tr>
            <td class="date-cell" colspan="7" text-align="center">Date: ' . date('d-m-Y') . '</td>
        </tr>
    </table>';

    /* ── Report Title ── */
    $html .= '<table style="margin-top:12px; margin-bottom:16px;">
        <tr><td class="page-title" colspan="7">Outstanding Report</td></tr>
    </table>';

    /* ════════════════════════════════
       INVOICE BLOCKS
    ════════════════════════════════ */
    foreach ($list as $invoice) {

        /* ── Calculate totals ── */
        $totalPaid = 0;
        foreach ($invoice['details'] ?? [] as $pmt) {
            $totalPaid += $pmt['paid_amount'] ?? 0;
        }
        $due          = ($invoice['grand_total'] ?? 0) - $totalPaid;
        $dueClass     = $due == 0 ? 'due-paid' : 'due-pending';
        $dueFormatted = function_exists('formatINR') ? formatINR($due)                        : number_format($due, 2);
        $grandFmt     = function_exists('formatINR') ? formatINR($invoice['grand_total'] ?? 0) : number_format($invoice['grand_total'] ?? 0, 2);

        /* ── Invoice date ── */
        $invDate = '-';
        if (!empty($invoice['invoice_date'])) {
            try { $invDate = \Carbon\Carbon::parse($invoice['invoice_date'])->format('d-m-Y'); } catch (\Exception $e) {}
        }

        /* ── Header row 1: Company | Gardens ── */
        $html .= '<table style="margin-bottom:0;">
            <tr class="inv-header-1">
                <td width="50%" colspan="2">Company: ' . htmlspecialchars($invoice['company_name'] ?? '-') . '</td>
                <td width="50%" style="text-align:right;" colspan="5">Gardens: ' . htmlspecialchars($invoice['garden_name'] ?? '-') . '</td>
            </tr>
        </table>';

        /* ── Header row 2: Invoice # | Buyers ── */
        $html .= '<table style="margin-bottom:0;">
            <tr class="inv-header-2">
                <td width="50%" colspan="2">Invoice #' . htmlspecialchars($invoice['invoice_no'] ?? '-') . '</td>
                <td width="50%" style="text-align:right;" colspan="5">Buyers: ' . htmlspecialchars($invoice['buyer_names'] ?? '-') . '</td>
            </tr>
        </table>';

        /* ── Meta row ── */
        $html .= '<table style="margin-bottom:0;">
            <tr class="inv-meta">
                <td colspan="7">
                    <b>Date:</b> ' . $invDate . ' &nbsp;|&nbsp;
                    <b>IGST:</b> '      . htmlspecialchars($invoice['igst']      ?? '0.00') . ' &nbsp;|&nbsp;
                    <b>CGST:</b> '      . htmlspecialchars($invoice['cgst']      ?? '0.00') . ' &nbsp;|&nbsp;
                    <b>SGST:</b> '      . htmlspecialchars($invoice['sgst']      ?? '0.00') . ' &nbsp;|&nbsp;
                    <b>Status:</b> '    . strtoupper(htmlspecialchars($invoice['status']    ?? '-')) . ' &nbsp;|&nbsp;
                    <b>Net Kg:</b> '    . htmlspecialchars($invoice['net_kg']    ?? '0.00') . ' &nbsp;|&nbsp;
                    <b>Brokerage:</b> ' . htmlspecialchars($invoice['brokerage'] ?? '0.00') . ' &nbsp;|&nbsp;
                    <b>Total Amount:</b> ' . $grandFmt . '
                </td>
            </tr>
        </table>';

        /* ── Payment table ── */
        $html .= '<table style="margin-bottom:0;">
            <tr>
                <th class="col-th"       width="13%">Payment Date</th>
                <th class="col-th"       width="13%">Receipt No.</th>
                <th class="col-th"       width="18%">Transaction ID</th>
                <th class="col-th"       width="14%">Paid By</th>
                <th class="col-th"       width="10%">Type</th>
                <th class="col-th"       width="12%">Paid Amt</th>
                <th class="col-th-right" width="12%">Balance</th>
            </tr>';

        /* ── Filter valid payments (has receipt_number) ── */
        $payments = array_values(array_filter(
            $invoice['details'] ?? [],
            fn($d) => !empty($d['receipt_number'])
        ));

        if (count($payments) === 0) {
            $html .= '<tr class="no-payment-row">
                <td colspan="7">No payments available</td>
            </tr>';
        } else {
            foreach ($payments as $detail) {
                $pmtDate = '-';
                if (!empty($detail['datetime'])) {
                    try { $pmtDate = \Carbon\Carbon::parse($detail['datetime'])->format('d-m-Y'); } catch (\Exception $e) {}
                }
                $paidFmt    = function_exists('formatINR') ? formatINR($detail['paid_amount']    ?? 0) : number_format($detail['paid_amount']    ?? 0, 2);
                $pendingFmt = function_exists('formatINR') ? formatINR($detail['pending_amount'] ?? 0) : number_format($detail['pending_amount'] ?? 0, 2);

                $html .= '<tr>
                    <td class="data-td">'       . $pmtDate . '</td>
                    <td class="data-td">'       . htmlspecialchars($detail['receipt_number'] ?? '-') . '</td>
                    <td class="data-td"><small>' . htmlspecialchars($detail['transaction_id'] ?? '-') . '</small></td>
                    <td class="data-td">'       . htmlspecialchars($detail['paid_by']         ?? '-') . '</td>
                    <td class="data-td">'       . htmlspecialchars($detail['paid_type']       ?? '-') . '</td>
                    <td class="data-td">'       . $paidFmt    . '</td>
                    <td class="data-td-right">' . $pendingFmt . '</td>
                </tr>';
            }
        }

        /* ── Total Outstanding row ── */
        $html .= '<tr class="total-row">
            <td colspan="6" style="text-align:right;"><b>Total Outstanding</b></td>
            <td style="text-align:right; border:1px solid #e0e0e0; padding:7px 5px; font-weight:bold;">
                <span class="' . $dueClass . '">' . $dueFormatted . '</span>
            </td>
        </tr>';

        $html .= '</table>';

        /* ── Gap between blocks ── */
        $html .= '<table><tr class="gap-row"><td>&nbsp;</td></tr></table>';
    }
    $html .= '</body></html>';

    return response($html, 200, [
        'Content-Type'        => 'application/vnd.ms-excel; charset=utf-8',
        'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        'Pragma'              => 'no-cache',
        'Expires'             => '0',
        'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
    ]);
}
   }

   public function leger(Request $request)
   {
      $userId = $request->user_id;

      $invoices = $this->invoiceModel
         ::leftJoin('partys', 'invoices.customer_id', '=', 'partys.id')
         ->leftJoin('companymasters', 'invoices.company_details_id', '=', 'companymasters.id')
         ->select(
            'invoices.*',
            DB::raw("DATE_FORMAT(invoices.inv_date, '%d-%m-%Y') as inv_date_formatted"),
            DB::raw("CONCAT_WS(' ', partys.name) as customer"),
            'companymasters.company_name as garden_company_name'
         )
         ->where('invoices.created_by', $userId)
         ->where('invoices.is_deleted', 0);
      $filters = [
         'filter_company'      => 'invoices.company_details_id',
         'filter_buyer'        => 'invoices.customer_id',
         'filter_payment_status'       => 'invoices.status',
      ];

      foreach ($filters as $requestKey => $column) {
         $value = $request->$requestKey;

         if (isset($value)) {
            if ($requestKey == 'filter_net_kg_from' || $requestKey == 'filter_net_kg_to' || $requestKey == 'filter_bags_from' || $requestKey == 'filter_bags_to') {
               $operator = strpos($requestKey, 'from') !== false ? '>=' : '<=';
               $invoices->where($column, $operator, $value);
            } else if (strpos($requestKey, 'from') !== false || strpos($requestKey, 'to') !== false) {
               $operator = strpos($requestKey, 'from') !== false ? '>=' : '<=';
               $invoices->whereDate($column, $operator, $value);
            } else {
               $invoices->where($column, $value);
            }
         }
      }
      $invoices = $invoices->orderBy('invoices.inv_date', 'desc')->get();

      $invoiceIds = $invoices->pluck('id');

      $payments = $this->paymentdetailsModel
         ::whereIn('inv_id', $invoiceIds)
         ->where('is_deleted', 0)
         ->orderBy('datetime', 'asc') // optional if you have payment_date
         ->get()
         ->groupBy('inv_id');

      $invoices->transform(function ($invoice) use ($payments) {
         $invoice->payment_details = $payments[$invoice->id] ?? collect();
         return $invoice;
      });

      $grouped = $invoices->groupBy('customer_id')
         ->map(function ($customerInvoices) {

            return [
               'customer_id'   => $customerInvoices->first()->customer_id,
               'customer_name' => $customerInvoices->first()->customer,

               'companies' => $customerInvoices->groupBy('company_details_id')
                  ->map(function ($companyInvoices) {

                     return [
                        'company_id'   => $companyInvoices->first()->company_details_id,
                        'company_name' => $companyInvoices->first()->garden_company_name,
                        'invoices'     => $companyInvoices->values()
                     ];
                  })->values()

            ];
         })->values();
      if ($grouped->isEmpty()) {
         return $this->successresponse(500, 'message', 'Not genrate pdf data is Empty!');
      }
      if (($request->type ?? 'pdf') === 'pdf') {
         $options = [
            'isPhpEnabled' => true,
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
         ];
         // dd($grouped);
         $pdf = PDF::setOptions($options)->loadView($this->version . '.admin.PDF.ledger', ["ledger" => $grouped])->setPaper('a4', 'portrait');

         // return view($this->version . '.admin.PDF.ledger', ["ledger" => $grouped]);
         // return $pdf->download('LEDGER' . date('Y-m-d_H-i-s') . '.pdf');
         return $pdf->stream('LEDGER' . date('Y-m-d_H-i-s') . '.pdf');
      }
      if ($request->type === 'excel') {
         $filename = 'Ledger-' . date('Y-m-d') . '.xls';

         $html  = '<html xmlns:o="urn:schemas-microsoft-com:office:office"';
         $html .= ' xmlns:x="urn:schemas-microsoft-com:office:excel">';
         $html .= '<head><meta charset="UTF-8">';
         $html .= '<!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets>';
         $html .= '<x:ExcelWorksheet><x:Name>Ledger</x:Name>';
         $html .= '<x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions>';
         $html .= '</x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->';
         $html .= '<style>
            body  { font-family: Arial, sans-serif; font-size: 11px; }
            table { border-collapse: collapse; width: 100%; margin-bottom: 0; }

            /* ── Top header ── */
            .company-name  { font-size: 16px; font-weight: bold; }
            .company-sub   { font-size: 10px; color: #444444; }
            .date-cell     { text-align: right; font-size: 11px; vertical-align: top; }
            .page-title    {
                  font-size: 15px; font-weight: bold; text-align: center;
                  background-color: #f0f0f0; border: 1px solid #cccccc;
                  padding: 8px; letter-spacing: 2px; text-transform: uppercase;
            }

            /* ── Customer block ── */
            .customer-header td {
                  background-color: #eeeeee;
                  border: 1px solid #999999;
                  padding: 6px 10px;
                  font-weight: bold;
                  font-size: 13px;
            }

            /* ── Company block ── */
            .company-header td {
                  background-color: #f9f9f9;
                  border: 1px solid #999999;
                  padding: 5px 10px;
                  font-weight: bold;
                  font-size: 12px;
            }

            /* ── Invoice meta row ── */
            .invoice-meta td {
                  background-color: #f9f9f9;
                  border: 1px solid #cccccc;
                  padding: 5px 10px;
                  font-size: 11px;
            }
            .due-pending { color: #d32f2f; font-weight: bold; }
            .due-paid    { color: #2e7d32; font-weight: bold; }

            /* ── Detail column headers ── */
            .col-th {
                  background-color: #ffffff;
                  border-bottom: 1px solid #999999;
                  border-top: 1px solid #cccccc;
                  border-left: 1px solid #cccccc;
                  border-right: 1px solid #cccccc;
                  padding: 6px 8px;
                  font-weight: bold;
                  font-size: 10px;
                  text-transform: uppercase;
            }
            .col-th-right {
                  background-color: #ffffff;
                  border-bottom: 1px solid #999999;
                  border-top: 1px solid #cccccc;
                  border-left: 1px solid #cccccc;
                  border-right: 1px solid #cccccc;
                  padding: 6px 8px;
                  font-weight: bold;
                  font-size: 10px;
                  text-transform: uppercase;
                  text-align: right;
            }

            /* ── Data rows ── */
            .data-td {
                  border: 1px solid #eeeeee;
                  padding: 6px 8px;
                  vertical-align: top;
                  font-size: 11px;
            }
            .data-td-right {
                  border: 1px solid #eeeeee;
                  padding: 6px 8px;
                  text-align: right;
                  vertical-align: top;
                  font-size: 11px;
                  font-weight: bold;
            }
            .debit-cell  { color: #d32f2f; font-weight: bold; text-align: right;
                           border: 1px solid #eeeeee; padding: 6px 8px; font-size: 11px; }
            .credit-cell { color: #2e7d32; font-weight: bold; text-align: right;
                           border: 1px solid #eeeeee; padding: 6px 8px; font-size: 11px; }

            /* ── All payments completed ── */
            .paid-row td {
                  text-align: center;
                  color: #2ecc71;
                  font-weight: bold;
                  border: 1px solid #eeeeee;
                  padding: 5px;
            }

            /* ── No payments ── */
            .no-payment-row td {
                  text-align: center;
                  color: #e74c3c;
                  font-weight: bold;
                  border: 1px solid #eeeeee;
                  padding: 5px;
                  font-size: 11px;
            }

            /* ── Gap ── */
            .gap-row td { border: none; padding: 8px; background: #ffffff; }
         </style></head><body>';

         /* ════════════════════════════════
            PAGE HEADER
         ════════════════════════════════ */
         $html .= '<table>';
         $html .= '<tr>
            <td class="date-cell" text-align="center" colspan="5">Date: ' . date('d-m-Y') . '</td>
         </tr>
         ';
         $html .= '</table>';

         /* ── Title ── */
         $html .= '<table style="margin-top:10px; margin-bottom:14px;">
            <tr><td class="page-title" colspan="5">Ledger</td></tr>
         </table>';

         /* ════════════════════════════════
            CUSTOMER BLOCKS
         ════════════════════════════════ */
         foreach ($grouped as $customer) {

            /* ── Customer header ── */
            $html .= '<table style="margin-bottom:2px;">
                  <tr class="customer-header">
                     <td colspan="5">Buyer: ' . htmlspecialchars($customer['customer_name'] ?? 'N/A') . '</td>
                  </tr>
            </table>';

            foreach ($customer['companies'] as $company) {

                  /* ── Company header ── */
                  $html .= '<table style="margin-bottom:2px;">
                     <tr class="company-header">
                        <td colspan="5">Company: ' . htmlspecialchars($company['company_name'] ?? 'N/A') . '</td>
                     </tr>
                  </table>';

                  foreach ($company['invoices'] as $invoice) {

                     /* ── Calculate due ── */
                     $due = $invoice['grand_total'] ?? 0;
                     foreach ($invoice['payment_details'] ?? [] as $pmt) {
                        $due -= $pmt['paid_amount'] ?? 0;
                     }
                     $dueClass  = $due > 0 ? 'due-pending' : 'due-paid';
                     $dueFormatted = function_exists('formatINR') ? formatINR($due) : number_format($due, 2);
                     $grandFormatted = function_exists('formatINR')
                        ? formatINR($invoice['grand_total'] ?? 0)
                        : number_format($invoice['grand_total'] ?? 0, 2);

                     /* ── Invoice meta row ── */
                     $html .= '<table style="margin-bottom:0;">
                        <tr class="invoice-meta">
                              <td width="33%" colspan="2"><b>Invoice No:</b> ' . htmlspecialchars($invoice['inv_no'] ?? '-') . '</td>
                              <td width="34%" style="text-align:center;"><b>Date:</b> ' . htmlspecialchars($invoice['inv_date_formatted'] ?? '-') . '</td>
                              <td width="33%" colspan="2" style="text-align:right;">
                                 <b>Due:</b> <span class="' . $dueClass . '">' . $dueFormatted . '</span>
                              </td>
                        </tr>
                     </table>';

                     /* ── Column headers ── */
                     $html .= '<table style="margin-bottom:0;">
                        <tr>
                              <th class="col-th"    width="12%">Date</th>
                              <th class="col-th"    width="43%">Details / Remarks</th>
                              <th class="col-th-right" width="15%">Debit</th>
                              <th class="col-th-right" width="15%">Credit</th>
                              <th class="col-th-right" width="15%">Balance</th>
                        </tr>';

                     /* ── Invoice balance row ── */
                     $html .= '<tr>
                        <td class="data-td">'       . htmlspecialchars($invoice['inv_date_formatted'] ?? '-') . '</td>
                        <td class="data-td">Invoice Balance</td>
                        <td class="debit-cell">'    . $grandFormatted . '</td>
                        <td class="data-td-right">0.00</td>
                        <td class="data-td-right">' . $grandFormatted . '</td>
                     </tr>';

                     /* ── Payment rows ── */
                     $payments = $invoice['payment_details'] ?? [];

                     if (count($payments) > 0) {
                        foreach ($payments as $payment) {
                              $paidDate    = isset($payment['datetime'])
                                 ? \Carbon\Carbon::parse($payment['datetime'])->format('d-m-Y')
                                 : '-';
                              $paidAmt     = function_exists('formatINR')
                                 ? formatINR($payment['paid_amount'] ?? 0)
                                 : number_format($payment['paid_amount'] ?? 0, 2);
                              $pendingAmt  = function_exists('formatINR')
                                 ? formatINR($payment['pending_amount'] ?? 0)
                                 : number_format($payment['pending_amount'] ?? 0, 2);

                              $html .= '<tr>
                                 <td class="data-td">' . $paidDate . '</td>
                                 <td class="data-td">
                                    Payment Received (' . htmlspecialchars($payment['paid_type'] ?? 'N/A') . ')<br>
                                    <small>Ref: ' . htmlspecialchars($payment['receipt_number'] ?? '-') . ' | By: ' . htmlspecialchars($payment['paid_by'] ?? '-') . '</small>
                                 </td>
                                 <td class="data-td-right">0.00</td>
                                 <td class="credit-cell">' . $paidAmt . '</td>
                                 <td class="data-td-right">' . $pendingAmt . '</td>
                              </tr>';

                              /* ── All payments completed ── */
                              if (($payment['pending_amount'] ?? 1) == 0) {
                                 $html .= '<tr class="paid-row">
                                    <td colspan="5">All payments completed</td>
                                 </tr>';
                              }
                        }
                     } else {
                        /* ── No payments ── */
                        $html .= '<tr class="no-payment-row">
                              <td colspan="5">No payment transactions found for this invoice.</td>
                        </tr>';
                     }

                     $html .= '</table>';

                     /* ── Small gap between invoices ── */
                     $html .= '<table><tr class="gap-row"><td>&nbsp;</td></tr></table>';
                  }
            }

            /* ── Gap between customers ── */
            $html .= '<table><tr class="gap-row"><td>&nbsp;</td></tr></table>';
         }
         $html .= '</body></html>';

         return response($html, 200, [
            'Content-Type'        => 'application/vnd.ms-excel; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma'              => 'no-cache',
            'Expires'             => '0',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
         ]);
      }
   }

   public function orderpdf($id)
   {
      $order = $this->orderModel::find($id);
      $order_details = $this->order_detailModel::where('order_details.order_id', $id)
         ->leftJoin('gardens', 'gardens.id', '=', 'order_details.garden_id')
         ->leftJoin('company_garden', 'company_garden.garden_id', '=', 'order_details.garden_id')
         ->leftJoin('companymasters', 'companymasters.id', '=', 'company_garden.company_id')
         ->leftJoin('grades', 'grades.id', '=', 'order_details.grade')
         ->select(
            'order_details.*',
            'gardens.garden_name',
            'grades.grade as grade_name',
            'companymasters.id as company_id',
            'companymasters.company_name'
         )
         ->orderBy('order_details.id', 'desc')
         ->get();
      $buyer_details = $this->partyModel::where('partys.id', $order->buyer_party)
         ->leftJoin($this->masterdbname . '.country', 'partys.country_id', '=', $this->masterdbname . '.country.id')
         ->leftJoin($this->masterdbname . '.state',   'partys.state_id',   '=', $this->masterdbname . '.state.id')
         ->leftJoin($this->masterdbname . '.city',    'partys.city_id',    '=', $this->masterdbname . '.city.id')
         ->select(
            'partys.*',
            $this->masterdbname . '.country.country_name as country_name',
            $this->masterdbname . '.state.state_name as state_name',
            $this->masterdbname . '.city.city_name as city_name'
         )
         ->first();

      if ($order->transport) {
         $transport_details = $this->partyModel::where('partys.id', $order->transport)
            ->leftJoin($this->masterdbname . '.country', 'partys.country_id', '=', $this->masterdbname . '.country.id')
            ->leftJoin($this->masterdbname . '.state',   'partys.state_id',   '=', $this->masterdbname . '.state.id')
            ->leftJoin($this->masterdbname . '.city',    'partys.city_id',    '=', $this->masterdbname . '.city.id')
            ->select(
               'partys.*',
               $this->masterdbname . '.country.country_name as country_name',
               $this->masterdbname . '.state.state_name as state_name',
               $this->masterdbname . '.city.city_name as city_name'
            )
            ->first();
      } else {
         $transport_details = null;
      }
      $order = [
         'order' => $order,
         'order_details' => $order_details,
         'buyer_details' => $buyer_details,
         'transport_details' => $transport_details
      ];
      $options = [
         'isPhpEnabled' => true,
         'isHtml5ParserEnabled' => true,
         'isRemoteEnabled' => true,
      ];
      // dd($order);
      $pdf = PDF::setOptions($options)->loadView($this->version . '.admin.PDF.orderpdf', ["order" => $order])->setPaper('a4', 'portrait');
      //return view($this->version . '.admin.PDF.orderpdf', ["order" => $order]);
      // return $pdf->download('orderpdf - '.$id. date('Y-m-d_H-i-s') . '.pdf');
      return $pdf->stream('Order Pdf:-' . $id .'Order Date:-'. $order['order']->order_date.'.pdf');
   }
}
