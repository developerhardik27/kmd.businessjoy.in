<?php

namespace App\Http\Controllers\v4_2_1\api;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class tblquotationformulaController extends commonController
{
    public $userId, $companyId, $masterdbname, $rp, $tbl_quotation_formulaModel;

    public function __construct(Request $request)
    {

        $this->dbname($request->company_id);
        $this->companyId = $request->company_id;
        $this->userId = $request->user_id;
        $this->masterdbname = DB::connection()->getDatabaseName();

        // **** for checking user has permission to action on all data 
        $user_rp = DB::connection('dynamic_connection')->table('user_permissions')->select('rp')->where('user_id', $this->userId)->get();
        $permissions = json_decode($user_rp, true);
        if(empty($permissions)){
            $this->customerrorresponse();
        }
        $this->rp = json_decode($permissions[0]['rp'], true);

        $this->tbl_quotation_formulaModel = $this->getmodel('tbl_quotation_formula');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        //condition for check if user has permission to view record
        // if ($this->rp['quotationmodule']['formula']['view'] != 1) {
        //     return $this->successresponse(500, 'message', 'You are Unauthorized');
        // }


        $quotationformulares = $this->tbl_quotation_formulaModel::orderBy('formula_order')
            ->where('is_deleted', 0);

        if ($this->rp['quotationmodule']['quotationformula']['alldata'] != 1) {
            $quotationformulares->where('created_by', $this->userId);
        }

        $quotationformula = $quotationformulares->get();

        if ($quotationformula->isEmpty()) {
            return $this->successresponse(404, 'quotationformula', 'No records found');
        }
        return $this->successresponse(200, 'quotationformula', $quotationformula);
    } 

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        //condition for check if user has permission to add record
        if ($this->rp['quotationmodule']['quotationformula']['add'] != 1) {
            return $this->successresponse(500, 'message', 'You are unauthorized');
        }

        $maxformulasequence = $this->tbl_quotation_formulaModel::where('is_deleted', 0)->max('formula_order');
        $formulasequence = 1;
        if ($maxformulasequence) {
            $formulasequence = ++$formulasequence;
        }
        $formuladata = $request['formuladata'];
        foreach ($formuladata as $key => $value) {
            $quotationformula = $this->tbl_quotation_formulaModel::create([
                'first_column' => $value[0],
                'operation' => $value[1],
                'second_column' => $value[2],
                'output_column' => $value[3],
                'formula_order' => $formulasequence,
                'company_id' => $this->companyId,
                'created_by' => $this->userId,
            ]);
            ++$formulasequence;
        }

        if ($quotationformula) {
            DB::connection('dynamic_connection')->table('quotations')->update([
                'is_editable' => 0
            ]);
            return $this->successresponse(200, 'message', 'Quotation formula succesfully added');
        } else {
            return $this->successresponse(500, 'message', 'Quotation formula not succesfully added');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //condition for check if user has permission to search  record
        if ($this->rp['quotationmodule']['quotationformula']['edit'] != 1) {
            return $this->successresponse(500, 'message', 'You are unauthorized');
        }

        $quotationformula = $this->tbl_quotation_formulaModel::get();

        if ($quotationformula->isEmpty()) {
            return $this->successresponse(404, 'message', "No such quotation formula found!");
        }
        if ($this->rp['quotationmodule']['quotationformula']['alldata'] != 1) {
            if ($quotationformula->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are unauthorized');
            }
        }

        return $this->successresponse(200, 'quotationformula', $quotationformula);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {

        //condition for check if user has permission to search  record
        if ($this->rp['quotationmodule']['quotationformula']['edit'] != 1) {
            return $this->successresponse(500, 'message', 'You are unauthorized');
        }

        $quotationformula = $this->tbl_quotation_formulaModel::find($id);


        if (!$quotationformula) {
            return $this->successresponse(404, 'message', "No such quotation formula found!");
        }
        if ($this->rp['quotationmodule']['quotationformula']['alldata'] != 1) {
            if ($quotationformula->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are unauthorized');
            }
        }
        return $this->successresponse(200, 'quotationformula', $quotationformula);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //condition for check if user has permission to search  record
        if ($this->rp['quotationmodule']['quotationformula']['edit'] != 1) {
            return $this->successresponse(500, 'message', 'You are unauthorized');
        }
        
        $validator = Validator::make($request->all(), [
            'first_column' => 'required|string|max:50',
            'operation' => 'required|string|max:50',
            'second_column' => 'required|string',
            'output_column' => 'required|string',
            'updated_by' => 'required|numeric',
            'created_by',
            'created_at',
            'updated_at',
            'is_active',
            'is_deleted'
        ]);

        if ($validator->fails()) {
            return $this->errorresponse(422, $validator->messages());
        } else {

            $quotationformula = $this->tbl_quotation_formulaModel::find($id);

            if (!$quotationformula) {
                return $this->successresponse(404, 'message', 'No such quotation formula found!');
            }
            if ($this->rp['quotationmodule']['quotationformula']['alldata'] != 1) {
                if ($quotationformula->created_by != $this->userId) {
                    return $this->successresponse(500, 'message', 'You are unauthorized');
                }
            }
            date_default_timezone_set('Asia/Kolkata');
            $quotationformula->update([
                'first_column' => $request->first_column,
                'operation' => $request->operation,
                'second_column' => $request->second_column,
                'output_column' => $request->output_column,
                'updated_by' => $request->updated_by,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            DB::connection('dynamic_connection')->table('quotations')->update([
                'is_editable' => 0
            ]);
            return $this->successresponse(200, 'message', 'Quotation formula succesfully updated');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        if ($this->rp['quotationmodule']['quotationformula']['delete'] != 1) {
            return $this->successresponse(500, 'message', 'You are unauthorized');
        }

        $quotationformula = $this->tbl_quotation_formulaModel::find($id);

        if (!$quotationformula) { 
            return $this->successresponse(404, 'message', 'No such quotation formula found!');
        }
        if ($this->rp['quotationmodule']['quotationformula']['alldata'] != 1) {
            if ($quotationformula->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are unauthorized');
            }
        }

        $quotationformula->update([
            'is_deleted' => 1
        ]);
        DB::connection('dynamic_connection')->table('quotations')->update([
            'is_editable' => 0
        ]);
        return $this->successresponse(200, 'message', 'Quotation formula succesfully deleted');
    }

    /**
     * set column order.
     */
    public function formulaorder(Request $request)
    {
        if ($this->rp['quotationmodule']['quotationformula']['edit'] != 1) {
            return $this->successresponse(500, 'message', 'You are unauthorized');
        }

        $successCount = 0;
        $errorCount = 0;

        foreach ($request->formulaorders as $key => $formulaorder) {
            if ($formulaorder !== null) {
                $updateResult = $this->tbl_quotation_formulaModel::where('id', $key)
                    ->update(['formula_order' => $formulaorder]);

                if ($updateResult) {
                    $successCount++;
                } else {
                    $errorCount++;
                }
            }
        }

        if ($successCount > 0) {
            return $this->successresponse(200, 'message', 'Formula order succesfully updated');
        } else {
            return $this->successresponse(404, 'message', 'Formula order not succesfully upadated');
        }
    }
}

