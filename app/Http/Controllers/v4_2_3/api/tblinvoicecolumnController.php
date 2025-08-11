<?php

namespace App\Http\Controllers\v4_2_3\api;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

class tblinvoicecolumnController extends commonController
{

    public $userId, $companyId, $masterdbname, $rp, $tbl_invoice_columnModel;

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

        $this->tbl_invoice_columnModel = $this->getmodel('tbl_invoice_column');
    }

    //  for formula list

    public function formula(Request $request)
    {

        //condition for check if user has permission to view record

        // if ($this->rp['invoicemodule']['mngcol']['view'] != 1) {
        //     return $this->successresponse(500, 'message', 'You are Unauthorized');
        // }

        $invoicecolumn = $this->tbl_invoice_columnModel::whereIn('column_type', ['decimal', 'percentage', 'number'])
            ->where('is_deleted', 0)
            ->get();

        if ($invoicecolumn->isEmpty()) {
            return $this->successresponse(404, 'invoicecolumn', 'No Records Found');
        }
        return $this->successresponse(200, 'invoicecolumn', $invoicecolumn);
    }

    public function column_details(string $id)
    {

        $columndetails = $this->tbl_invoice_columnModel::where('is_deleted', 0)
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

        // if ($this->rp['invoicemodule']['mngcol']['view'] != 1) {
        //     return $this->successresponse(500, 'message', 'You are Unauthorized');
        // } 

        $invoicecolumn = $this->tbl_invoice_columnModel::orderBy('column_order')
            ->where('is_deleted', 0)->get();

        if ($invoicecolumn->isEmpty()) {
            return $this->successresponse(404, 'invoicecolumn', 'No Records Found');
        }
        return $this->successresponse(200, 'invoicecolumn', $invoicecolumn);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'column_name' => 'required|string|max:50',
            'column_type' => 'required|string|max:50',
            'column_width' => 'required|numeric',
            'default_value' => 'nullable|string|max:200',
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
            //condition for check if user has permission to add record
            if ($this->rp['invoicemodule']['mngcol']['add'] != 1) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }

            $modifiedcolumnname = strtolower(str_replace(' ', '_', $request->column_name));
            $defaultcolumns = ['invoice_id', 'id', 'amount', 'created_by', 'updated_by', 'created_at', 'updated_at', 'is_active', 'is_deleted'];
            if (in_array($modifiedcolumnname, $defaultcolumns)) {
                return $this->successresponse(500, 'message', "'$request->column_name'" . " column is a default system field and cannot be added or modified manually, so there is no need to add it again.");
            }

            $invoicecolumn = $this->tbl_invoice_columnModel::where('column_name', $request->column_name)
                ->where('is_deleted', 0)
                ->get();
            if ($invoicecolumn->isNotEmpty()) {
                return $this->successresponse(500, 'message', $request->column_name . ' Columns Already Exist');
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

                // Step 2: Manually check for invalid default on longtext
                if (
                    $request->column_type === 'longtext' &&
                    $request->has('default_value') &&
                    !empty($request->default_value)
                ) {
                    return $this->successresponse('500','message','Default value is not allowed for longtext columns.');
                }

                $columnType = $columnTypes[$request->column_type];
                $tablename = 'mng_col';
                $columnname = str_replace(' ', '_', $request->column_name);
                $maxColumnOrder = $this->tbl_invoice_columnModel::where('is_deleted', 0)->max('column_order');
                $columnsequence = 1;
                if ($maxColumnOrder) {
                    $columnsequence = ++$maxColumnOrder;
                }

                $defaultSQL = '';

                // Only set default if value is provided (not null or empty)
                if (!empty($request->default_value)) {
                    // Sanitize and quote default value if needed
                    $defaultVal = addslashes($request->default_value);
                    $defaultSQL = " DEFAULT '{$defaultVal}'";
                }

                if (!Schema::connection('dynamic_connection')->hasColumn($tablename, $columnname)) {
                    $addcolumn = DB::connection('dynamic_connection')->statement(
                        "ALTER TABLE $tablename ADD COLUMN $columnname $columnType$defaultSQL"
                    );
                }

                $invoicecolumn = $this->tbl_invoice_columnModel::create([
                    'column_name' => $request->column_name,
                    'column_type' => $request->column_type,
                    'column_width' => $request->column_width,
                    'default_value' => $request->default_value,
                    'company_id' => $request->company_id,
                    'column_order' => $columnsequence,
                    'created_by' => $this->userId,
                ]);

                if ($invoicecolumn) {
                    DB::connection('dynamic_connection')->table('invoices')->update([
                        'is_editable' => 0
                    ]);
                    return $this->successresponse(200, 'message', 'Invoice Columns  succesfully added');
                } else {
                    return $this->successresponse(500, 'message', 'Invoice Columns not succesfully added');
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
        if ($this->rp['invoicemodule']['mngcol']['edit'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $invoicecolumn = $this->tbl_invoice_columnModel::get();

        if ($invoicecolumn->isEmpty()) {
            return $this->successresponse(404, 'message', "No Such Invoice Column Found!");
        }

        if ($this->rp['invoicemodule']['mngcol']['alldata'] != 1) {
            if ($invoicecolumn->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }

        return $this->successresponse(200, 'invoicecolumn', $invoicecolumn);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {

        //condition for check if user has permission to search record
        if ($this->rp['invoicemodule']['mngcol']['edit'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $invoicecolumn = $this->tbl_invoice_columnModel::find($id);

        if (!$invoicecolumn) {
            return $this->successresponse(404, 'message', "No Such Invoice Column Found!");
        }
        if ($this->rp['invoicemodule']['mngcol']['alldata'] != 1) {
            if ($invoicecolumn->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }

        return $this->successresponse(200, 'invoicecolumn', $invoicecolumn);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //condition for check if user has permission to search record
        if ($this->rp['invoicemodule']['mngcol']['edit'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $validator = Validator::make($request->all(), [
            'column_name' => 'required|string|max:50',
            'column_type' => 'required|string|max:50',
            'column_width' => 'required|numeric',
            'default_value' => 'nullable|string|max:200',
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
            $defaultcolumns = ['invoice_id', 'id', 'amount', 'created_by', 'updated_by', 'created_at', 'updated_at', 'is_active', 'is_deleted'];
           
            if (in_array($modifiedcolumnname, $defaultcolumns)) {
                return $this->successresponse(500, 'message', "'$request->column_name'" . " column is a default system field and cannot be added or modified manually, so there is no need to add it again.");
            }

            $invoicecolumn = $this->tbl_invoice_columnModel::find($id);

            if (!$invoicecolumn) {
                return $this->successresponse(404, 'message', 'No Such Invoice Column Found!');
            }
            if ($this->rp['invoicemodule']['mngcol']['alldata'] != 1) {
                if ($invoicecolumn->created_by != $this->userId) {
                    return $this->successresponse(500, 'message', 'You are Unauthorized');
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

            // Step 2: Manually check for invalid default on longtext
            if (
                $request->column_type === 'longtext' &&
                $request->has('default_value') &&
                !empty($request->default_value)
            ) {
                return $this->successresponse('500','message','Default value is not allowed for longtext columns.');
            }

            $columnType = $columnTypes[$request->column_type];
            $oldcolumnvaluewithoutchange = $invoicecolumn->column_name; // for formula table change 
            $oldcolumnname = str_replace(' ', '_', $invoicecolumn->column_name);
            $columnname = str_replace(' ', '_', $request->column_name);

           // Handle default value
            $defaultSQL = '';
            if (!empty($request->default_value)) {
                $defaultVal = addslashes($request->default_value); // Escaping quotes if needed
                $defaultSQL = " DEFAULT '{$defaultVal}'";
            }

            // Change column name and type and apply default value (if provided)
            DB::connection('dynamic_connection')->statement(
                "ALTER TABLE mng_col CHANGE $oldcolumnname $columnname $columnType$defaultSQL"
            );

            // replace old column name with new columnname in invoice table > show_col column
            DB::connection('dynamic_connection')
                ->table('invoices')
                ->whereRaw("show_col LIKE '%$oldcolumnname%'")
                ->update(['show_col' => DB::raw("REPLACE(show_col, '$oldcolumnname', '$columnname')")]);


            // change column name in formula table
            DB::connection('dynamic_connection')
                ->table('tbl_invoice_formulas')
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


            // update column name in tbl invoice column 
            $invoicecolumn->update([
                'column_name' => $request->column_name,
                'column_type' => $request->column_type,
                'column_width' => $request->column_width,
                'default_value' => $request->default_value,
                'updated_by' => $this->userId,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            return $this->successresponse(200, 'message', 'Invoice Column succesfully updated');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {

        //condition for check if user has permission to search record
        if ($this->rp['invoicemodule']['mngcol']['delete'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        // fetch column data from tbl invoice column table
        $invoicecolumn = $this->tbl_invoice_columnModel::find($id);

        // if column data not found
        if (!$invoicecolumn) {
            return $this->successresponse(404, 'message', 'No Such Invoice Column Found!');
        }

        if ($this->rp['invoicemodule']['mngcol']['alldata'] != 1) {
            if ($invoicecolumn->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }
        $tablename = 'mng_col';
        $columname = $invoicecolumn->column_name;
        $modifiedcolumname = str_replace(' ', '_', $invoicecolumn->column_name);

        // check if column is using or not into formula if it is using then user will not able to delete it
        $checkrec = DB::connection('dynamic_connection')
            ->table('tbl_invoice_formulas')
            ->where(function ($query) use ($columname) {
                $query->where('first_column', $columname)
                    ->orWhere('second_column', $columname)
                    ->orWhere('output_column', $columname);
            })->where('is_deleted', 0)
            ->get();

        if ($checkrec->isNotEmpty()) {
            return $this->successresponse(404, 'message', 'This column is using in Formula so please first remove it from formula!');
        }

        // drop column from mng col table it is it exist
        if (Schema::connection('dynamic_connection')->hasColumn($tablename, $modifiedcolumname)) {
            DB::connection('dynamic_connection')->statement("ALTER TABLE $tablename DROP COLUMN $modifiedcolumname");
        }

        // remove column name from invoice table  > show_col table
        DB::connection('dynamic_connection')
            ->table('invoices')
            ->whereRaw("show_col LIKE '%$modifiedcolumname,%'") // Match when the old column is not the last column
            ->orWhereRaw("show_col LIKE '$modifiedcolumname,%'") // Match when the old column is the last column
            ->orWhereRaw("show_col LIKE '%$modifiedcolumname'") // Match when the old column is the first or only column
            ->update([
                'show_col' => DB::raw("TRIM(BOTH ',' FROM REPLACE(CONCAT(',', show_col, ','), ',$modifiedcolumname,', ','))")
            ]);

        // change column status is deleted = 1 that means column is deleted
        $invoicecolumn->update([
            'is_deleted' => 1
        ]);

        return $this->successresponse(200, 'message', 'Invoice Column succesfully deleted');



    }

    /**
     * Hide the specified Record from Invoice form.
     */
    public function hide(Request $request, string $id)
    {

        if ($this->rp['invoicemodule']['mngcol']['edit'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $invoicecolumn = $this->tbl_invoice_columnModel::find($id);


        if (!$invoicecolumn) {
            return $this->successresponse(404, 'message', 'No Such Invoice Column Found!');
        }
        if ($this->rp['invoicemodule']['mngcol']['alldata'] != 1) {
            if ($invoicecolumn->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }
        $invoicecolumn->update([
            'is_hide' => $request->hidevalue
        ]);
        DB::connection('dynamic_connection')->table('invoices')->update([
            'is_editable' => 0
        ]);
        return $this->successresponse(200, 'message', 'Invoice Column succesfully updated');
    }

    /**
     * set column order.
     */
    public function columnorder(Request $request)
    {

        if ($this->rp['invoicemodule']['mngcol']['edit'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $successCount = 0;
        $errorCount = 0;

        foreach ($request->columnorders as $key => $columnOrder) {
            if ($columnOrder !== null) {
                $updateResult = $this->tbl_invoice_columnModel::where('id', $key)
                    ->update(['column_order' => $columnOrder]);

                if ($updateResult) {
                    $successCount++;
                } else {
                    $errorCount++;
                }
            }
        }

        if ($successCount > 0) {
            return $this->successresponse(200, 'message', 'Column Order Succesfully updated');
        } else {
            return $this->successresponse(404, 'message', 'Column Order Not Succesfully upadated');
        }
    }
}
