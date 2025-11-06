<?php

namespace App\Http\Controllers\v4_3_0\api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class inventoryController extends commonController
{

    public $userId, $companyId, $masterdbname, $rp, $productModel, $inventoryModel, $purchaseModel;

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

        $this->productModel = $this->getmodel('product');
        $this->inventoryModel = $this->getmodel('inventory');
        $this->purchaseModel = $this->getmodel('Purchase');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        // condition for check if user has permission to view records
        if ($this->rp['inventorymodule']['inventory']['view'] != 1) {
            return response()->json([
                'status' => 500,
                'message' => 'You are Unauthorized',
                'data' => [],
                'recordsTotal' => 0,
                'recordsFiltered' => 0
            ]);
        }

        $inventoryres = $this->inventoryModel::join('products', 'inventory.product_id', 'products.id')
            ->leftJoin('purchase_order_details as pod', function ($join) {
                $join->on('inventory.product_id', '=', 'pod.product_id')
                    ->where('pod.is_deleted', '=', 0)
                    ->where('pod.is_active', '=', 1); // Make sure we only join non-deleted pod records
            })
            ->select(
                'products.name',
                'products.sku',
                'inventory.*',
                DB::raw('COALESCE(SUM(pod.quantity), 0) - COALESCE(SUM(pod.accepted), 0)  - COALESCE(SUM(pod.rejected), 0) AS incoming_count') // Use COALESCE to avoid nulls
            )
            ->where('products.is_deleted', 0)
            ->where('products.track_quantity', 1)
            ->groupBy(
                'products.id',
                'products.name',
                'products.sku',
                'inventory.id',
                'inventory.product_id',
                'inventory.damaged',
                'inventory.quality_control',
                'inventory.safety_stock',
                'inventory.other',
                'inventory.available',
                'inventory.on_hand',
                'inventory.incoming',
                'inventory.is_active',
                'inventory.is_deleted',
                'inventory.created_at',
                'inventory.updated_at',
            ); // Ensure that the results are grouped by product and inventory ID

        if ($this->rp['inventorymodule']['inventory']['alldata'] != 1) {
            $inventoryres->where('products.created_by', $this->userId);
        }

        $totalcount = $inventoryres->get()->count(); // count total record

        $inventory = $inventoryres->get();

        if ($inventory->isEmpty()) {
            return DataTables::of($inventory)
                ->with([
                    'status' => 404,
                    'message' => 'No Data Found',
                    'recordsTotal' => $totalcount, // Total records count
                ])
                ->make(true);
        }

        return DataTables::of($inventory)
            ->with([
                'status' => 200,
                'recordsTotal' => $totalcount, // Total records count
            ])
            ->make(true);
    }

    public function quantityupdate(Request $request, int $id)
    {
        //condition for check if user has permission to edit record
        if ($this->rp['inventorymodule']['inventory']['edit'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $validator = Validator::make($request->all(), [
            'product_id' => 'required|numeric',
            'quantity' => 'required|numeric',
            'targetcolumn' => 'required|string',
            'type' => 'required|string',
            'company_id' => 'required|numeric',
            'user_id' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return $this->errorresponse(422, $validator->messages());
        } else {

            $targetcolumn = $request->targetcolumn;
           
            $inventory = $this->inventoryModel::find($id);

            if (!$inventory) {
                return $this->successresponse(404, 'message', 'No such product inventory found!');
            }

            if ($request->type == 'add') {
                $inventory->$targetcolumn += $request->quantity;
                $inventory->on_hand += $request->quantity;
                $inventory->save();
            } elseif ($request->type == 'move') {
                $inventory->$targetcolumn -= $request->quantity;
                $inventory->available += $request->quantity;
                $inventory->save();
            } elseif ($request->type == 'delete') {
                $inventory->$targetcolumn -= $request->quantity;
                $inventory->on_hand -= $request->quantity;
                $inventory->save();
            }

            return $this->successresponse(200, 'message', 'Quantity updated.');

        }
    }

    public function onhandquantityupdate(Request $request, int $id)
    {
        //condition for check if user has permission to edit record
        if ($this->rp['inventorymodule']['inventory']['edit'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $validator = Validator::make($request->all(), [
            'quantity' => 'required|numeric',
            'company_id' => 'required|numeric',
            'user_id' => 'required|numeric',

        ]);
        if ($validator->fails()) {
            return $this->errorresponse(422, $validator->messages());
        } else { 
            $inventory = $this->inventoryModel::find($id);

            if (!$inventory) {
                return $this->successresponse(404, 'message', 'No such product inventory found!');
            }

            $inventory->on_hand = $inventory->on_hand + $request->quantity;
            $inventory->available = $inventory->available + $request->quantity;

            $inventory->save();

            return $this->successresponse(200, 'message', 'Quantity updated.');
        }
    }

    public function availablequantityupdate(Request $request, int $id)
    {
        //condition for check if user has permission to edit record
        if ($this->rp['inventorymodule']['inventory']['edit'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $validator = Validator::make($request->all(), [
            'available_type' => 'required|string',
            'quantity' => 'required|numeric',
            'company_id' => 'required|numeric',
            'user_id' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return $this->errorresponse(422, $validator->messages());
        } else { 
            $inventory = $this->inventoryModel::find($id);

            if (!$inventory) {
                return $this->successresponse(404, 'message', 'No such product inventory found!');
            }

            if ($request->available_type == 'adjust') {
                $inventory->available += $request->quantity;
                $inventory->on_hand += $request->quantity;
            } else if ($request->available_type == 'move') {
                $reason = $request->reason;
                $inventory->available -= $request->quantity;
                $inventory->$reason += $request->quantity;
            }

            $inventory->save();


            return $this->successresponse(200, 'message', 'Quantity updated.');

        }
    }

    public function incominginventory(int $id)
    {
        // condition for check if user has permission to view records
        if ($this->rp['inventorymodule']['inventory']['view'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized.');
        }

        $inventory = $this->inventoryModel::find($id);

        if (!$inventory) {
            return $this->successresponse(404, 'message', 'No such inventory found.');
        }

        if ($this->rp['inventorymodule']['inventory']['alldata'] != 1) {
            if ($inventory->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized.');
            }
        }

        $productid = $inventory->product_id;

        $incominginventory = $this->purchaseModel::join('purchase_order_details as pod', 'purchases.id', 'pod.purchase_id')
            ->where('purchases.is_active', 1)
            ->where('purchases.is_deleted', 0)
            ->where('pod.product_id', $productid)
            ->where('pod.is_deleted', 0)
            ->where('pod.is_active', 1)
            ->distinct();

        if ($this->rp['inventorymodule']['inventory']['alldata'] != 1) {
            $incominginventory = $incominginventory->where('purchases.created_by', $this->userId);
        }

        $incominginventory = $incominginventory->groupBy('purchases.id')
            ->having(DB::raw('COALESCE(SUM(pod.quantity), 0) - COALESCE(SUM(pod.accepted), 0) - COALESCE(SUM(pod.rejected), 0)'), '>', 0)
            ->pluck(DB::raw('COALESCE(SUM(pod.quantity), 0) - COALESCE(SUM(pod.accepted), 0)  - COALESCE(SUM(pod.rejected), 0) AS incoming_count'), 'purchases.id');


        if ($incominginventory->isEmpty()) {
            return $this->successresponse(404, 'incominginventory', 'No such purchase order found.');
        }

        return $this->successresponse(200, 'incominginventory', $incominginventory);

    }
}
