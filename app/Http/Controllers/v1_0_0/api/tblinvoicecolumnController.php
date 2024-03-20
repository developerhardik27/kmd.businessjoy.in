<?php

namespace App\Http\Controllers\v1_0_0\api;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

class tblinvoicecolumnController extends commonController
{

    public $userId, $companyId, $masterdbname, $rp,$tbl_invoice_columnModel;

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

        $this->tbl_invoice_columnModel = $this->getmodel('tbl_invoice_column');
    }

    //  for formula list

    public function formula(Request $request)
    {
        //condition for check if user has permission to view record
        if ($this->rp['invoicemodule']['mngcol']['view'] != 1) {
            return response()->json([
                'status' => 500,
                'message' => 'You are Unauthorized'
            ]);
        }

        $invoicecolumn = $this->tbl_invoice_columnModel::all()->whereIn('column_type', ['decimal', 'percentage', 'number'])->where('is_deleted', 0);

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
        //condition for check if user has permission to view record
        if ($this->rp['invoicemodule']['mngcol']['view'] != 1) {
            return response()->json([
                'status' => 500,
                'message' => 'You are Unauthorized'
            ]);
        }

        $invoicecolumnres = $this->tbl_invoice_columnModel::orderBy('column_order')
            ->where('is_deleted', 0);

        if ($this->rp['invoicemodule']['mngcol']['alldata'] != 1) {
            $invoicecolumnres->where('created_by', $this->userId);
        }

        $invoicecolumn = $invoicecolumnres->get();

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
            'user_id' => 'required|numeric',
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
            //condition for check if user has permission to add record
            if ($this->rp['invoicemodule']['mngcol']['add'] != 1) {
                return response()->json([
                    'status' => 500,
                    'message' => 'You are Unauthorized'
                ]);
            }

            $invoicecolumn = $this->tbl_invoice_columnModel::all()
                ->where('column_name', $request->column_name)
                ->where('is_deleted', 0);
            if ($invoicecolumn->count() > 0) {
                return response()->json([
                    'status' => 500,
                    'message' => $request->column_name . ' Columns Already Exist'
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
                $tablename = 'mng_col';
                $columnname = str_replace(' ', '_', $request->column_name);
                if (DB::connection('dynamic_connection')->statement("ALTER TABLE $tablename ADD COLUMN  $columnname  $columnType")) {
                    $invoicecolumn = $this->tbl_invoice_columnModel::create([
                        'column_name' => $request->column_name,
                        'column_type' => $request->column_type,
                        'company_id' => $request->company_id,
                        'created_by' => $this->userId,

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

        //condition for check if user has permission to search  record
        if ($this->rp['invoicemodule']['mngcol']['edit'] != 1) {
            return response()->json([
                'status' => 500,
                'message' => "You are Unauthorized!"
            ]);
        }

        $invoicecolumn = $this->tbl_invoice_columnModel::get();

        if ($this->rp['invoicemodule']['mngcol']['alldata'] != 1) {
            if ($invoicecolumn->created_by != $this->userId) {
                return response()->json([
                    'status' => 500,
                    'message' => "You are Unauthorized!"
                ]);
            }
        }

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

        //condition for check if user has permission to search record
        if ($this->rp['invoicemodule']['mngcol']['edit'] != 1) {
            return response()->json([
                'status' => 500,
                'message' => "You are Unauthorized!"
            ]);
        }

        $invoicecolumn = $this->tbl_invoice_columnModel::find($id);

        if ($this->rp['invoicemodule']['mngcol']['alldata'] != 1) {
            if ($invoicecolumn->created_by != $this->userId) {
                return response()->json([
                    'status' => 500,
                    'message' => "You are Unauthorized!"
                ]);
            }
        }

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
            'user_id' => 'required|numeric',
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

            //condition for check if user has permission to search record
            if ($this->rp['invoicemodule']['mngcol']['edit'] != 1) {
                return response()->json([
                    'status' => 500,
                    'message' => "You are Unauthorized!"
                ]);
            }

            $invoicecolumn = $this->tbl_invoice_columnModel::find($id);

            if ($this->rp['invoicemodule']['mngcol']['alldata'] != 1) {
                if ($invoicecolumn->created_by != $this->userId) {
                    return response()->json([
                        'status' => 500,
                        'message' => "You are Unauthorized!"
                    ]);
                }
            }

            if ($invoicecolumn) {
                date_default_timezone_set('Asia/Kolkata');
                $invoicecolumn->update([
                    'column_name' => $request->column_name,
                    'column_type' => $request->column_type,
                    'updated_by' => $this->userId,
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

        //condition for check if user has permission to search record
        if ($this->rp['invoicemodule']['mngcol']['delete'] != 1) {
            return response()->json([
                'status' => 500,
                'message' => "You are Unauthorized!"
            ]);
        }

        $fetchcolumnname = DB::connection('dynamic_connection')->table('tbl_invoice_columns')->select('column_name')->where('id', $id)->first();
        if ($fetchcolumnname) {
            $columname = $fetchcolumnname->column_name;
            $tablename = 'mng_col';

            if (Schema::connection('dynamic_connection')->hasColumn($tablename, $columname)) {
                $checkrec = DB::connection('dynamic_connection')->table($tablename)->select($columname)->get();
                if ($checkrec->count() > 0) {
                    return response()->json([
                        'status' => 404,
                        'message' => 'This column has data so you can not delete this column!'
                    ]);
                } else {

                    DB::connection('dynamic_connection')->statement("ALTER TABLE $tablename DROP COLUMN $columname");
                    $invoicecolumn = $this->tbl_invoice_columnModel::find($id);
                    $invoicecolumn->update([
                        'is_deleted' => 1
                    ]);
                    return response()->json([
                        'status' => 200,
                        'message' => 'Invoice Column succesfully deleted'
                    ]);
                }
            } else {

                $invoicecolumn = $this->tbl_invoice_columnModel::find($id);

                if ($this->rp['invoicemodule']['mngcol']['alldata'] != 1) {
                    if ($invoicecolumn->created_by != $this->userId) {
                        return response()->json([
                            'status' => 500,
                            'message' => "You are Unauthorized!"
                        ]);
                    }
                }

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

        if ($this->rp['invoicemodule']['mngcol']['edit'] != 1) {
            return response()->json([
                'status' => 500,
                'message' => "You are Unauthorized!"
            ]);
        }

        $invoicecolumn = $this->tbl_invoice_columnModel::find($id);

        if ($this->rp['invoicemodule']['mngcol']['alldata'] != 1) {
            if ($invoicecolumn->created_by != $this->userId) {
                return response()->json([
                    'status' => 500,
                    'message' => "You are Unauthorized!"
                ]);
            }
        }

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

        if ($this->rp['invoicemodule']['mngcol']['edit'] != 1) {
            return response()->json([
                'status' => 500,
                'message' => "You are Unauthorized!"
            ]);
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
