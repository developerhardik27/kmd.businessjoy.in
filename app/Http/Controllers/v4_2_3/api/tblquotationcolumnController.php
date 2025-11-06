<?php

namespace App\Http\Controllers\v4_2_3\api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

class tblquotationcolumnController extends commonController
{
    public $userId, $companyId, $masterdbname, $rp, $tbl_quotation_columnModel;

    public function __construct(Request $request)
    {
        $this->companyId = $request->company_id;
        $this->userId = $request->user_id;
        
        $this->dbname($this->companyId);
        // **** for checking user has permission to action on all data 
        $user_rp = DB::connection('dynamic_connection')->table('user_permissions')->select('rp')->where('user_id', $this->userId)->value('rp');

        if (empty($user_rp)) {
            $this->customerrorresponse();
        }

        $this->rp = json_decode($user_rp, true);

        $this->masterdbname = DB::connection()->getDatabaseName();

        $this->tbl_quotation_columnModel = $this->getmodel('tbl_quotation_column');
    }

    //  for formula list 
    public function formula(Request $request)
    {
        //condition for check if user has permission to view record

        // if ($this->rp['quotationmodule']['mngcol']['view'] != 1) {
        //     return $this->successresponse(500, 'message', 'You are Unauthorized');
        // }

        $quotationcolumn = $this->tbl_quotation_columnModel::whereIn('column_type', ['decimal', 'percentage', 'number'])
            ->where('is_deleted', 0)
            ->get();

        if ($quotationcolumn->isEmpty()) {
            return $this->successresponse(404, 'quotationcolumn', 'No Records Found');
        }
        return $this->successresponse(200, 'quotationcolumn', $quotationcolumn);
    }
 
