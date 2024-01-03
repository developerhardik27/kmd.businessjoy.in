<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class countryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {   
        $country = country::all()->where('is_deleted', 0)->where('is_active', 1);

        if ($country->count() > 0) {
            return response()->json([
                'status' => 200,
                'country' => $country
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'country' => $country
            ], 404);
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
            'country_name' => 'required|alpha',
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

            $country = country::create([
                'country_id' =>  $request->country_id,
                'country_name' =>  $request->country_name,
                'created_by' => $request->created_by

            ]);

            if ($country) {
                return response()->json([
                    'status' => 200,
                    'message' => 'country succesfully created'
                ], 200);
            } else {
                return response()->json([
                    'status' => 500,
                    'message' => 'country not succesfully create'
                ], 500);
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $country = country::find($id);
        if ($country) {
            return response()->json([
                'status' => 200,
                'country' => $country
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'country' => $country,
                'message' => "No Such country Found!"
            ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $country = country::find($id);
        if ($country) {
            return response()->json([
                'status' => 200,
                'country' => $country
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'message' => "No Such country Found!"
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'country_name' => 'required|alpha',
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

            $country = country::find($id);
            if ($country) {

                $country->update([
                    'country_name' =>  $request->country_name,
                    'updated_by' => $request->updated_by,
                    'updated_at' => date('Y-m-d'),
                    'is_active' =>  $request->is_active
                ]);

                return response()->json([
                    'status' => 200,
                    'message' => 'country succesfully updated'
                ], 200);
            } else {
                return response()->json([
                    'status' => 404,
                    'message' => 'No Such country Found!'
                ]);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $country = country::find($id);

        if ($country) {
            $country->update([
                'is_deleted' => 1

            ]);
            return response()->json([
                'status' => 200,
                'message' => 'country succesfully deleted'
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'No Such country Found!'
            ], 404);
        }
    }
}
