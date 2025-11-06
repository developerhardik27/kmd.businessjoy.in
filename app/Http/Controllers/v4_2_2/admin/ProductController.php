<?php

namespace App\Http\Controllers\v4_2_2\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ProductController extends Controller
{
    public $version, $productmodel;
    public function __construct()
    {
        if (session_status() !== PHP_SESSION_ACTIVE)
            session_start();

        if (isset($_SESSION['folder_name'])) {
            $this->version = $_SESSION['folder_name'];
            $this->productmodel = 'App\\Models\\' . $this->version . "\\product";
        } else {
            $this->productmodel = 'App\\Models\\v4_2_2\\product';
        }
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

        return view($this->version . '.admin.Product.product', ["search" => $search]);
    }

    public function productcolumnmapping(){
        return view($this->version . '.admin.Product.productcolumnmapping', ['user_id' => Session::get('user_id'), 'company_id' => Session::get('company_id')]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        return view($this->version . '.admin.Product.productform', ['user_id' => Session::get('user_id'), 'company_id' => Session::get('company_id')]);
    }

   
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {

        // $product = $this->productmodel::findOrFail($id);
        // $this->authorize('view', $product);

        return view($this->version . '.admin.Product.productupdateform', ['user_id' => Session::get('user_id'), 'edit_id' => $id]);
    }

}