    public function column_details(string $id)
    {

        $columndetails = $this->tbl_quotation_columnModel::where('is_deleted', 0)
            ->get();


        if ($columndetails->isEmpty()) {
            return $this->successresponse(404, 'columndetails', 'No Records Found');
        }
        return $this->successresponse(200, 'columndetails', $columndetails);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //condition for check if user has permission to view record

        // if ($this->rp['quotationmodule']['mngcol']['view'] != 1) {
        //     return $this->successresponse(500, 'message', 'You are Unauthorized');
        // }


        $quotationcolumn = $this->tbl_quotation_columnModel::orderBy('column_order')
            ->where('is_deleted', 0)->get();

        if ($quotationcolumn->isEmpty()) {
            return $this->successresponse(404, 'quotationcolumn', 'No Records Found');
        }
        return $this->successresponse(200, 'quotationcolumn', $quotationcolumn);
    }
 
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //condition for check if user has permission to add record
        if ($this->rp['quotationmodule']['quotationmngcol']['add'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $validator = Validator::make($request->all(), [
            'column_name' => 'required|string|max:50',
            'column_type' => 'required|string|max:50',
            'column_width' => 'required|numeric',
            'company_id' => 'required|numeric',
            'user_id' => 'required|numeric',
            'updated_by',
            'created_at',
            'updated_at',
            'is_active',
            'is_deleted'
        ]);

        if ($validator->fails()) {
            return $this->errorresponse(422, $validator->messages());
        } else {

            $modifiedcolumnname = strtolower(str_replace(' ', '_', $request->column_name));
            $defaultcolumns = ['quotation_id', 'id', 'amount', 'created_by', 'updated_by', 'created_at', 'updated_at', 'is_active', 'is_deleted'];
            if (in_array($modifiedcolumnname, $defaultcolumns)) {
                return $this->successresponse(500, 'message', "'$request->column_name'" . " column is a default system field and cannot be added or modified manually, so there is no need to add it again.");
            }

            $quotationcolumn = $this->tbl_quotation_columnModel::where('column_name', $request->column_name)
                ->where('is_deleted', 0)
                ->get();
            if ($quotationcolumn->isNotEmpty()) {
                return $this->successresponse(500, 'message', $request->column_name . ' columns already exist');
            } else {

                $columnTypes = [
                    'text' => 'varchar(255)',
                    'longtext' => 'longtext',
                    'number' => 'int',
                    'decimal' => 'decimal(10,2)', // Adjust precision and scale as needed
                    'percentage' => 'float(4,2)' // Adjust precision and scale as needed
                ];

                // Validation rules
                $rules = [
                    'column_name' => 'required|string',
                    'column_type' => 'required|in:' . implode(',', array_keys($columnTypes)),
                ];

                // Validate the request
                $request->validate($rules);
                $columnType = $columnTypes[$request->column_type];
                $tablename = 'quotation_mng_col';
                $columnname = str_replace(' ', '_', $request->column_name);
                $maxColumnOrder = $this->tbl_quotation_columnModel::where('is_deleted', 0)->max('column_order');
                $columnsequence = 1;
                if ($maxColumnOrder) {
                    $columnsequence = ++$maxColumnOrder;
                }

                if (!Schema::connection('dynamic_connection')->hasColumn($tablename, $columnname)) {
                    $addcolumn = DB::connection('dynamic_connection')->statement("ALTER TABLE $tablename ADD COLUMN  $columnname  $columnType");
                }

                $quotationcolumn = $this->tbl_quotation_columnModel::create([
                    'column_name' => $request->column_name,
                    'column_type' => $request->column_type,
                    'column_width' => $request->column_width,
                    'company_id' => $request->company_id,
                    'column_order' => $columnsequence,
                    'created_by' => $this->userId,
                ]);

                if ($quotationcolumn) {
                    DB::connection('dynamic_connection')->table('quotations')->update([
                        'is_editable' => 0
                    ]);
                    return $this->successresponse(200, 'message', 'Quotation columns  succesfully added');
                } else {
                    return $this->successresponse(500, 'message', 'Quotation columns not succesfully added');
                }
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {

        //condition for check if user has permission to search  record
        if ($this->rp['quotationmodule']['quotationmngcol']['edit'] != 1) {
            return $this->successresponse(500, 'message', 'You are unauthorized');
        }

        $quotationcolumn = $this->tbl_quotation_columnModel::get();

        if ($quotationcolumn->isEmpty()) {
            return $this->successresponse(404, 'message', "No such quotation column found!");
        }
        if ($this->rp['quotationmodule']['quotationmngcol']['alldata'] != 1) {
            if ($quotationcolumn->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are unauthorized');
            }
        }

        return $this->successresponse(200, 'quotationcolumn', $quotationcolumn);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {

        //condition for check if user has permission to search record
        if ($this->rp['quotationmodule']['quotationmngcol']['edit'] != 1) {
            return $this->successresponse(500, 'message', 'You are unauthorized');
        }

        $quotationcolumn = $this->tbl_quotation_columnModel::find($id);

        if (!$quotationcolumn) {
            return $this->successresponse(404, 'message', "No such quotation column found!");
        }
        if ($this->rp['quotationmodule']['quotationmngcol']['alldata'] != 1) {
            if ($quotationcolumn->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are unauthorized');
            }
        }

        return $this->successresponse(200, 'quotationcolumn', $quotationcolumn);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        
        //condition for check if user has permission to search record
        if ($this->rp['quotationmodule']['quotationmngcol']['edit'] != 1) {
            return $this->successresponse(500, 'message', 'You are unauthorized');
        }
        $validator = Validator::make($request->all(), [
            'column_name' => 'required|string|max:50',
            'column_type' => 'required|string|max:50',
            'column_width' => 'required|numeric',
            'user_id' => 'required|numeric',
            'created_by',
            'created_at',
            'updated_at',
            'is_active',
            'is_deleted'
        ]);

        if ($validator->fails()) {
            return $this->errorresponse(422, $validator->messages());
        } else {

            $modifiedcolumnname = strtolower(str_replace(' ', '_', $request->column_name));
            $defaultcolumns = ['quotation_id', 'id', 'amount', 'created_by', 'updated_by', 'created_at', 'updated_at', 'is_active', 'is_deleted'];
            if (in_array($modifiedcolumnname, $defaultcolumns)) {
                return $this->successresponse(500, 'message', "'$request->column_name'" . " column is a default system field and cannot be added or modified manually, so there is no need to add it again.");
            }


            $quotationcolumn = $this->tbl_quotation_columnModel::find($id);

            if (!$quotationcolumn) {
                return $this->successresponse(404, 'message', 'No such quotation column found!');
            }
            if ($this->rp['quotationmodule']['quotationmngcol']['alldata'] != 1) {
                if ($quotationcolumn->created_by != $this->userId) {
                    return $this->successresponse(500, 'message', 'You are unauthorized');
                }
            }

            date_default_timezone_set('Asia/Kolkata');

            $columnTypes = [
                'text' => 'varchar(255)',
                'longtext' => 'longtext',
                'number' => 'int',
                'decimal' => 'decimal(10,2)', // Adjust precision and scale as needed
                'percentage' => 'float(4,2)' // Adjust precision and scale as needed
            ];
            $columnType = $columnTypes[$request->column_type];
            $oldcolumnvaluewithoutchange = $quotationcolumn->column_name; // for formula table change 
            $oldcolumnname = str_replace(' ', '_', $quotationcolumn->column_name);
            $columnname = str_replace(' ', '_', $request->column_name);

            // change column name in mng_col table who storing quotations data
            DB::connection('dynamic_connection')->statement("ALTER TABLE quotation_mng_col CHANGE  $oldcolumnname $columnname $columnType");

            // replace old column name with new columnname in quotation table > show_col column
            DB::connection('dynamic_connection')
                ->table('quotations')
                ->whereRaw("show_col LIKE '%$oldcolumnname%'")
                ->update(['show_col' => DB::raw("REPLACE(show_col, '$oldcolumnname', '$columnname')")]);


            // change column name in formula table
            DB::connection('dynamic_connection')
                ->table('tbl_quotation_formulas')
                ->where(function ($query) use ($oldcolumnvaluewithoutchange) {
                    $query->where('first_column', 'LIKE', "%$oldcolumnvaluewithoutchange%")
                        ->orWhere('second_column', 'LIKE', "%$oldcolumnvaluewithoutchange%")
                        ->orWhere('output_column', 'LIKE', "%$oldcolumnvaluewithoutchange%");
                })
                ->update([
                    'first_column' => DB::raw("REPLACE(first_column, '$oldcolumnvaluewithoutchange', '$request->column_name')"),
                    'second_column' => DB::raw("REPLACE(second_column, '$oldcolumnvaluewithoutchange', '$request->column_name')"),
                    'output_column' => DB::raw("REPLACE(output_column, '$oldcolumnvaluewithoutchange', '$request->column_name')")
                ]);


            // update column name in tbl quotation column 
            $quotationcolumn->update([
                'column_name' => $request->column_name,
                'column_type' => $request->column_type,
                'column_width' => $request->column_width,
                'updated_by' => $this->userId,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            return $this->successresponse(200, 'message', 'Quotation column succesfully updated');


        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {

        //condition for check if user has permission to search record
        if ($this->rp['quotationmodule']['quotationmngcol']['delete'] != 1) {
            return $this->successresponse(500, 'message', 'You are unauthorized');
        }

        // fetch column data from tbl quotation column table
        $quotationcolumn = $this->tbl_quotation_columnModel::find($id);

        // if column data not found
        if (!$quotationcolumn) {
            return $this->successresponse(404, 'message', 'No such quotation column found!');
        }
        if ($this->rp['quotationmodule']['quotationmngcol']['alldata'] != 1) {
            if ($quotationcolumn->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are unauthorized');
            }
        }
        $tablename = 'quotation_mng_col';
        $columname = $quotationcolumn->column_name;
        $modifiedcolumname = str_replace(' ', '_', $quotationcolumn->column_name);

        // check if column is using or not into formula if it is using then user will not able to delete it
        $checkrec = DB::connection('dynamic_connection')
            ->table('tbl_quotation_formulas')
            ->where(function ($query) use ($columname) {
                $query->where('first_column',$columname)
                    ->orWhere('second_column',$columname)
                    ->orWhere('output_column',$columname);
            })->where('is_deleted', 0)
            ->get();
        if ($checkrec->count() > 0) {
            return $this->successresponse(404, 'message', 'This column is using in formula so please first remove it from formula!');
        }

        // drop column from mng col table it is it exist
        if (Schema::connection('dynamic_connection')->hasColumn($tablename, $modifiedcolumname)) {
            DB::connection('dynamic_connection')->statement("ALTER TABLE $tablename DROP COLUMN $modifiedcolumname");
        }

        // remove column name from quotation table  > show_col table
        DB::connection('dynamic_connection')
            ->table('quotations')
            ->whereRaw("show_col LIKE '%$modifiedcolumname,%'") // Match when the old column is not the last column
            ->orWhereRaw("show_col LIKE '$modifiedcolumname,%'") // Match when the old column is the last column
            ->orWhereRaw("show_col LIKE '%$modifiedcolumname'") // Match when the old column is the first or only column
            ->update([
                'show_col' => DB::raw("TRIM(BOTH ',' FROM REPLACE(CONCAT(',', show_col, ','), ',$modifiedcolumname,', ','))")
            ]);

        // change column status is deleted = 1 that means column is deleted
        $quotationcolumn->update([
            'is_deleted' => 1
        ]);

        return $this->successresponse(200, 'message', 'Quotation Column succesfully deleted');
    }



    /**
     * Hide the specified Record from quotation form.
     */
    public function hide(Request $request, string $id)
    {

        if ($this->rp['quotationmodule']['quotationmngcol']['edit'] != 1) {
            return $this->successresponse(500, 'message', 'You are unauthorized');
        }

        $quotationcolumn = $this->tbl_quotation_columnModel::find($id);
        
        if (!$quotationcolumn) { 
            return $this->successresponse(404, 'message', 'No Such quotation Column Found!');
        }

        if ($this->rp['quotationmodule']['quotationmngcol']['alldata'] != 1) {
            if ($quotationcolumn->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are unauthorized');
            }
        }

        $quotationcolumn->update([
            'is_hide' => $request->hidevalue
        ]);

        DB::connection('dynamic_connection')->table('quotations')->update([
            'is_editable' => 0
        ]);

        return $this->successresponse(200, 'message', 'Quotation column succesfully updated');
    }

    /**
     * set column order.
     */
    public function columnorder(Request $request)
    {
        if ($this->rp['quotationmodule']['quotationmngcol']['edit'] != 1) {
            return $this->successresponse(500, 'message', 'You are unauthorized');
        }

        $successCount = 0;
        $errorCount = 0;

        foreach ($request->columnorders as $key => $columnOrder) {
            if ($columnOrder !== null) {
                $updateResult = $this->tbl_quotation_columnModel::where('id', $key)
                    ->update(['column_order' => $columnOrder]);

                if ($updateResult) {
                    $successCount++;
                } else {
                    $errorCount++;
                }
            }
        }

        if ($successCount > 0) {
            return $this->successresponse(200, 'message', 'Column order succesfully updated');
        } else {
            return $this->successresponse(404, 'message', 'Column order not succesfully upadated');
        }
    }
}

