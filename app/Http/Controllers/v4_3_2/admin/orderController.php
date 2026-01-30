<?php

namespace App\Http\Controllers\v4_3_2\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;

class orderController extends Controller
{
    public $version, $orderModel;
    public function __construct()
    {
        if (session_status() !== PHP_SESSION_ACTIVE)
            session_start();
        if (isset($_SESSION['folder_name'])) {
            $this->version = $_SESSION['folder_name'];
            $this->orderModel = 'App\\Models\\' . $this->version . "\\order";
        } else {
            $this->orderModel = 'App\\Models\\v4_3_2\\order';
        }
    }
    public function index(Request $request)
    {
        if (isset($request->search)) {
            $search = $request->search;
        } else {
            $search = '';
        }
        return view($this->version . '.admin.order.order', ["search" => $search]);
    }
    public function create()
    {
        request()->merge([
            'company_id' => session('company_id'),
            'user_id' => session('user_id')
        ]);
        $partycontroller = "App\\Http\\Controllers\\" . $this->version . "\\api\\partycontroller";
        $jsonpartydetails = app($partycontroller)->buyerindex();
        $partydetailscontent = $jsonpartydetails->getContent();
        $pdetails = json_decode($partydetailscontent);

        $companymasterController = "App\\Http\\Controllers\\" . $this->version . "\\api\\companymasterController";
        $jsongardendetails = app($companymasterController)->gardenindex();
        $gardendetailscontent = $jsongardendetails->getContent();
        $gardendetails = json_decode($gardendetailscontent);

        if ($gardendetails->status != 200) {
           
            return redirect()->route('admin.gardenform')->with("message", "Please create Garden before creating Order");
       
            }
        if ($pdetails->status != 200) {
            return redirect()->route('admin.partyform')->with("message", "Please create Party before creating Order");
        }
        return view($this->version . '.admin.order.orderform', ['company_id' => Session::get('company_id')]);
    }
    public function edit($id)
    {
        return view($this->version . '.admin.order.orderupdateform', ['edit_id' => $id]);
    }
}
