<?php

namespace App\Http\Controllers\v4_2_2\admin;

use App\Http\Controllers\Controller;
use App\Models\company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class QuotationController extends Controller
{

    public $version, $quotationModel;
    public function __construct()
    {
        if (session_status() !== PHP_SESSION_ACTIVE)
            session_start();
        if (isset($_SESSION['folder_name'])) {
            $this->version = $_SESSION['folder_name'];
            $this->quotationModel = 'App\\Models\\' . $this->version . "\\quotation";
        } else {
            $this->quotationModel = 'App\\Models\\v4_2_2\\quotation';
        }
    }
     
    /**
     * quotation settings pages.
     */
    public function managecolumn()
    {
        return view($this->version . '.admin.Quotation.quotationmanagecolumn', ['user_id' => Session::get('user_id'), 'company_id' => Session::get('company_id')]);
    }
    public function formula()
    {
        return view($this->version . '.admin.Quotation.quotationformula', ['user_id' => Session::get('user_id'), 'company_id' => Session::get('company_id')]);
    }
    public function othersettings()
    {
        return view($this->version . '.admin.Quotation.quotationothersettings', ['user_id' => Session::get('user_id'), 'company_id' => Session::get('company_id')]);
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

        return view($this->version . '.admin.Quotation.quotation', ['search' => $search]);
    }



    /**
     * Show the form for creating a new resource.
     */

    public function create()
    {
        $company_id = Session::get('company_id'); 

        $quotationcolumnController = "App\\Http\\Controllers\\" . $this->version . "\\api\\tblquotationcolumnController";
        $jsoncolumndetails = app($quotationcolumnController)->column_details($company_id);
        $columncontent = $jsoncolumndetails->getContent();
        $columndetails = json_decode($columncontent);

        $quotationothersettingController = "App\\Http\\Controllers\\" . $this->version . "\\api\\tblquotationothersettingController";
        $jsonquotationothersettingdetails = app($quotationothersettingController)->quotationnumberpatternindex($company_id);
        $quotationothersettingcontent = $jsonquotationothersettingdetails->getContent();
        $quotationothersettingdetails = json_decode($quotationothersettingcontent);

        if ($quotationothersettingdetails->status != 200 || count($quotationothersettingdetails->pattern) < 2) {
            return view($this->version . '.admin.Quotation.quotationothersettings', ['user_id' => Session::get('user_id'), 'company_id' => Session::get('company_id'), 'message' => 'yes']);
        }
       
        if ($columndetails->status != 200) {
            return view($this->version . '.admin.Quotation.quotationmanagecolumn', ['user_id' => Session::get('user_id'), 'company_id' => Session::get('company_id'), 'message' => 'yes']);
        }
        return view($this->version . '.admin.Quotation.quotationform    ', ['user_id' => Session::get('user_id'), 'company_id' => Session::get('company_id')]);
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {

        $quotation = $this->quotationModel::findOrFail($id);
        
        $is_editable = $quotation->is_editable;

        return view($this->version . '.admin.Quotation.quotationupdateform', ['edit_id' => $id, 'user_id' => Session::get('user_id'), 'company_id' => Session::get('company_id'), 'is_editable' => $is_editable]);
    }

}
