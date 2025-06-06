<?php

namespace App\Http\Controllers\v2_0_0\api;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class bankdetailsController extends commonController
{
    public $userId, $companyId, $masterdbname, $rp, $bankdetailmodel;

    public function __construct(Request $request)
    {
        if ($request->company_id) {
            $this->dbname($request->company_id);
            $this->companyId = $request->company_id;
        } else {
            $this->dbname(session()->get('company_id'));
        }

        if ($request->user_id) {
            $this->userId = $request->user_id;
        } else {
            $this->userId = session()->get('user_id');
        }

        $this->masterdbname = DB::connection()->getDatabaseName();

        // **** for checking user has permission to action on all data 
        $user_rp = DB::connection('dynamic_connection')->table('user_permissions')->select('rp')->where('user_id', $this->userId)->get();
        $permissions = json_decode($user_rp, true);
        $this->rp = json_decode($permissions[0]['rp'], true);
        $this->bankdetailmodel = $this->getmodel('bank_detail');
    }

    // using for pdf
    public function bankdetailspdf(string $id)
    {
        $bankdetailres = DB::connection('dynamic_connection')->table('bank_details')->where('id', $id);


        $bankdetail = $bankdetailres->get();

        if ($bankdetail->count() > 0) {
            if ($this->rp['invoicemodule']['bank']['view'] == 1 || $this->rp['reportmodule']['report']['view'] == 1) {
                return $this->successresponse(200, 'bankdetail', $bankdetail);
            } else {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        } else {
            return $this->successresponse(404, 'bankdetail', 'No Records Found');
        }
    }

    public function bank_details(string $id)
    {
        if ($this->rp['invoicemodule']['bank']['view'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }
        $bankdetail = DB::connection('dynamic_connection')->table('bank_details')
            ->where('is_deleted', 0)
            ->get();

        if ($bankdetail->count() > 0) {
            return $this->successresponse(200, 'bankdetail', $bankdetail);
        } else {
            return $this->successresponse(404, 'bankdetail', 'No Records Found');
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $bankdetailres = DB::connection('dynamic_connection')->table('bank_details')->where('is_deleted', 0)
            ->select('bank_details.*', DB::raw('DATE_FORMAT(created_at,"%d-%M-%Y %h:%i %p") as created_at_formatted'));

        if ($this->rp['invoicemodule']['bank']['alldata'] != 1) {
            $bankdetailres->where('created_by', $this->userId);
        }
        $bankdetail = $bankdetailres->get();

        if ($bankdetail->count() > 0) {
            if ($this->rp['invoicemodule']['bank']['view'] == 1) {
                return $this->successresponse(200, 'bankdetail', $bankdetail);
            } else {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        } else {
            return $this->successresponse(404, 'bankdetail', 'No Records Found');
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
            'branch_name' => 'nullable|string|max:50',
            'bank_name' => 'required|string|max:50',
            'account_number' => 'required|numeric',
            'swift_code' => 'nullable|string|max:50',
            'ifsc_code' => 'required|string|min:6',
            'company_id' => 'required|numeric',
            'updated_by',
            'created_at',
            'updated_at',
            'is_active',
            'is_deleted'
        ]);

        if ($validator->fails()) {
            return $this->errorresponse(422, $validator->messages());
        } else {

            if ($this->rp['invoicemodule']['bank']['add'] == 1) {

                $bankdetail = $this->bankdetailmodel::create([
                    'holder_name' => $request->holder_name,
                    'bank_name' => $request->bank_name,
                    'branch_name' => $request->branch_name,
                    'account_no' => $request->account_number,
                    'swift_code' => $request->swift_code,
                    'ifsc_code' => $request->ifsc_code, 
                    'created_by' => $this->userId,
                ]);

                if ($bankdetail) {
                    return $this->successresponse(200, 'message', 'Bank Details  succesfully added');
                } else {
                    return $this->successresponse(500, 'message', 'Bank Details not succesfully added');
                }
            } else {
                return $this->successresponse(500, 'message', 'You are Unauthorized');

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

        $bankdetail = $this->bankdetailmodel::find($id);

        if ($this->rp['invoicemodule']['bank']['alldata'] != 1) {
            if ($bankdetail->created_by != $this->userId) {
                return $this->successresponse(500, 'message', "You are Unauthorized!");
            }
        }
        if ($bankdetail) {
            if ($this->rp['invoicemodule']['bank']['edit'] != 1) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }

            $bankdetail->update([
                'is_active' => $request->status
            ]);

            return $this->successresponse(200, 'message', 'status succesfully updated');

        } else {
            return $this->successresponse(404, 'message', 'No Such bank Found!');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {

        $bankdetail = $this->bankdetailmodel::find($id);

        if ($this->rp['invoicemodule']['bank']['alldata'] != 1) {
            if ($bankdetail->created_by != $this->userId) {
                return $this->successresponse(500, 'message', "You are Unauthorized!");
            }
        }

        if ($bankdetail) {
            if ($this->rp['invoicemodule']['bank']['delete'] == 1) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
            
            $bankdetail->update([
                'is_deleted' => 1
            ]);
            return $this->successresponse(200, 'message', 'bankdetail succesfully deleted');
        } else {
            return $this->successresponse(404, 'message', 'No Such bank Found!');
        }
    }
}
