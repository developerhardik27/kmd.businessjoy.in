<?php

namespace App\Http\Controllers\v1_1_1\admin;

use App\Http\Controllers\Controller;
use App\Models\company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class InvoiceController extends Controller
{

    public $version, $invoiceModel;
    public function __construct()
    {
        if (session_status() !== PHP_SESSION_ACTIVE)
            session_start();
        if (isset($_SESSION['folder_name'])) {
            $this->version = $_SESSION['folder_name'];
            $this->invoiceModel = 'App\\Models\\' . $this->version . "\\invoice";
        } else {
            $this->invoiceModel = 'App\\Models\\v1_1_1\\invoice';
        }
    }
    public function invoiceview(string $id)
    {

        $dbname = company::find(Session::get('company_id'));
        config(['database.connections.dynamic_connection.database' => $dbname->dbname]);

        // Establish connection to the dynamic database
        DB::purge('dynamic_connection');
        DB::reconnect('dynamic_connection');

        $invoice = $this->invoiceModel::findOrFail($id);
        $this->authorize('view', $invoice);
        $companyController = "App\\Http\\Controllers\\" . $this->version . "\\api\\companyController";
        $bankdetailsController = "App\\Http\\Controllers\\" . $this->version . "\\api\\bankdetailsController";
        $jsoncompanydetailsdata = app($companyController)->companydetailspdf($invoice->company_details_id);
        $jsonbankdetailsdata = app($bankdetailsController)->bankdetailspdf($invoice->account_id);

        $jsoncompanyContent = $jsoncompanydetailsdata->getContent();
        $jsonbankContent = $jsonbankdetailsdata->getContent();

        $companydetailsdata = json_decode($jsoncompanyContent, true);
        $bankdetailsdata = json_decode($jsonbankContent, true);

        $data = [
            'companydetails' => $companydetailsdata['companydetails'][0],
            'bankdetails' => $bankdetailsdata['bankdetail'][0]

        ];

        return view($this->version . '.admin.invoiceview', ['id' => $id, 'data' => $data]);
    }

    /**
     * Invoice settings pages.
     */
    public function managecolumn()
    {
        return view($this->version . '.admin.managecolumn', ['user_id' => Session::get('user_id'), 'company_id' => Session::get('company_id')]);
    }
    public function formula()
    {
        return view($this->version . '.admin.formula', ['user_id' => Session::get('user_id'), 'company_id' => Session::get('company_id')]);
    }
    public function othersettings()
    {
        return view($this->version . '.admin.othersettings', ['user_id' => Session::get('user_id'), 'company_id' => Session::get('company_id')]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (isset($request->search)) {
            $search = $request->search;
        } else {
            $search = '';
        }

        return view($this->version . '.admin.invoice', ['search' => $search]);
    }



    /**
     * Show the form for creating a new resource.
     */

    public function create()
    {
        $company_id = Session::get('company_id');
        $bankdetailsController = "App\\Http\\Controllers\\" . $this->version . "\\api\\bankdetailsController";
        $jsonbankdetails = app($bankdetailsController)->bank_details($company_id);
        $bdetailscontent = $jsonbankdetails->getContent();
        $bdetails = json_decode($bdetailscontent);

        $invoicecolumnController = "App\\Http\\Controllers\\" . $this->version . "\\api\\tblinvoicecolumnController";
        $jsoncolumndetails = app($invoicecolumnController)->column_details($company_id);
        $columncontent = $jsoncolumndetails->getContent();
        $columndetails = json_decode($columncontent);

        $invoiceothersettingController = "App\\Http\\Controllers\\" . $this->version . "\\api\\tblinvoiceothersettingController";
        $jsoninvoiceothersettingdetails = app($invoiceothersettingController)->invoicenumberpatternindex($company_id);
        $invoiceothersettingcontent = $jsoninvoiceothersettingdetails->getContent();
        $invoiceothersettingdetails = json_decode($invoiceothersettingcontent);

        if ($invoiceothersettingdetails->status != 200 || count($invoiceothersettingdetails->pattern) < 2) {
            return view($this->version . '.admin.othersettings', ['user_id' => Session::get('user_id'), 'company_id' => Session::get('company_id'), 'message' => 'yes']);
        }
        if ($bdetails->status != 200) {
            return view($this->version . '.admin.bankform', ['user_id' => Session::get('user_id'), 'company_id' => Session::get('company_id'), 'message' => 'yes']);
        }
        if ($columndetails->status != 200) {
            return view($this->version . '.admin.managecolumn', ['user_id' => Session::get('user_id'), 'company_id' => Session::get('company_id'), 'message' => 'yes']);
        }
        return view($this->version . '.admin.invoiceform', ['user_id' => Session::get('user_id'), 'company_id' => Session::get('company_id')]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {

        $invoice = $this->invoiceModel::findOrFail($id);
        
        $is_editable = $invoice->is_editable;

        return view($this->version . '.admin.invoiceupdateform', ['edit_id' => $id, 'user_id' => Session::get('user_id'), 'company_id' => Session::get('company_id'), 'is_editable' => $is_editable]);
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
        //
    }
}
