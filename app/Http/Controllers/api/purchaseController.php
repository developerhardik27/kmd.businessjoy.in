<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class purchaseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $user_id = $request->input('user_id');
        if ($user_id == 1) {
            $purchases = DB::table('purchases')
                ->join('company', 'purchases.company_id', '=', 'company.id')
                ->join('company_details', 'company.company_details_id', '=', 'company_details.id')
                ->select('purchases.id', 'purchases.name', 'purchases.description', 'purchases.amount', 'purchases.amount_type', 'purchases.date', 'company_details.name as company_name', 'purchases.img', 'purchases.created_by', 'purchases.updated_by', 'purchases.is_active')
                ->where('purchases.is_deleted', 0)->get();
        } else {
            $purchases = DB::table('purchases')
                ->join('company', 'purchases.company_id', '=', 'company.id')
                ->join('company_details', 'company.company_details_id', '=', 'company_details.id')
                ->select('purchases.id', 'purchases.name', 'purchases.description', 'purchases.amount', 'purchases.amount_type', 'purchases.date', 'company_details.name as company_name', 'purchases.img', 'purchases.created_by', 'purchases.updated_by', 'purchases.is_active')
                ->where('purchases.is_deleted', 0)->where('purchases.company_id', $user_id)->get();
        }
        if ($purchases->count() > 0) {
            return response()->json([
                'status' => 200,
                'purchase' => $purchases
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'purchase' => 'No Records Found'
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
            'description' => 'required|string',
            'amount' => 'required|numeric',
            'amount_type' => 'required|string',
            'date' => 'required|date',
            'company_id' => 'required|numeric',
            'created_by' => 'required|numeric',
            'img' => 'nullable|mimes:jpg,jpeg,png,pdf|max:2048',
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

            if ($request->hasFile('img') && $request->file('img') != '') {
                $image = $request->file('img');
                $imageName = $request->name . time() . '.' . $image->getClientOriginalExtension();
                if (!file_exists(public_path('uploads'))) {
                    mkdir(public_path('uploads'), 0755, true);
                }
                // Save the image to the uploads directory
                if ($image->move(public_path('uploads'), $imageName)) {

                    $purchases = Purchase::create([
                        'name' => $request->name,
                        'description' => $request->description,
                        'amount' => $request->amount,
                        'amount_type' => $request->amount_type,
                        'date' => $request->date,
                        'img' => $imageName,
                        'company_id' => $request->company_id,
                        'created_by' => $request->created_by,
                    ]);


                    if ($purchases) {
                        return response()->json([
                            'status' => 200,
                            'message' => 'purchases succesfully created'
                        ]);
                    } else {
                        return response()->json([
                            'status' => 500,
                            'message' => 'purchases not succesfully create'
                        ]);
                    }
                } else {
                    return response()->json([
                        'status' => 500,
                        'message' => 'image not succesfully upload'
                    ]);
                }
            } else {
                $purchases = Purchase::create([
                    'name' => $request->name,
                    'description' => $request->description,
                    'amount' => $request->amount,
                    'amount_type' => $request->amount_type,
                    'date' => $request->date,
                    'company_id' => $request->company_id,
                    'created_by' => $request->created_by,
                ]);


                if ($purchases) {
                    return response()->json([
                        'status' => 200,
                        'message' => 'purchases succesfully created'
                    ]);
                } else {
                    return response()->json([
                        'status' => 500,
                        'message' => 'purchases not succesfully create'
                    ]);
                }
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $purchases = Purchase::find($id);
        if ($purchases) {
            return response()->json([
                'status' => 200,
                'purchases' => $purchases
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => "No Such purchases Found!"
            ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $purchases = Purchase::find($id);
        if ($purchases) {
            return response()->json([
                'status' => 200,
                'purchases' => $purchases
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => "No Such purchase Found!"
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
            'description' => 'required|string',
            'amount' => 'required|numeric',
            'amount_type' => 'required|string',
            'date' => 'required|date',
            'updated_by'=> 'required|numeric',
            'img' => 'nullable|mimes:jpg,jpeg,png,pdf|max:2048',
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
            if ($request->hasFile('img') && $request->hasFile('img') != '') {
                $image = $request->file('img');
                $imageName = $request->name . time() . '.' . $image->getClientOriginalExtension();
                // Save the image to the uploads directory
                if ($image->move(public_path('uploads'), $imageName)) {
                    $purchases = Purchase::find($id);
                    if ($purchases) {
                        $imagePath = public_path('uploads/' . $purchases->img);
                        if (is_file($imagePath)) {
                            unlink($imagePath);  // old img remove
                        }

                        $purchases->update([
                            'name' => $request->name,
                            'description' => $request->description,
                            'amount' => $request->amount,
                            'amount_type' => $request->amount_type,
                            'date' => $request->date,
                            'img' => $imageName,
                            'updated_by' => $request->updated_by,
                            'updated_at' => date('Y-m-d')
                        ]);
                        return response()->json([
                            'status' => 200,
                            'message' => 'purchases succesfully updated'
                        ]);
                    } else {
                        return response()->json([
                            'status' => 404,
                            'message' => 'No Such purchases Found!'
                        ]);
                    }
                } else {
                    return response()->json([
                        'status' => 500,
                        'message' => 'image not succesfully upload'
                    ]);
                }
            } else {
                $purchases = Purchase::find($id);
                if ($purchases) {

                    $purchases->update([
                        'name' => $request->name,
                        'description' => $request->description,
                        'amount' => $request->amount,
                        'amount_type' => $request->amount_type,
                        'date' => $request->date,
                        'updated_by' => $request->updated_by,
                        'updated_at' => date('Y-m-d')
                    ]);
                    return response()->json([
                        'status' => 200,
                        'message' => 'purchases succesfully updated'
                    ]);
                } else {
                    return response()->json([
                        'status' => 404,
                        'message' => 'No Such purchases Found!'
                    ]);
                }
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $purchases = Purchase::find($id);

        if ($purchases) {
            $purchases->update([
                'is_deleted' => 1

            ]);
            return response()->json([
                'status' => 200,
                'message' => 'purchases succesfully deleted'
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'No Such purchases Found!'
            ]);
        }
    }
}
