<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\tbl_invoice_column;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

class tblinvoicecolumnController extends Controller
{

    //  for formula list

    public function formula(Request $request)
    {
        $userId = $request->input('user_id');

        $invoicecolumn = tbl_invoice_column::all()->whereIn('column_type', ['decimal', 'percentage', 'number'])->where('company_id', $userId)->where('is_deleted', 0);

        if ($invoicecolumn->count() > 0) {
            return response()->json([
                'status' => 200,
                'invoicecolumn' => $invoicecolumn
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'invoicecolumn' => 'No Records Found'
            ]);
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $userId = $request->input('user_id');

        $invoicecolumn = tbl_invoice_column::where('company_id', $userId)
            ->orderBy('column_order')
            ->where('is_deleted', 0)
            ->get();

        if ($invoicecolumn->count() > 0) {
            return response()->json([
                'status' => 200,
                'invoicecolumn' => $invoicecolumn
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'invoicecolumn' => 'No Records Found'
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
            'column_name' => 'required|string|max:50',
            'column_type' => 'required|string|max:50',
            'company_id' => 'required|numeric',
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
            ]);
        } else {

            $invoicecolumn = tbl_invoice_column::all()
                ->where('column_name', $request->column_name)
                ->where('company_id', $request->company_id)
                ->where('is_deleted', 0);
            if ($invoicecolumn->count() > 0) {
                return response()->json([
                    'status' => 500,
                    'message' => $request->column_name . ' Columns Already Exicst'
                ]);
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
                $tablename = 'mng_col_' . $request->company_id;
                $columnname = str_replace(' ', '_', $request->column_name);
                if (DB::statement("ALTER TABLE $tablename ADD COLUMN  $columnname  $columnType")) {
                    $invoicecolumn = tbl_invoice_column::create([
                        'column_name' => $request->column_name,
                        'column_type' =>  $request->column_type,
                        'company_id' => $request->company_id,
                        'created_by' => $request->created_by,

                    ]);

                    if ($invoicecolumn) {
                        return response()->json([
                            'status' => 200,
                            'message' => 'Invoice Columns  succesfully added'
                        ], 200);
                    } else {
                        return response()->json([
                            'status' => 500,
                            'message' => 'Invoice Columns not succesfully added'
                        ]);
                    }
                } else {
                    return response()->json([
                        'status' => 500,
                        'message' => 'Invoice Columns not succesfully added'
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
        $invoicecolumn = tbl_invoice_column::get()->where('company_id', $id);
        if ($invoicecolumn->count() > 0) {
            return response()->json([
                'status' => 200,
                'invoicecolumn' => $invoicecolumn
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'invoicecolumn' => $invoicecolumn,
                'message' => "No Such Invoice Column Found!"
            ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $invoicecolumn = tbl_invoice_column::find($id);
        if ($invoicecolumn->count() > 0) {
            return response()->json([
                'status' => 200,
                'invoicecolumn' => $invoicecolumn
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'invoicecolumn' => $invoicecolumn,
                'message' => "No Such Invoice Column Found!"
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

        $validator = Validator::make($request->all(), [
            'column_name' => 'required|string|max:50',
            'column_type' => 'required|string|max:50',
            'updated_by'  => 'required|numeric',
            'created_by',
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

            $invoicecolumn = tbl_invoice_column::find($id);
            if ($invoicecolumn) {
                date_default_timezone_set('Asia/Kolkata');
                $invoicecolumn->update([
                    'column_name' => $request->column_name,
                    'column_type' => $request->column_type,
                    'updated_by' => $request->updated_by,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

                return response()->json([
                    'status' => 200,
                    'message' => 'Invoice Column succesfully updated'
                ]);
            } else {
                return response()->json([
                    'status' => 404,
                    'message' => 'No Such Invoice Column Found!'
                ]);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {

        //  $tablename = 'mng_tbl_'.$request->company_id;
        $fetchcolumnname = DB::table('tbl_invoice_columns')->select('column_name')->where('id', $id)->first();
        if ($fetchcolumnname) {
            $columname = $fetchcolumnname->column_name;
            $tablename = 'mng_col_8';

            if (Schema::hasColumn($tablename, $columname)) {
                $checkrec = DB::table($tablename)->select($columname)->get();
                if ($checkrec->count() > 0) {
                    return response()->json([
                        'status' => 404,
                        'message' => 'This column has data so you can not delete this column!'
                    ]);
                } else {

                    DB::statement("ALTER TABLE $tablename DROP COLUMN $columname");
                    $invoicecolumn = tbl_invoice_column::find($id);
                    $invoicecolumn->update([
                        'is_deleted' => 1
                    ]);
                    return response()->json([
                        'status' => 200,
                        'message' => 'Invoice Column succesfully deleted'
                    ]);
                }
            } else {
                $invoicecolumn = tbl_invoice_column::find($id);

                if ($invoicecolumn) {
                    $invoicecolumn->update([
                        'is_deleted' => 1
                    ]);
                    return response()->json([
                        'status' => 200,
                        'message' => 'Invoice Column succesfully deleted'
                    ]);
                } else {
                    return response()->json([
                        'status' => 404,
                        'message' => 'No Such Invoice Column Found!'
                    ]);
                }
                return response()->json([
                    'status' => 404,
                    'message' => 'No Such Column Found In Table!'
                ]);
            }
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'No Such Invoice Column Found!'
            ]);
        }
    }



    /**
     * Hide the specified Record from Invoice form.
     */
    public function hide(Request $request, string $id)
    {
        $invoicecolumn = tbl_invoice_column::find($id);

        if ($invoicecolumn) {
            $invoicecolumn->update([
                'is_hide' => $request->hidevalue
            ]);
            return response()->json([
                'status' => 200,
                'message' => 'Invoice Column succesfully updated'
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'No Such Invoice Column Found!'
            ]);
        }
    }

    /**
     * set column order.
     */
    public function columnorder(Request $request)
    {
        $successCount = 0;
        $errorCount = 0;

        foreach ($request->columnorders as $key => $columnOrder) {
            if ($columnOrder !== null) {
                $updateResult =  tbl_invoice_column::where('id', $key)
                    ->update(['column_order' => $columnOrder]);

                if ($updateResult) {
                    $successCount++;
                } else {
                    $errorCount++;
                }
            }
        }

        if ($successCount > 0) {
            return response()->json([
                'status' => 200,
                'message' => 'Column Order Succesfully updated'
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'Column Order Not Succesfully upadated'
            ]);
        }
    }
}
