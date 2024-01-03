<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\state;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class stateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $state = state::all()->where('is_deleted', 0)->where('is_active', 1);

        if ($state->count() > 0) {
            return response()->json([
                'status' => 200,
                'state' => $state
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'state' => 'No Records Found'
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
            'country_id' => 'required|numeric',
            'state_name' => 'required|alpha',
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

            $state = state::create([
                'country_id' =>  $request->country_id,
                'state_name' =>  $request->state_name,
                'created_by' => $request->created_by

            ]);

            if ($state) {
                return response()->json([
                    'status' => 200,
                    'message' => 'state succesfully created'
                ], 200);
            } else {
                return response()->json([
                    'status' => 500,
                    'message' => 'state not succesfully create'
                ]);
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $state = state::get()->where('country_id',$id);
        if ($state->count() > 0) {
            return response()->json([
                'status' => 200,
                'state' => $state
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'state' => $state,
                'message' => "No Such state Found!"
            ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $state = state::find($id);
        if ($state) {
            return response()->json([
                'status' => 200,
                'state' => $state
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => "No Such state Found!"
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'state_id' => 'required|numeric',
            'state_name' => 'required|alpha',
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
            ]);
        } else {

            $state = state::find($id);
            if ($state) {

                $state->update([
                    'country_id' =>  $request->country_id,
                    'state_name' =>  $request->state_name,
                    'updated_by' => $request->updated_by,
                    'updated_at' => date('Y-m-d'),
                    'is_active' =>  $request->is_active
                ]);

                return response()->json([
                    'status' => 200,
                    'message' => 'state succesfully updated'
                ]);
            } else {
                return response()->json([
                    'status' => 404,
                    'message' => 'No Such state Found!'
                ]);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $state = state::find($id);

        if ($state) {
            $state->update([
                'is_deleted' => 1

            ]);
            return response()->json([
                'status' => 200,
                'message' => 'state succesfully deleted'
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'No Such state Found!'
            ]);
        }
    }
}
