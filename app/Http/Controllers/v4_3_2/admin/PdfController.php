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
   public $version, $invoiceModel, $paymentdetailsModel, $quotationModel, $consignor_copyModel, $brokerpurchaseModel, $bank_detailsModel, $broker_bill_invoiceModel, $broker_payment_detailsModel, $company_gardenModel, $orderModel, $order_detailModel, $brokerbillinvoiceModel;
   public function __construct()
   {
      if (session_status() !== PHP_SESSION_ACTIVE)
         session_start();
      if (isset($_SESSION['folder_name'])) {
         $this->version = $_SESSION['folder_name'];
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
      } else {
         $this->invoiceModel = 'App\\Models\\v4_3_2\\invoice';
         $this->paymentdetailsModel = 'App\\Models\\v4_3_2\\payment_details';
         $this->quotationModel = 'App\\Models\\v4_3_2\\quotation';
         $this->consignor_copyModel = 'App\\Models\\v4_3_2\\consignor_copy';
         $this->brokerpurchaseModel = 'App\\Models\\v4_3_2\\broker_purchase';
         $this->bank_detailsModel = 'App\\Models\\v4_3_2\\bank_detail';
         $this->broker_bill_invoiceModel = 'App\\Models\\v4_3_2\\broker_bill_invoice';
         $this->broker_payment_detailsModel = 'App\\Models\\v4_3_2\\broker_bill_payment_detail';
         $this->company_gardenModel = 'App\\Models\\v4_3_2\\company_garden';
         $this->orderModel = 'App\\Models\\v4_3_2\\order';
         $this->order_detailModel = 'App\\Models\\v4_3_2\\order_detail';
         $this->brokerbillinvoiceModel = 'App\\Models\\v4_3_2\\broker_bill_invoice';
      }
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

      $gardenCompanyData = $this->brokerpurchaseModel
         ::where('broker_purchases.is_deleted', 0)
         ->where('broker_purchases.garden_id', $invoice->garden_id)
         ->leftJoin('company_garden', 'company_garden.garden_id', '=', 'broker_purchases.garden_id')
         ->leftJoin('companymasters', 'companymasters.id', '=', 'company_garden.company_id')
         ->leftJoin('broker_bill_invoice', function ($join) {
            $join->on('broker_bill_invoice.garden_id', '=', 'broker_purchases.garden_id')
               ->where('broker_bill_invoice.is_deleted', 0);
         })

         ->select(
            'company_garden.company_id as garden_company_id',
            'companymasters.*',
            'broker_bill_invoice.id as invoice_id',
         )
         ->first();

      $bank_details  = $this->bank_detailsModel::first();

      $usedInvoices = $this->brokerpurchaseModel
         ::where('broker_purchases.is_deleted', 0)
         ->where('broker_purchases.garden_id', $invoice->garden_id)
         ->whereBetween(
            'broker_purchases.brokerage_date',
            [$invoice->from_date, $invoice->to_date]
         )

         ->leftJoin('gardens', 'gardens.id', '=', 'broker_purchases.garden_id')
         ->leftJoin('grades', 'grades.id', '=', 'broker_purchases.grade')

         ->join('order_details', function ($join) {
            $join->on('order_details.garden_id', '=', 'broker_purchases.garden_id')
               ->on('order_details.invoice_no', '=', 'broker_purchases.invoice_no');
         })

         ->leftJoin('company_garden', 'company_garden.garden_id', '=', 'broker_purchases.garden_id')
         ->leftJoin('companymasters', 'companymasters.id', '=', 'company_garden.company_id')

         ->join('orders', 'orders.id', '=', 'order_details.order_id')
         ->join('partys as buyer', 'buyer.id', '=', 'orders.buyer_party')
         ->join('partys as transporter', 'transporter.id', '=', 'orders.transport')

         ->leftJoin('invoices', function ($join) {
            $join->on('invoices.company_details_id', '=', 'companymasters.id')
               ->on('invoices.customer_id', '=', 'orders.buyer_party')
               ->whereRaw(
                  'FIND_IN_SET(broker_purchases.id, REPLACE(invoices.sample_ids, \'"\', \'\'))'
               )
               ->where('invoices.is_deleted', 0);
         })
         ->select(
            'broker_purchases.*',
            'gardens.garden_name as garden_name',
            'grades.grade as grade',
            'orders.buyer_party',
            'buyer.name as buyer_name',
            'invoices.inv_no',
            'invoices.inv_date',
            'invoices.sample_ids',
         )
         ->get();
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
      // return view($this->version . '.admin.PDF.invoicetemplate', $data);
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
      $gardenCompanyData = $this->brokerpurchaseModel
         ::where('broker_purchases.is_deleted', 0)
         ->where('broker_purchases.garden_id', $invoice->garden_id)
         ->leftJoin('company_garden', 'company_garden.garden_id', '=', 'broker_purchases.garden_id')
         ->leftJoin('companymasters', 'companymasters.id', '=', 'company_garden.company_id')
         ->leftJoin('broker_bill_invoice', function ($join) {
            $join->on('broker_bill_invoice.garden_id', '=', 'broker_purchases.garden_id');
         })
         ->select(
            'company_garden.company_id as garden_company_id',
            'companymasters.*',
            'broker_bill_invoice.invoice_no as  Bill_no',
            'broker_bill_invoice.invoice_date as  Bill_date',
         )
         ->first();
      $bank_details  = $this->bank_detailsModel::first();
      $paymentdetail = $this->broker_payment_detailsModel::where('inv_id', $id)->where('is_deleted', 0)->get();
      $usedInvoices = $this->brokerpurchaseModel
         ::where('broker_purchases.is_deleted', 0)
         ->where('broker_purchases.garden_id', $invoice->garden_id)
         ->leftJoin('gardens', 'gardens.id', '=', 'broker_purchases.garden_id')
         ->leftJoin('grades', 'grades.id', '=', 'broker_purchases.grade')
         ->join('order_details', function ($join) {
            $join->on('order_details.garden_id', '=', 'broker_purchases.garden_id')
               ->on('order_details.invoice_no', '=', 'broker_purchases.invoice_no');
         })
         ->leftJoin('company_garden', 'company_garden.garden_id', '=', 'broker_purchases.garden_id')
         ->leftJoin('companymasters', 'companymasters.id', '=', 'company_garden.company_id')
         ->join('orders', 'orders.id', '=', 'order_details.order_id')
         ->join('partys as buyer', 'buyer.id', '=', 'orders.buyer_party')
         ->join('partys as transporter', 'transporter.id', '=', 'orders.transport')
         ->leftJoin('invoices', function ($join) {
            $join->on('invoices.company_details_id', '=', 'companymasters.id')
               ->on('invoices.customer_id', '=', 'orders.buyer_party')
               ->where('invoices.is_deleted', '=', 0);
         })
         ->whereBetween('broker_purchases.brokerage_date', [$invoice->from_date, $invoice->to_date])

         ->select(
            'broker_purchases.*',
            'gardens.garden_name as garden_name',
            'grades.grade as grade',
            'orders.buyer_party',
            'buyer.name as buyer_name',
            'invoices.inv_no',
            'invoices.inv_date',
         )
         ->get();

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

      $gardenCompanyData = $this->brokerpurchaseModel
         ::where('broker_purchases.is_deleted', 0)
         ->where('broker_purchases.garden_id', $invoice->garden_id)
         ->leftJoin('company_garden', 'company_garden.garden_id', '=', 'broker_purchases.garden_id')
         ->leftJoin('companymasters', 'companymasters.id', '=', 'company_garden.company_id')
         ->leftJoin('broker_bill_invoice', function ($join) {
            $join->on('broker_bill_invoice.garden_id', '=', 'broker_purchases.garden_id');
         })
         ->select(
            'company_garden.company_id as garden_company_id',
            'companymasters.*',
            'broker_bill_invoice.invoice_no as  Bill_no',
            'broker_bill_invoice.invoice_date as  Bill_date',
         )
         ->first();
      $bank_details  = $this->bank_detailsModel::first();
      $paymentdetail = $this->broker_payment_detailsModel::where('id', $id)->where('is_deleted', 0)->get();
      $usedInvoices = $this->brokerpurchaseModel
         ::where('broker_purchases.is_deleted', 0)
         ->where('broker_purchases.garden_id', $invoice->garden_id)
         ->leftJoin('gardens', 'gardens.id', '=', 'broker_purchases.garden_id')
         ->leftJoin('grades', 'grades.id', '=', 'broker_purchases.grade')
         ->join('order_details', function ($join) {
            $join->on('order_details.garden_id', '=', 'broker_purchases.garden_id')
               ->on('order_details.invoice_no', '=', 'broker_purchases.invoice_no');
         })
         ->leftJoin('company_garden', 'company_garden.garden_id', '=', 'broker_purchases.garden_id')
         ->leftJoin('companymasters', 'companymasters.id', '=', 'company_garden.company_id')
         ->join('orders', 'orders.id', '=', 'order_details.order_id')
         ->join('partys as buyer', 'buyer.id', '=', 'orders.buyer_party')
         ->join('partys as transporter', 'transporter.id', '=', 'orders.transport')
         ->leftJoin('invoices', function ($join) {
            $join->on('invoices.company_details_id', '=', 'companymasters.id')
               ->on('invoices.customer_id', '=', 'orders.buyer_party')
               ->where('invoices.is_deleted', '=', 0);
         })
         ->whereBetween('broker_purchases.brokerage_date', [$invoice->from_date, $invoice->to_date])
         ->select(
            'broker_purchases.*',
            'gardens.garden_name as garden_name',
            'grades.grade as grade',
            'orders.buyer_party',
            'buyer.name as buyer_name',
            'invoices.inv_no',
            'invoices.inv_date',
         )
         ->get();

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
      $jsonbankdetailsdata = app('App\Http\Controllers\\' . $this->version . '\api\bankdetailsController')->bankdetailspdf($invoice->account_id);

      // this get form data is product data
      $jsonproductContent = $jsonproductdata->getContent();

      //this form data is invoice data
      $jsoninvformdata = $jsoninvdata->getContent();
      //this get copmany details data
      $jsoncompanymasterContent = $jsoncompanydetailsdata->getContent();

      //this get transport details data
      $jsontransportContent = $jsontransportdata->getContent();
      // this get bank details data
      $jsonbankContent = $jsonbankdetailsdata->getContent();

      // Decode the JSON data
      $productdata = json_decode($jsonproductContent, true);
      $invdata = json_decode($jsoninvformdata, true);
      $companydetailsdata = json_decode($jsoncompanymasterContent, true);
      $transportdata = json_decode($jsontransportContent, true);
      $bankdetailsdata = json_decode($jsonbankContent, true);




      if ($productdata['status'] == 404) {
         session()->flash('custom_error_message', 'Product column not found');
         abort('404');
      }
      if ($transportdata['status'] == 404) {
         session()->flash('custom_error_message', 'Transport details not found');
         abort('404');
      }
      if ($bankdetailsdata['status'] == 404) {
         session()->flash('custom_error_message', 'Bank details not found');
         abort('404');
      }

      if ($bankdetailsdata['status'] == 500) {
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
         'transportdetails' => $transportdata['party'],
         'bankdetails' => $bankdetailsdata['bankdetail'][0],
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
      $jsonbankdetailsdata = app('App\Http\Controllers\\' . $this->version . '\api\bankdetailsController')->bankdetailspdf($invoice->account_id);

      // this get form data is product data
      $jsonproductContent = $jsonproductdata->getContent();

      //this form data is invoice data
      $jsoninvformdata = $jsoninvdata->getContent();
      //this get copmany details data
      $jsoncompanymasterContent = $jsoncompanydetailsdata->getContent();

      //this get transport details data
      $jsontransportContent = $jsontransportdata->getContent();
      // this get bank details data
      $jsonbankContent = $jsonbankdetailsdata->getContent();

      // Decode the JSON data
      $productdata = json_decode($jsonproductContent, true);
      $invdata = json_decode($jsoninvformdata, true);
      $companydetailsdata = json_decode($jsoncompanymasterContent, true);
      $transportdata = json_decode($jsontransportContent, true);
      $bankdetailsdata = json_decode($jsonbankContent, true);
      //   dd($transportdata);

      if ($productdata['status'] == 404) {
         session()->flash('custom_error_message', 'Product column not found');
         abort('404');
      }
      if ($transportdata['status'] == 404) {
         session()->flash('custom_error_message', 'Transport details not found');
         abort('404');
      }
      if ($bankdetailsdata['status'] == 404) {
         session()->flash('custom_error_message', 'Bank details not found');
         abort('404');
      }

      if ($bankdetailsdata['status'] == 500) {
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
         'transportdetails' => $transportdata['party'],
         'bankdetails' => $bankdetailsdata['bankdetail'][0]
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
         ->join('partys as transport', 'transport.id', 'orders.transport')
         ->join('order_details', 'order_details.order_id', 'orders.id')
         ->join('gardens', 'gardens.id', 'order_details.garden_id')
         ->join('grades', 'grades.id', 'order_details.grade')
         ->where("orders.is_deleted", 0);
      $filters = [
         'filter_transport'      => 'orders.transport',
         'filter_buyer'        => 'orders.buyer_party',
         'filter_garden'       => 'order_details.garden_id',
         'filter_grade'        => 'order_details.grade',
         'filter_credit_days_from'  => 'orders.credit_days',
         'filter_credit_days_to'    => 'orders.credit_days',
         'filter_final_amount_from'    => 'orders.finalAmount',
         'filter_final_amount_to'      => 'orders.finalAmount',
      ];
      foreach ($filters as $requestKey => $column) {
         $value = $request->$requestKey;

         if (isset($value)) {
            if ($requestKey == 'filter_credit_days_from' || $requestKey == 'filter_credit_days_to' || $requestKey == 'filter_final_amount_from' || $requestKey == 'filter_final_amount_to') {
               $operator = strpos($requestKey, 'from') !== false ? '>=' : '<=';
               $order->where($column, $operator, $value);
            } else if (strpos($requestKey, 'from') !== false || strpos($requestKey, 'to') !== false) {
               $operator = strpos($requestKey, 'from') !== false ? '>=' : '<=';
               $order->whereDate($column, $operator, $value);
            } else {

               $order->whereIn($column, $value);
            }
         }
      }


      $order = $order
         ->select(
            'orders.id as order_id',
            'buyer.name as buyer_name',
            'transport.name as transport_name',
            'orders.*',
            'order_details.*',
            'gardens.garden_name as garden_name',
            'grades.grade as grade_name'
         )
         ->get()
         ->groupBy('order_id')
         ->map(function ($details, $orderId) {
            // Map each order to an 'auto-tuple' style array
            $first = $details->first();
            return [
               'id' => $orderId,
               'buyer_name' => $first->buyer_name,
               'transport_name' => $first->transport_name,
               'discount' => $first->discount,
               'totalNetKg' => $first->totalNetKg,
               'credit_days' => $first->credit_days,
               'final_amount' => $first->finalAmount,
               'details' => $details->map(function ($item) {
                  return [
                     'garden_name' => $item->garden_name,
                     'grade_name' => $item->grade_name,
                     'invoice_no' => $item->invoice_no,
                     'bags' => $item->bags,
                     'kg' => $item->kg,
                     'net_kg' => $item->net_kg,
                     'rate' => $item->rate,
                     'amount' => $item->amount,
                  ];
               })->toArray()
            ];
         })
         ->values();
      // dd($order);
      if ($order->isEmpty()) {
         return $this->successresponse(500, 'message', 'Not genrate pdf data is Empty!');
      }

      $options = [
         'isPhpEnabled' => true,
         'isHtml5ParserEnabled' => true,
         'margin_top' => 0,
         'margin_right' => 0,
         'margin_bottom' => 0,
         'margin_left' => 0,
      ];

      $pdf = PDF::setOptions($options)->loadView($this->version . '.admin.PDF.orderreport', ["order" => $order])->setPaper('a4', 'portrait');

      $name = 'Order-Report.pdf';
      // return view($this->version . '.admin.PDF.orderreport', ["order" => $order]);
      return $pdf->download('order_report_' . date('Y-m-d_H-i-s') . '.pdf');
      return $pdf->stream($name);
   }

   public function outstanding(Request $request)
   {
      $list = $this->brokerbillinvoiceModel
         ::leftJoin('broker_bill_payment_details', 'broker_bill_payment_details.inv_id', '=', 'broker_bill_invoice.id')
         ->join('gardens', 'gardens.id', '=', 'broker_bill_invoice.garden_id')
         ->where('broker_bill_invoice.is_deleted', 0);
      $filters = [
         'filter_payment_status' => 'broker_bill_invoice.status',
         'filter_garden' => 'broker_bill_invoice.garden_id',
      ];
      foreach ($filters as $requestKey => $column) {
         $value = $request->$requestKey;

         if (isset($value)) {
            if ($requestKey == 'filter_credit_days_from' || $requestKey == 'filter_credit_days_to' || $requestKey == 'filter_final_amount_from' || $requestKey == 'filter_final_amount_to') {
               $operator = strpos($requestKey, 'from') !== false ? '>=' : '<=';
               $list->where($column, $operator, $value);
            } else if (strpos($requestKey, 'from') !== false || strpos($requestKey, 'to') !== false) {
               $operator = strpos($requestKey, 'from') !== false ? '>=' : '<=';
               $list->whereDate($column, $operator, $value);
            } else {

               $list->whereIn($column, $value);
            }
         }
      }
      $list = $list
         ->select(
            'broker_bill_invoice.id as invoice_id',
            'broker_bill_invoice.invoice_no',
            'broker_bill_invoice.invoice_date',
            'broker_bill_invoice.totalamount',
            'broker_bill_invoice.igst',
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
            'broker_bill_payment_details.pending_amount'
         )
         ->get()
         ->groupBy('invoice_id')
         ->map(function ($rows, $invoiceId) {
            $first = $rows->first();
            return [
               'id'            => $invoiceId,
               'invoice_no'    => $first->invoice_no,
               'invoice_date'  => $first->invoice_date,
               'totalamount'  => $first->totalamount,
               'igst'          => $first->igst,
               'grand_total'   => $first->grand_total,
               'status'        => $first->status,
               'from_date'     => $first->from_date,
               'to_date'       => $first->to_date,
               'garden_name'  => $first->garden_name,
               'details' => $rows->map(function ($item) {
                  return [
                     'receipt_number' => $item->receipt_number,
                     'transaction_id' => $item->transaction_id,
                     'datetime'       => $item->datetime,
                     'paid_by'        => $item->paid_by,
                     'paid_type'      => $item->paid_type,
                     'paid_amount'    => $item->paid_amount,
                     'pending_amount' => $item->pending_amount,
                  ];
               })->toArray()
            ];
         })
         ->values();

      if ($list->isEmpty()) {
         return $this->successresponse(500, 'message', 'Not genrate pdf data is Empty!');
      }

      $options = [
         'isPhpEnabled' => true,
         'isHtml5ParserEnabled' => true,
         'margin_top' => 0,
         'margin_right' => 0,
         'margin_bottom' => 0,
         'margin_left' => 0,
      ];
      $gardenNames = $list->pluck('garden_name')->unique()->values();

      $pdf = PDF::setOptions($options)->loadView($this->version . '.admin.PDF.outstanding', ["list" => $list, 'gardenNames' => $gardenNames])->setPaper('a4', 'portrait');

      if ($gardenNames->count() === 1) {
         $name = $gardenNames[0];
      } else {
         $name = $gardenNames->implode('-');
      }

      //return view($this->version . '.admin.PDF.outstanding', ["list" => $list]);
      return $pdf->download($name . '-Garden_Outstanding_' . date('Y-m-d_H-i-s') . '.pdf');
      return $pdf->stream($name);
   }
}
