<?php

namespace App\Http\Controllers\v2_0_0\api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;


class productController extends commonController
{

    public $userId, $companyId, $masterdbname, $rp, $productModel;

    public function __construct(Request $request)
    {
        $this->dbname($request->company_id);
        $this->companyId = $request->company_id;
        $this->userId = $request->user_id;
        $this->masterdbname = DB::connection()->getDatabaseName();

        // **** for checking user has permission to action on all data 
        $user_rp = DB::connection('dynamic_connection')->table('user_permissions')->select('rp')->where('user_id', $this->userId)->get();
        $permissions = json_decode($user_rp, true);
        $this->rp = json_decode($permissions[0]['rp'], true);

        $this->productModel = $this->getmodel('product');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $productres = $this->productModel::join($this->masterdbname . '.company', 'products.company_id', '=', $this->masterdbname . '.company.id')
            ->join($this->masterdbname . '.company_details', $this->masterdbname . '.company.company_details_id', '=', $this->masterdbname . '.company_details.id')
            ->select('products.id', 'products.name', 'products.description', 'products.product_code', 'products.unit', 'products.price_per_unit', 'company_details.name as company_name', 'products.created_by', 'products.updated_by', 'products.created_at', 'products.updated_at', 'products.is_active', 'products.is_deleted')
            ->where('products.is_deleted', 0)->where('products.is_active', 1);

        if ($this->rp['inventorymodule']['product']['alldata'] != 1) {
            $productres->where('products.created_by', $this->userId);
        }

        $product = $productres->get();

        // condition for check if user has permission to view records
        if ($this->rp['inventorymodule']['product']['view'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }
        if ($product->count() > 0) {
            return $this->successresponse(200, 'product', $product);
        } else {
            return $this->successresponse(404, 'product', 'No Records Found');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'name' => 'required|string|max:50',
            'description' => 'required|string|max:255',
            'product_code' => 'required|max:50',
            'unit' => 'required',
            'price_per_unit' => 'required|numeric',
            'company_id' => 'required|numeric',
            'user_id' => 'required|numeric',
            'updated_by',
            'created_at',
            'updated_at',
            'is_active',
            'is_deleted'
        ]);

        if ($validator->fails()) {
            return $this->errorresponse(422, $validator->messages());
        } else {

            // condition for check if user has permission to add new records
            if ($this->rp['inventorymodule']['product']['add'] != 1) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }

            $product = $this->productModel::create([
                'name' => $request->name,
                'description' => $request->description,
                'product_code' => $request->product_code,
                'unit' => $request->unit,
                'price_per_unit' => $request->price_per_unit,
                'company_id' => $this->companyId,
                'created_by' => $this->userId,
            ]);

            if ($product) {
                return $this->successresponse(200, 'message', 'product  succesfully created');
            } else {
                return $this->successresponse(500, 'message', 'product not succesfully created');
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = $this->productModel::find($id);

        if ($this->rp['inventorymodule']['product']['alldata'] != 1) {
            if ($product->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }
        //condition for check if user has permission to search record
        if ($this->rp['inventorymodule']['product']['view'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }
        if ($product) {
            return $this->successresponse(200, 'product', $product);
        } else {
            return $this->successresponse(404, 'message', "No Such product Found!");
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $product = $this->productModel::find($id);

        if ($this->rp['inventorymodule']['product']['alldata'] != 1) {
            if ($product->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }
        //condition for check if user has permission to edit record
        if ($this->rp['inventorymodule']['product']['edit'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }
        if ($product) {
            return $this->successresponse(200, 'meproductssage', $product);
        } else {
            return $this->successresponse(404, 'message', "No Such product Found!");
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [

            'name' => 'required|string|max:50',
            'description' => 'required|string|max:255',
            'product_code' => 'required|max:50',
            'unit' => 'required',
            'price_per_unit' => 'required|numeric',
            'created_by',
            'user_id' => 'required|numeric',
            'created_at',
            'updated_at',
            'is_active',
            'is_deleted'
        ]);

        if ($validator->fails()) {
            return $this->errorresponse(422, $validator->messages());
        } else {
            $product = $this->productModel::find($id);

            if ($this->rp['inventorymodule']['product']['alldata'] != 1) {
                if ($product->created_by != $this->userId) {
                    return $this->successresponse(500, 'message', 'You are Unauthorized');
                }
            }
            //condition for check if user has permission to edit record
            if ($this->rp['inventorymodule']['product']['edit'] != 1) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
            if ($product) {

                $product->update([
                    'name' => $request->name,
                    'description' => $request->description,
                    'product_code' => $request->product_code,
                    'unit' => $request->unit,
                    'price_per_unit' => $request->price_per_unit,
                    'updated_by' => $this->userId,
                    'updated_at' => date('Y-m-d')
                ]);
                return $this->successresponse(200, 'message', 'product succesfully updated');
            } else {
                return $this->successresponse(404, 'message', 'No Such product Found!');
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = $this->productModel::find($id);
        if ($this->rp['inventorymodule']['product']['alldata'] != 1) {
            if ($product->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }
        //condition for check if user has permission to delete record
        if ($this->rp['inventorymodule']['product']['delete'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }
        if ($product) {
            $product->update([
                'is_deleted' => 1

            ]);
            return $this->successresponse(200, 'message', 'product succesfully deleted');
        } else {
            return $this->successresponse(404, 'message', 'No Such product Found!');
        }
    }
}
