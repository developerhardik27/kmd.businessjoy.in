<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\company;
use App\Models\invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class InvoiceController extends Controller
{
    public function invoiceview(string $id)
    {
      
        $dbname = company::find(Session::get('company_id'));
        config(['database.connections.dynamic_connection.database' => $dbname->dbname]);

        // Establish connection to the dynamic database
        DB::purge('dynamic_connection');
        DB::reconnect('dynamic_connection');

        $invoice = invoice::findOrFail($id);
        $this->authorize('view', $invoice);
        $jsoncompanydetailsdata = app('App\Http\Controllers\api\companyController')->companydetailspdf($invoice->company_details_id);
        $jsonbankdetailsdata = app('App\Http\Controllers\api\bankdetailsController')->bankdetailspdf($invoice->account_id);

        $jsoncompanyContent = $jsoncompanydetailsdata->getContent();
        $jsonbankContent = $jsonbankdetailsdata->getContent();

        $companydetailsdata = json_decode($jsoncompanyContent, true);
        $bankdetailsdata = json_decode($jsonbankContent, true);

        $data = [
            'companydetails' =>  $companydetailsdata['companydetails'][0],
            'bankdetails' =>  $bankdetailsdata['bankdetail'][0]

        ];

        return view('admin.invoiceview', ['id' => $id, 'data' => $data]);
    }

    /**
     * Invoice settings pages.
     */
    public function managecolumn(){
        return view('admin.managecolumn',['user_id' => Session::get('user_id'), 'company_id' => Session::get('company_id')]);
    }
    public function formula(){
        return view('admin.formula',['user_id' => Session::get('user_id'), 'company_id' => Session::get('company_id')]);
    }
    public function othersettings(){
        return view('admin.othersettings',['user_id' => Session::get('user_id'), 'company_id' => Session::get('company_id')]);
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

        return view('admin.invoice', ['search' => $search]);
    }



    /**
     * Show the form for creating a new resource.
     */

    public function create()
    {  
        $company_id = Session::get('company_id');
        $jsonbankdetails =  app('App\Http\Controllers\api\bankdetailsController')->bank_details($company_id);
        $bdetailscontent = $jsonbankdetails->getContent();
        $bdetails = json_decode($bdetailscontent);

        if ($bdetails->status === 200) {
            return view('admin.invoiceform', ['user_id' => Session::get('user_id'), 'company_id' => Session::get('company_id')]);
        } else {
            return view('admin.bankform', ['user_id' => Session::get('user_id'), 'company_id' => Session::get('company_id'), 'message' => 'yes']);
        }
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
        //
    }
}
