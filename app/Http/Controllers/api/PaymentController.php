<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\payment_details;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(string $id)
    {
      
        $payment = DB::table('payment_details')
                    ->where('inv_id', $id)
                    ->get();

        if ($payment->count() > 0) {
            return response()->json([
                'status' => 200,
                'payment' => $payment
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'payment' => 'No Records Found'
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
            'inv_id' => 'required',
            'transid' => 'required|string|max:50',

        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->messages()
            ]);
        } else {

            $payment_details = payment_details::updateOrCreate(
                ['inv_id' => $request->inv_id],
                [
                    'transaction_id' => $request->transid,
                    'paid_by' => $request->paid_by,
                    'paid_type' => $request->payment_type
                ]
            );

            if ($payment_details) {
                return response()->json([
                    'status' => 200,
                    'message' => 'payment_details succesfully created'
                ]);
            } else {
                return response()->json([
                    'status' => 500,
                    'message' => 'payment_details not succesfully create'
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
