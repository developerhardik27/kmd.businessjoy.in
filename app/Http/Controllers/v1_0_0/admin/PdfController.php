<?php


namespace App\Http\Controllers\v1_0_0\admin;

use App\Http\Controllers\Controller;
use App\Models\company;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;



class PdfController extends Controller
{
   public $version, $invoiceModel, $paymentdetailsModel;
   public function __construct()
   {
      if (session_status() !== PHP_SESSION_ACTIVE)
         session_start();
      if (isset($_SESSION['folder_name'])) {
         $this->version = $_SESSION['folder_name'];
         $this->invoiceModel = 'App\\Models\\' . $this->version . "\\invoice";
         $this->paymentdetailsModel = 'App\\Models\\' . $this->version . "\\payment_details";
      } else {
         $this->invoiceModel = 'App\\Models\\v1_0_0\\invoice';
         $this->paymentdetailsModel = 'App\\Models\\v1_0_0\\payment_details';
      }
   }
   public function generatepdf(string $id)
   {

      $dbname = company::find(Session::get('company_id'));
      config(['database.connections.dynamic_connection.database' => $dbname->dbname]);

      // Establish connection to the dynamic database
      DB::purge('dynamic_connection');
      DB::reconnect('dynamic_connection');

      $invoice = $this->invoiceModel::findOrFail($id);
      $this->authorize('view', $invoice);

      $jsonproductdata = app('App\Http\Controllers\\' . $this->version . '\api\invoiceController')->inv_details($id);
      $jsoninvdata = app('App\Http\Controllers\\' . $this->version . '\api\invoiceController')->index($id);
      $jsoncompanydetailsdata = app('App\Http\Controllers\\' . $this->version . '\api\companyController')->companydetailspdf($invoice->company_details_id);
      $jsonbankdetailsdata = app('App\Http\Controllers\\' . $this->version . '\api\bankdetailsController')->bankdetailspdf($invoice->account_id);


      $jsonproductContent = $jsonproductdata->getContent();
      $jsoninvContent = $jsoninvdata->getContent();
      $jsoncompanyContent = $jsoncompanydetailsdata->getContent();
      $jsonbankContent = $jsonbankdetailsdata->getContent();

      // Decode the JSON data
      $productdata = json_decode($jsonproductContent, true);
      $invdata = json_decode($jsoninvContent, true);
      $companydetailsdata = json_decode($jsoncompanyContent, true);
      $bankdetailsdata = json_decode($jsonbankContent, true);

      // dd($companydetailsdata);

      $data = [
         'productscolumn' => $productdata['columns'],
         'products' => $productdata['invoice'],
         'othersettings' => $productdata['othersettings'][0],
         'invdata' => $invdata['invoice'][0],
         'companydetails' => $companydetailsdata['companydetails'][0],
         'bankdetails' => $bankdetailsdata['bankdetail'][0]

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



      $pdf = PDF::setOptions($options)->loadView($this->version . '.admin.invoicedetail', $data)->setPaper('a4', 'portrait');

      return $pdf->stream('invoice.pdf');


   }
   public function generatereciept(string $id)
   {


      $dbname = company::find(Session::get('company_id'));
      config(['database.connections.dynamic_connection.database' => $dbname->dbname]);

      // Establish connection to the dynamic database
      DB::purge('dynamic_connection');
      DB::reconnect('dynamic_connection');

      $paymentdetail = $this->paymentdetailsModel::findOrFail($id);
      $invoice = $this->invoiceModel::findOrFail($paymentdetail->inv_id);
      $this->authorize('view', $invoice);

      $jsoninvdata = app('App\Http\Controllers\\' . $this->version . '\api\invoiceController')->index($paymentdetail->inv_id);
      $jsonpaymentdata = app('App\Http\Controllers\\' . $this->version . '\api\PaymentController')->paymentdetailsforpdf($id);
      $jsoncompanydetailsdata = app('App\Http\Controllers\\' . $this->version . '\api\companyController')->companydetailspdf($invoice->company_details_id);


      $jsonpaymentContent = $jsonpaymentdata->getContent();
      $jsoninvContent = $jsoninvdata->getContent();
      $jsoncompanyContent = $jsoncompanydetailsdata->getContent();



      // Decode the JSON data
      $paymentdata = json_decode($jsonpaymentContent, true);
      $invdata = json_decode($jsoninvContent, true);
      $companydetailsdata = json_decode($jsoncompanyContent, true);

      $data = [
         'payment' => $paymentdata['paymentdetail'],
         'invdata' => $invdata['invoice'][0],
         'companydetails' => $companydetailsdata['companydetails'][0],
      ];

      $options = [
         'isPhpEnabled' => true,
         'isHtml5ParserEnabled' => true,
         'margin_top' => 0,
         'margin_right' => 0,
         'margin_bottom' => 0,
         'margin_left' => 0,
      ];

      $pdf = PDF::setOptions($options)->loadView($this->version . '.admin.paymentreciept', $data)->setPaper('a4', 'portrait');

      return $pdf->stream();

      // $name =  'reciept'.Str::Random(3).'pdf';
      // $pdf = PDF::setOptions($options)->loadView('admin.invoicedetail',[ 'payment' => $paymentdata['payment']])->setPaper('a4', 'portrait');

      // return $pdf->stream($name);


   }
   public function generaterecieptall(string $id)
   {


      $dbname = company::find(Session::get('company_id'));
      config(['database.connections.dynamic_connection.database' => $dbname->dbname]);

      // Establish connection to the dynamic database
      DB::purge('dynamic_connection');
      DB::reconnect('dynamic_connection');


      $invoice = $this->invoiceModel::findOrFail($id);
      $this->authorize('view', $invoice);

      $jsoninvdata = app('App\Http\Controllers\\' . $this->version . '\api\invoiceController')->index($id);
      $jsonpaymentdata = app('App\Http\Controllers\\' . $this->version . '\api\PaymentController')->index($id);
      $jsoncompanydetailsdata = app('App\Http\Controllers\\' . $this->version . '\api\companyController')->companydetailspdf($invoice->company_details_id);


      $jsonpaymentContent = $jsonpaymentdata->getContent();
      $jsoninvContent = $jsoninvdata->getContent();
      $jsoncompanyContent = $jsoncompanydetailsdata->getContent();



      // Decode the JSON data
      $paymentdata = json_decode($jsonpaymentContent, true);
      $invdata = json_decode($jsoninvContent, true);
      $companydetailsdata = json_decode($jsoncompanyContent, true);

      $data = [
         'payment' => $paymentdata['payment'],
         'invdata' => $invdata['invoice'][0],
         'companydetails' => $companydetailsdata['companydetails'][0],
      ];

      $options = [
         'isPhpEnabled' => true,
         'isHtml5ParserEnabled' => true,
         'margin_top' => 0,
         'margin_right' => 0,
         'margin_bottom' => 0,
         'margin_left' => 0,
      ];

      $pdf = PDF::setOptions($options)->loadView($this->version . '.admin.paymentpaidreciept', $data)->setPaper('a4', 'portrait');

      return $pdf->stream();

      // $name =  'reciept'.Str::Random(3).'pdf';
      // $pdf = PDF::setOptions($options)->loadView('admin.invoicedetail',[ 'payment' => $paymentdata['payment']])->setPaper('a4', 'portrait');

      // return $pdf->stream($name);


   }
}
