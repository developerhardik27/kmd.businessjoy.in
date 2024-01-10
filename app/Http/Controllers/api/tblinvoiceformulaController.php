<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\tbl_invoice_formula;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class tblinvoiceformulaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $userId = $request->input('user_id');

        $invoiceformula = tbl_invoice_formula::all()->where('company_id', $userId)->where('is_deleted', 0);

        if ($invoiceformula->count() > 0) {
            return response()->json([
                'status' => 200,
                'invoiceformula' => $invoiceformula
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'invoiceformula' => 'No Records Found'
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
       
            $formuladata = $request['formuladata'];
            foreach($formuladata as $key => $value){
                $invoiceformula = tbl_invoice_formula::create([
                    'first_column' => $value[0],
                    'operation' => $value[1],
                    'second_column' =>$value[2],
                    'output_column' => $value[3],
                    'company_id' => $request->company_id,
                    'created_by' => $request->created_by,
                ]);
            }
           

            if ($invoiceformula) {
                return response()->json([
                    'status' => 200,
                    'message' => 'Invoice Formula  succesfully added'
                ], 200);
            } else {
                return response()->json([
                    'status' => 500,
                    'message' => 'Invoice Formula not succesfully added'
                ]);
            }
        
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $invoiceformula = tbl_invoice_formula::get()->where('company_id', $id);
        if ($invoiceformula->count() > 0) {
            return response()->json([
                'status' => 200,
                'invoiceformula' => $invoiceformula
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'invoiceformula' => $invoiceformula,
                'message' => "No Such Invoice Formula Found!"
            ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $invoiceformula = tbl_invoice_formula::find($id);
        if ($invoiceformula->count() > 0) {
            return response()->json([
                'status' => 200,
                'invoiceformula' => $invoiceformula
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'invoiceformula' => $invoiceformula,
                'message' => "No Such Invoice Formula Found!"
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    { 
           
        $validator = Validator::make($request->all(), [
            'first_column' => 'required|string|max:50',
            'operation' => 'required|string|max:50',
            'second_column' => 'required|string',
            'output_column' => 'required|string',
            'updated_by'  => 'required|numeric',
            'created_by',
            'created_at',
            'updated_at',
            'is_active',
            'is_deleted'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->messages()
            ]);
        } else {

            $invoiceformula = tbl_invoice_formula::find($id);
            if ($invoiceformula) {
                date_default_timezone_set('Asia/Kolkata');
                $invoiceformula->update([
                    'first_column' => $request->first_column,
                    'operation' => $request->operation,
                    'second_column' => $request->second_column,
                    'output_column' => $request->output_column,
                    'updated_by' => $request->updated_by,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

                return response()->json([
                    'status' => 200,
                    'message' => 'Invoice Formula succesfully updated'
                ]);
            } else {
                return response()->json([
                    'status' => 404,
                    'message' => 'No Such Invoice Formula Found!'
                ]);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $invoiceformula = tbl_invoice_formula::find($id);

        if ($invoiceformula) {
            $invoiceformula->update([
                'is_deleted' => 1
            ]);
            return response()->json([
                'status' => 200,
                'message' => 'Invoice Formula succesfully deleted'
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'No Such Invoice Formula Found!'
            ]);
        }
    }
}
