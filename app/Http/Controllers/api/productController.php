<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class productController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $companyid = $request->input('company_id');
        
        if ($companyid == 1) {
            $product = product::join('company','products.company_id','=','company.id')
            ->join('company_details','company.company_details_id','=','company_details.id')
            ->select('products.id','products.name','products.description','products.product_code','products.unit','products.price_per_unit','company_details.name as company_name','products.created_by','products.updated_by','products.created_at','products.updated_at','products.is_active','products.is_deleted')
            ->get()
            ->where('is_deleted', 0)->where('is_active', 1);
        }else{

            $product = product::join('company','products.company_id','=','company.id')
            ->join('company_details','company.company_details_id','=','company_details.id')
            ->select('products.id','products.name','products.description','products.product_code','products.unit','products.price_per_unit','company_details.name as company_name','products.created_by','products.updated_by','products.created_at','products.updated_at','products.is_active','products.is_deleted')
            ->where('products.is_deleted', 0)->where('products.is_active', 1)->where('products.company_id',$companyid)
            ->get();
        }
            
        if ($product->count() > 0) {
            return response()->json([
                'status' => 200,
                'product' => $product
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'product' => 'No Records Found'
            ]);
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
            'created_by' => 'required|numeric',
            'updated_by',
            'created_at',
            'updated_at',
            'is_active',
            'is_deleted'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->messages()
            ], 422);
        } else {

            $product = product::create([
                'name' => $request->name,
                'description' => $request->description,
                'product_code' => $request->product_code,
                'unit' => $request->unit,
                'price_per_unit' => $request->price_per_unit,
                'company_id' => $request->company_id,
                'created_by' => $request->created_by,
            ]);

            if ($product) {
                return response()->json([
                    'status' => 200,
                    'message' => 'product  succesfully created'
                ], 200);
            } else {
                return response()->json([
                    'status' => 500,
                    'message' => 'product not succesfully created'
                ], 500);
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = product::find($id);
        
        if ($product) {
            return response()->json([
                'status' => 200,
                'product' => $product
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => "No Such product Found!"
            ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $product = product::find($id);
        if ($product) {
            return response()->json([
                'status' => 200,
                'product' => $product
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'message' => "No Such product Found!"
            ]);
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
            'updated_by' => 'required|numeric',
            'created_at',
            'updated_at',
            'is_active',
            'is_deleted'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->messages()
            ], 422);
        } else {
            $product = product::find($id);

            if ($product) {

                $product->update([
                    'name' => $request->name,
                    'description' => $request->description,
                    'product_code' => $request->product_code,
                    'unit' => $request->unit,
                    'price_per_unit' => $request->price_per_unit,
                    'updated_by' => $request->updated_by,
                    'updated_at' => date('Y-m-d')
                ]);

                return response()->json([
                    'status' => 200,
                    'message' => 'product succesfully updated'
                ], 200);
            } else {
                return response()->json([
                    'status' => 404,
                    'message' => 'No Such product Found!'
                ], 404);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = product::find($id);

        if ($product) {
            $product->update([ 
               'is_deleted' => 1

            ]);
            return response()->json([
                'status' => 200,
                'message' => 'product succesfully deleted'
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'No Such product Found!'
            ]);
        }
    }
}
