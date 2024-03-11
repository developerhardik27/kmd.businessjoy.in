<?php

namespace App\Http\Controllers\api;


use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class purchaseController extends commonController
{

    public $userId, $companyId, $masterdbname, $rp;

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
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {


        $purchasesres = Purchase::join($this->masterdbname . '.company', 'purchases.company_id', '=', $this->masterdbname . '.company.id')
            ->join($this->masterdbname . '.company_details', $this->masterdbname . '.company.company_details_id', '=', $this->masterdbname . '.company_details.id')
            ->select('purchases.id', 'purchases.name', 'purchases.description', 'purchases.amount', 'purchases.amount_type', 'purchases.date', 'company_details.name as company_name', 'purchases.img', 'purchases.created_by', 'purchases.updated_by', 'purchases.is_active')
            ->where('purchases.is_deleted', 0);

        if ($this->rp['invoicemodule']['purchase']['alldata'] != 1) {
            $purchasesres->where('purchases.created_by', $this->userId);
        }

        //condition for check if user has permission to view record
        if ($this->rp['invoicemodule']['purchase']['view'] != 1) {
            return response()->json([
                'status' => 500,
                'message' => 'You are Unauthorized'
            ]);
        }

        $purchases = $purchasesres->get();

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
            'user_id' => 'required|numeric',
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

            //condition for check if user has permission to add new record
            if ($this->rp['invoicemodule']['purchase']['add'] != 1) {
                return response()->json([
                    'status' => 500,
                    'message' => 'You are Unauthorized'
                ]);
            }

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
                        'company_id' => $this->companyId,
                        'created_by' => $this->userId,
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
                    'company_id' => $this->companyId,
                    'created_by' => $this->userId,
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
        if ($this->rp['invoicemodule']['purchase']['alldata'] != 1) {
            if ($purchases->created_by != $this->userId) {
                return response()->json([
                    'status' => 500,
                    'message' => "You are Unauthorized!"
                ]);
            }
        }
        //condition for check if user has permission to search record
        if ($this->rp['invoicemodule']['purchase']['view'] != 1) {
            return response()->json([
                'status' => 500,
                'message' => "You are Unauthorized!"
            ]);
        }

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
        if ($this->rp['invoicemodule']['purchase']['alldata'] != 1) {
            if ($purchases->created_by != $this->userId) {
                return response()->json([
                    'status' => 500,
                    'message' => "You are Unauthorized!"
                ]);
            }
        }

        //condition for check if user has permission to edit record
        if ($this->rp['invoicemodule']['purchase']['edit'] != 1) {
            return response()->json([
                'status' => 500,
                'message' => "You are Unauthorized!"
            ]);
        }
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
            'user_id' => 'required|numeric',
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
            //condition for check if user has permission to edit record
            if ($this->rp['invoicemodule']['purchase']['edit'] != 1) {
                return response()->json([
                    'status' => 500,
                    'message' => "You are Unauthorized!"
                ]);
            }

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
                            'updated_by' => $this->userId,
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
                        'updated_by' => $this->userId,
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
        if ($this->rp['invoicemodule']['purchase']['alldata'] != 1) {
            if ($purchases->created_by != $this->userId) {
                return response()->json([
                    'status' => 500,
                    'message' => "You are Unauthorized!"
                ]);
            }
        }
        //condition for check if user has permission to delete record
        if ($this->rp['invoicemodule']['purchase']['delete'] != 1) {
            return response()->json([
                'status' => 500,
                'message' => "You are Unauthorized!"
            ]);
        }

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
