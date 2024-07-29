<?php


namespace App\Http\Controllers\v1_1_0\admin;

use Illuminate\Support\Facades\Log;
use ZipArchive;
use App\Models\company;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Exception;




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
         $this->invoiceModel = 'App\\Models\\v1_1_0\\invoice';
         $this->paymentdetailsModel = 'App\\Models\\v1_1_0\\payment_details';
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

      if ($productdata['status'] == 404) {
         return redirect()->back()->with('message', 'yes');
      }


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


      $companyname = $data['invdata']['firstname'] . $data['invdata']['lastname']; // if customer company name is not set
      if ($data['invdata']['company_name'] != '') {
         $companyname = $data['invdata']['company_name'];
      }

      // return view($this->version . '.admin.invoicetemplate', $data);
      $pdfname = $data['invdata']['inv_no'] . ' ' . $companyname . ' ' . date('d-M-y') .'.pdf';

      // $pdf = PDF::setOptions($options)->loadView($this->version . '.admin.invoicedetail', $data)->setPaper('a4', 'portrait');
      $pdf = PDF::setOptions($options)->loadView($this->version . '.admin.invoicetemplate', $data)->setPaper('a4', 'portrait');

      // return view($this->version . '.admin.invoicetemplate', $data);
      return $pdf->stream($pdfname);

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

      $jsonproductdata = app('App\Http\Controllers\\' . $this->version . '\api\invoiceController')->inv_details($paymentdetail->inv_id);
      $jsoninvdata = app('App\Http\Controllers\\' . $this->version . '\api\invoiceController')->index($paymentdetail->inv_id);
      $jsonpaymentdata = app('App\Http\Controllers\\' . $this->version . '\api\PaymentController')->paymentdetailsforpdf($id);
      $jsoncompanydetailsdata = app('App\Http\Controllers\\' . $this->version . '\api\companyController')->companydetailspdf($invoice->company_details_id);


      $jsonproductContent = $jsonproductdata->getContent();
      $jsonpaymentContent = $jsonpaymentdata->getContent();
      $jsoninvContent = $jsoninvdata->getContent();
      $jsoncompanyContent = $jsoncompanydetailsdata->getContent();



      // Decode the JSON data
      $productdata = json_decode($jsonproductContent, true);
      $paymentdata = json_decode($jsonpaymentContent, true);
      $invdata = json_decode($jsoninvContent, true);
      $companydetailsdata = json_decode($jsoncompanyContent, true);

      if ($productdata['status'] == 404) {
         return redirect()->back()->with('message', 'yes');
      }

      $data = [
         'productscolumn' => $productdata['columns'],
         'products' => $productdata['invoice'],
         'othersettings' => $productdata['othersettings'][0],
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

      // return view($this->version . '.admin.paymentreciept', $data);
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

      $jsonproductdata = app('App\Http\Controllers\\' . $this->version . '\api\invoiceController')->inv_details($id);
      $jsoninvdata = app('App\Http\Controllers\\' . $this->version . '\api\invoiceController')->index($id);
      $jsonpaymentdata = app('App\Http\Controllers\\' . $this->version . '\api\PaymentController')->index($id);
      $jsoncompanydetailsdata = app('App\Http\Controllers\\' . $this->version . '\api\companyController')->companydetailspdf($invoice->company_details_id);

      $jsonproductContent = $jsonproductdata->getContent();
      $jsonpaymentContent = $jsonpaymentdata->getContent();
      $jsoninvContent = $jsoninvdata->getContent();
      $jsoncompanyContent = $jsoncompanydetailsdata->getContent();



      // Decode the JSON data
      $productdata = json_decode($jsonproductContent, true);
      $paymentdata = json_decode($jsonpaymentContent, true);
      $invdata = json_decode($jsoninvContent, true);
      $companydetailsdata = json_decode($jsoncompanyContent, true);

      if ($productdata['status'] == 404) {
         return redirect()->back()->with('message', 'yes');
      }

      $data = [
         'productscolumn' => $productdata['columns'],
         'products' => $productdata['invoice'],
         'othersettings' => $productdata['othersettings'][0],
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

      // return view($this->version . '.admin.paymentpaidreciept', $data);
      return $pdf->stream();

      // $name =  'reciept'.Str::Random(3).'pdf';
      // $pdf = PDF::setOptions($options)->loadView('admin.invoicedetail',[ 'payment' => $paymentdata['payment']])->setPaper('a4', 'portrait');

      // return $pdf->stream($name);


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

         $startDate = $request->fromdate;
         $endDate = Carbon::parse($request->todate);

         $invoices = $this->invoiceModel::whereBetween('created_at', [$startDate, $endDate->addDay()])
            ->where([
               'is_deleted' => 0, 
            ])
            ->whereIn('created_by', $reportuserlist)
            ->get();

         if (count($invoices) == 0) {
            return back()->with('success', 'Not any invoice exists between this  date');
         }

         $tempDir = storage_path('app/temp_pdf');
         if (!file_exists($tempDir)) {
            mkdir($tempDir, 0777, true);
         }

         foreach ($invoices as $invoice) {
            $data = $this->prepareDataForPDF($invoice);
            $pdf = PDF::loadView($this->version . '.admin.invoicetemplate', $data)->setPaper('a4', 'portrait');
            $pdfFileName = $invoice->inv_no . '_' . $invoice->company_name . '_' . $invoice->created_at->format('d-M-y') . '.pdf';
            $pdf->save($tempDir . '/' . $pdfFileName);
         }

         $zipFileName = 'invoices_' . date('Ymd') . '.zip';
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
         return response()->download(storage_path('app/' . $zipFileName))->deleteFileAfterSend(true);
      } catch (Exception $e) {
         // Log the error
         Log::error($e->getMessage());

         // Return back with an error message
         return back()->with('error', 'Something went wrong while creating the zip file');
      }
   }

   // Helper function to prepare data for PDF generation
   private function prepareDataForPDF($invoice)
   {
      $jsonproductdata = app('App\Http\Controllers\\' . $this->version . '\api\invoiceController')->inv_details($invoice->id);
      $jsoninvdata = app('App\Http\Controllers\\' . $this->version . '\api\invoiceController')->index($invoice->id);
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

      if ($productdata['status'] == 404) {
         return redirect()->back()->with('message', 'yes');
      }

      $data = [
         'productscolumn' => $productdata['columns'],
         'products' => $productdata['invoice'],
         'othersettings' => $productdata['othersettings'][0],
         'invdata' => $invdata['invoice'][0],
         'companydetails' => $companydetailsdata['companydetails'][0],
         'bankdetails' => $bankdetailsdata['bankdetail'][0]
      ];
      return $data;
   }
}
