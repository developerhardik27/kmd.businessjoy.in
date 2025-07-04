<?php

namespace App\Http\Controllers\v4_2_1\api;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class tblinvoiceformulaController extends commonController
{

    public $userId, $companyId, $masterdbname, $rp, $tbl_invoice_formulaModel;

    public function __construct(Request $request)
    {

        $this->companyId = $request->company_id ?? session('company_id');
        $this->userId = $request->user_id ?? session('user_id');
        
        $this->dbname($this->companyId);
        // **** for checking user has permission to action on all data 
        $user_rp = DB::connection('dynamic_connection')->table('user_permissions')->select('rp')->where('user_id', $this->userId)->value('rp');

        if (empty($user_rp)) {
            $this->customerrorresponse();
        }

        $this->rp = json_decode($user_rp, true);

        $this->masterdbname = DB::connection()->getDatabaseName();

        $this->tbl_invoice_formulaModel = $this->getmodel('tbl_invoice_formula');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        //condition for check if user has permission to view record
        // if ($this->rp['invoicemodule']['formula']['view'] != 1) {
        //     return $this->successresponse(500, 'message', 'You are Unauthorized');
        // } 

        $invoiceformulares = $this->tbl_invoice_formulaModel::orderBy('formula_order')
            ->where('is_deleted', 0);

        if ($this->rp['invoicemodule']['formula']['alldata'] != 1) {
            $invoiceformulares->where('created_by', $this->userId);
        }

        $invoiceformula = $invoiceformulares->get();

        if ($invoiceformula->isEmpty()) {
            return $this->successresponse(404, 'invoiceformula', 'No Records Found');
        }
        return $this->successresponse(200, 'invoiceformula', $invoiceformula);
    } 

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        //condition for check if user has permission to add record
        if ($this->rp['invoicemodule']['formula']['add'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $maxformulasequence = $this->tbl_invoice_formulaModel::where('is_deleted', 0)->max('formula_order');
        $formulasequence = 1;
        if ($maxformulasequence) {
            $formulasequence = ++$formulasequence;
        }
        $formuladata = $request['formuladata'];
        foreach ($formuladata as $key => $value) {
            $invoiceformula = $this->tbl_invoice_formulaModel::create([
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

        if ($invoiceformula) {
            DB::connection('dynamic_connection')->table('invoices')->update([
                'is_editable' => 0
            ]);
            return $this->successresponse(200, 'message', 'Invoice Formula succesfully added');
        } else {
            return $this->successresponse(500, 'message', 'Invoice Formula not succesfully added');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //condition for check if user has permission to search  record
        if ($this->rp['invoicemodule']['formula']['edit'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $invoiceformula = $this->tbl_invoice_formulaModel::get();

        if ($invoiceformula->isEmpty()) {
            return $this->successresponse(404, 'message', "No Such Invoice Formula Found!");
        }
        if ($this->rp['invoicemodule']['formula']['alldata'] != 1) {
            if ($invoiceformula->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }

        return $this->successresponse(200, 'invoiceformula', $invoiceformula);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {

        //condition for check if user has permission to search  record
        if ($this->rp['invoicemodule']['formula']['edit'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $invoiceformula = $this->tbl_invoice_formulaModel::find($id);

        if (!$invoiceformula) {
            return $this->successresponse(404, 'message', "No Such Invoice Formula Found!");
        }
        if ($this->rp['invoicemodule']['formula']['alldata'] != 1) {
            if ($invoiceformula->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }

        return $this->successresponse(200, 'invoiceformula', $invoiceformula);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        
        //condition for check if user has permission to search  record
        if ($this->rp['invoicemodule']['formula']['edit'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
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

            $invoiceformula = $this->tbl_invoice_formulaModel::find($id);

            if (!$invoiceformula) {
                return $this->successresponse(404, 'message', 'No Such Invoice Formula Found!');
            }
            if ($this->rp['invoicemodule']['formula']['alldata'] != 1) {
                if ($invoiceformula->created_by != $this->userId) {
                    return $this->successresponse(500, 'message', 'You are Unauthorized');
                }
            }

            date_default_timezone_set('Asia/Kolkata');
            $invoiceformula->update([
                'first_column' => $request->first_column,
                'operation' => $request->operation,
                'second_column' => $request->second_column,
                'output_column' => $request->output_column,
                'updated_by' => $request->updated_by,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            DB::connection('dynamic_connection')->table('invoices')->update([
                'is_editable' => 0
            ]);
            return $this->successresponse(200, 'message', 'Invoice Formula succesfully updated');

        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        if ($this->rp['invoicemodule']['formula']['delete'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $invoiceformula = $this->tbl_invoice_formulaModel::find($id);

        if (!$invoiceformula) { 
            return $this->successresponse(404, 'message', 'No Such Invoice Formula Found!');
        }
        if ($this->rp['invoicemodule']['formula']['alldata'] != 1) {
            if ($invoiceformula->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }

        $invoiceformula->update([
            'is_deleted' => 1
        ]);
        DB::connection('dynamic_connection')->table('invoices')->update([
            'is_editable' => 0
        ]);
        return $this->successresponse(200, 'message', 'Invoice Formula succesfully deleted');
    }

    /**
     * set column order.
     */
    public function formulaorder(Request $request)
    {
        if ($this->rp['invoicemodule']['formula']['edit'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $successCount = 0;
        $errorCount = 0;

        foreach ($request->formulaorders as $key => $formulaorder) {
            if ($formulaorder !== null) {
                $updateResult = $this->tbl_invoice_formulaModel::where('id', $key)
                    ->update(['formula_order' => $formulaorder]);

                if ($updateResult) {
                    $successCount++;
                } else {
                    $errorCount++;
                }
            }
        }

        if ($successCount > 0) {
            return $this->successresponse(200, 'message', 'Formula Order Succesfully updated');
        } else {
            return $this->successresponse(404, 'message', 'Formula Order Not Succesfully upadated');
        }
    }
}
