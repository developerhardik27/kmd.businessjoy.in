<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\bank_detail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class bankdetailsController extends Controller
{
    public function bankdetailspdf(string $id)
    {

        $bankdetail = bank_detail::all()->where('id', $id);
        if ($bankdetail->count() > 0) {
            return response()->json([
                'status' => 200,
                'bankdetail' => $bankdetail
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'bankdetail' => 'No Records Found'
            ]);
        }
    }

    public function bank_details(string $id)
    {

        $bankdetail = bank_detail::all()->where('created_by', $id)->where('is_deleted', 0);
        if ($bankdetail->count() > 0) {
            return response()->json([
                'status' => 200,
                'bankdetail' => $bankdetail
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'bankdetail' => 'No Records Found'
            ]);
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $userId = $request->input('user_id');

        $bankdetail = bank_detail::all()->where('created_by', $userId)->where('is_deleted', 0);

        if ($bankdetail->count() > 0) {
            return response()->json([
                'status' => 200,
                'bankdetail' => $bankdetail
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'bankdetail' => 'No Records Found'
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
            'holder_name' => 'required|string|max:50',
            'branch_name' => 'required|string|max:50',
            'account_number' => 'required|numeric',
            'swift_code' => 'required|string|max:50',
            'ifsc_code' => 'required|string|min:6',
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

            $bankdetail = bank_detail::create([
                'holder_name' => $request->holder_name,
                'branch_name' => $request->branch_name,
                'account_no' => $request->account_number,
                'swift_code' => $request->swift_code,
                'ifsc_code' => $request->ifsc_code,
                'address' => $request->address,
                'created_by' => $request->created_by,

            ]);

            if ($bankdetail) {
                return response()->json([
                    'status' => 200,
                    'message' => 'Bank Details  succesfully added'
                ], 200);
            } else {
                return response()->json([
                    'status' => 500,
                    'message' => 'Bank Details not succesfully added'
                ]);
            }
        }
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


        
        $bankdetail = bank_detail::find($id);

        if ($bankdetail) {
            $bankdetail->update([
                'is_active' => $request->status
            ]);
            return response()->json([
                'status' => 200,
                'message' => 'status succesfully updated'
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'No Such bank Found!'
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $bankdetail = bank_detail::find($id);

        if ($bankdetail) {
            $bankdetail->update([
                'is_deleted' => 1
            ]);
            return response()->json([
                'status' => 200,
                'message' => 'bankdetail succesfully deleted'
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'No Such bank Found!'
            ]);
        }
    }
}
