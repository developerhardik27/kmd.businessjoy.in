<?php

namespace App\Http\Controllers\v4_3_2\api;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class bankdetailsController extends commonController
{
    public $userId, $companyId, $masterdbname, $rp, $bankdetailmodel;

    public function __construct(Request $request)
    {
        $this->companyId = $request->company_id;
        $this->userId = $request->user_id;

        $this->dbname($this->companyId);
        // **** for checking user has permission to action on all data 
        $user_rp = DB::connection('dynamic_connection')->table('user_permissions')->where('user_id', $this->userId)->value('rp');

        if (empty($user_rp)) {
            $this->customerrorresponse();
        }

        $this->rp = json_decode($user_rp, true);

        $this->masterdbname = DB::connection()->getDatabaseName();

        $this->rp = json_decode($user_rp, true);
        $this->bankdetailmodel = $this->getmodel('bank_detail');
    }

    /**
     * Summary of bankdetailspdf
     * use for pdf 
     * @param string $id
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function bankdetailspdf(string $id)
    {
        $bankdetailres = $this->bankdetailmodel::where('id', $id);

        $bankdetail = $bankdetailres->get();

        if ($bankdetail->isEmpty()) {
            return $this->successresponse(404, 'bankdetail', 'No Records Found');
        }
        
        if ($this->rp['invoicemodule']['bank']['view'] != 1 && $this->rp['reportmodule']['report']['view'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        return $this->successresponse(200, 'bankdetail', $bankdetail);
    }

    /**
     * Summary of bank_details
     * use for pdf
     * @param string $id
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function bank_details(string $id)
    {
        if ($this->rp['invoicemodule']['bank']['view'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $bankdetail = $this->bankdetailmodel::where('is_deleted', 0)
            ->get();

        if ($bankdetail->isEmpty()) {
            return $this->successresponse(404, 'bankdetail', 'No Records Found');
        }
        return $this->successresponse(200, 'bankdetail', $bankdetail);
    }

    /**
     * Summary of index
     * return bank account list
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        if ($this->rp['invoicemodule']['bank']['view'] != 1) {
            return response()->json([
                'status' => 500,
                'message' => 'You are Unauthorized',
                'data' => [],
                'recordsTotal' => 0,
                'recordsFiltered' => 0
            ]);
        }

        $bankdetailres = $this->bankdetailmodel::where('is_deleted', 0)
            ->select(
                'bank_details.*',
                DB::raw('DATE_FORMAT(created_at,"%d-%M-%Y %h:%i %p") as created_at_formatted')
            );

        if ($this->rp['invoicemodule']['bank']['alldata'] != 1) {
            $bankdetailres->where('created_by', $this->userId);
        }

        $totalcount = $bankdetailres->get()->count(); // count total record

        $bankdetail = $bankdetailres->get();

        if ($bankdetail->isEmpty()) {
            return DataTables::of($bankdetail)
                ->with([
                    'status' => 404,
                    'message' => 'No Data Found',
                    'recordsTotal' => $totalcount, // Total records count
                ])
                ->make(true);
        }

        return DataTables::of($bankdetail)
            ->with([
                'status' => 200,
                'recordsTotal' => $totalcount, // Total records count
            ])
            ->make(true);
    }

    /**
     * Summary of store
     * store new bankdetails
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\JsonResponse
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

            if ($this->rp['invoicemodule']['bank']['add'] != 1) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
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
        }
    }

    /**
     * Summary of update
     * update bank details status
     * @param \Illuminate\Http\Request $request
     * @param string $id
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function update(Request $request, string $id)
    {

        if ($this->rp['invoicemodule']['bank']['edit'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $bankdetail = $this->bankdetailmodel::find($id);


        if (!$bankdetail) {
            return $this->successresponse(404, 'message', 'No Such bank Found!');
        }

        if ($this->rp['invoicemodule']['bank']['alldata'] != 1) {
            if ($bankdetail->created_by != $this->userId) {
                return $this->successresponse(500, 'message', "You are Unauthorized!");
            }
        }

        $bankdetail->update([
            'is_active' => $request->status
        ]);

        return $this->successresponse(200, 'message', 'status succesfully updated');
    }

    /**
     * Summary of destroy
     * delete bank details record
     * @param \Illuminate\Http\Request $request
     * @param string $id
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, string $id)
    {

        if ($this->rp['invoicemodule']['bank']['delete'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $bankdetail = $this->bankdetailmodel::find($id);

        if (!$bankdetail) {
            return $this->successresponse(404, 'message', 'No Such bank Found!');
        }

        if ($this->rp['invoicemodule']['bank']['alldata'] != 1) {
            if ($bankdetail->created_by != $this->userId) {
                return $this->successresponse(500, 'message', "You are Unauthorized!");
            }
        }

        $bankdetail->update([
            'is_deleted' => 1
        ]);

        return $this->successresponse(200, 'message', 'bankdetail succesfully deleted');
    }
}
