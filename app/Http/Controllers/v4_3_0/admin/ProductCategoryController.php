<?php

namespace App\Http\Controllers\v4_3_0\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;

class ProductCategoryController extends Controller
{
    public $version, $productCategoryModel;
    public function __construct()
    {
        if (session_status() !== PHP_SESSION_ACTIVE)
            session_start();

        if (isset($_SESSION['folder_name'])) {
            $this->version = $_SESSION['folder_name'];
            $this->productCategoryModel = 'App\\Models\\' . $this->version . "\\product_category";
        } else {
            $this->productCategoryModel = 'App\\Models\\v4_3_0\\product_category';
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return view($this->version . '.admin.ProductCategory.productcategory');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        return view($this->version . '.admin.ProductCategory.productcategoryform', ['user_id' => Session::get('user_id'), 'company_id' => Session::get('company_id')]);
    }
 
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        return view($this->version . '.admin.ProductCategory.productcategoryupdateform', ['user_id' => Session::get('user_id'), 'edit_id' => $id]);
    }
}
