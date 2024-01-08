<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\tbl_invoice_column;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class tblinvoicecolumnController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $userId = $request->input('user_id');

        $invoicecolumn = tbl_invoice_column::all()->where('company_id', $userId)->where('is_deleted', 0);

        if ($invoicecolumn->count() > 0) {
            return response()->json([
                'status' => 200,
                'invoicecolumn' => $invoicecolumn
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'invoicecolumn' => 'No Records Found'
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
            'column_name' => 'required|string|max:50',
            'column_type' => 'required|string|max:50',
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
            ]);
        } else {

            $invoicecolumn = tbl_invoice_column::create([
                'column_name' => $request->column_name,
                'column_type' => $request->column_type,
                'company_id' => $request->company_id,
                'created_by' => $request->created_by,

            ]);

            if ($invoicecolumn) {
                return response()->json([
                    'status' => 200,
                    'message' => 'Invoice Columns  succesfully added'
                ], 200);
            } else {
                return response()->json([
                    'status' => 500,
                    'message' => 'Invoice Columns not succesfully added'
                ]);
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $invoicecolumn = tbl_invoice_column::get()->where('company_id', $id);
        if ($invoicecolumn->count() > 0) {
            return response()->json([
                'status' => 200,
                'invoicecolumn' => $invoicecolumn
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'invoicecolumn' => $invoicecolumn,
                'message' => "No Such Invoice Column Found!"
            ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $invoicecolumn = tbl_invoice_column::find($id);
        if ($invoicecolumn->count() > 0) {
            return response()->json([
                'status' => 200,
                'invoicecolumn' => $invoicecolumn
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'invoicecolumn' => $invoicecolumn,
                'message' => "No Such Invoice Column Found!"
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    { 

        $validator = Validator::make($request->all(), [
            'column_name' => 'required|string|max:50',
            'column_type' => 'required|string|max:50',
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

            $invoicecolumn = tbl_invoice_column::find($id);
            if ($invoicecolumn) {

                $invoicecolumn->update([
                    'column_name' => $request->column_name,
                    'column_type' => $request->column_type,
                    'updated_by' => $request->updated_by,
                    'updated_at' => date('Y-m-d'),
                ]);

                return response()->json([
                    'status' => 200,
                    'message' => 'Invoice Column succesfully updated'
                ]);
            } else {
                return response()->json([
                    'status' => 404,
                    'message' => 'No Such Invoice Column Found!'
                ]);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $invoicecolumn = tbl_invoice_column::find($id);

        if ($invoicecolumn) {
            $invoicecolumn->update([
                'is_deleted' => 1
            ]);
            return response()->json([
                'status' => 200,
                'message' => 'Invoice Column succesfully deleted'
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'No Such Invoice Column Found!'
            ]);
        }
    }
}
