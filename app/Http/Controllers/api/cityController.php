<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\city;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class cityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $city = city::all()->where('is_deleted', 0)->where('is_active', 1);

        if ($city->count() > 0) {
            return response()->json([
                'status' => 200,
                'city' => $city
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'city' => 'No Records Found'
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
            'state_id' => 'required|numeric',
            'city_name' => 'required|alpha',
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

            $city = city::create([
                'state_id' =>  $request->state_id,
                'city_name' =>  $request->city_name,
                'created_by' => $request->created_by

            ]);

            if ($city) {
                return response()->json([
                    'status' => 200,
                    'message' => 'city succesfully created'
                ], 200);
            } else {
                return response()->json([
                    'status' => 500,
                    'message' => 'city not succesfully create'
                ], 500);
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $city = city::get()->where('state_id',$id);

        if ($city->count() > 0) {
            return response()->json([
                'status' => 200,
                'city' => $city
            ]);
        } else {
            return response()->json([
                'status' => 404,
                 'city' => $city 
            ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $city = city::find($id);
        if ($city) {
            return response()->json([
                'status' => 200,
                'city' => $city
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'message' => "No Such city Found!"
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'state_id' => 'required|numeric',
            'city_name' => 'required|alpha',
            'created_by',
            'updated_by'=> 'required',
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
            $city = city::find($id);



            if ($city) {

                $city->update([
                    'state_id' =>  $request->state_id,
                    'city_name' =>  $request->city_name,
                    'updated_by' => $request->updated_by,
                    'updated_at' => date('Y-m-d'),
                    'is_active' =>  $request->is_active
                ]);

                return response()->json([
                    'status' => 200,
                    'message' => 'city succesfully updated'
                ], 200);
            } else {
                return response()->json([
                    'status' => 404,
                    'message' => 'No Such city Found!'
                ], 404);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $city = city::find($id);

        if ($city) {
            $city->update([ 
               'is_deleted' => 1

            ]);
            return response()->json([
                'status' => 200,
                'message' => 'city succesfully deleted'
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'No Such city Found!'
            ], 404);
        }
    }
}
