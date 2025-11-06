<?php

namespace App\Http\Controllers\v4_3_0\api;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class purchaseController extends commonController
{

    public $userId, $companyId, $masterdbname, $rp, $purchaseModel, $purchase_order_detailModel, $purchase_historyModel, $purchase_order_detail_historyModel, $inventoryModel;

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

        $this->purchaseModel = $this->getmodel('Purchase');
        $this->purchase_order_detailModel = $this->getmodel('purchase_order_detail');
        $this->purchase_historyModel = $this->getmodel('purchase_history');
        $this->purchase_order_detail_historyModel = $this->getmodel('purchase_order_detail_history');
        $this->inventoryModel = $this->getmodel('inventory');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //condition for check if user has permission to view record
        if ($this->rp['inventorymodule']['purchase']['view'] != 1) {
            return response()->json([
                'status' => 500,
                'message' => 'You are Unauthorized',
                'data' => [],
                'recordsTotal' => 0,
                'recordsFiltered' => 0
            ]);
        }

        $purchaseres = $this->purchaseModel::leftJoin('suppliers', 'purchases.supplier_id', 'suppliers.id')
            ->leftJoin($this->masterdbname . '.country', 'purchases.currency', '=', $this->masterdbname . '.country.id')
            ->select(
                'purchases.id',
                'purchases.payment_terms',
                DB::raw("DATE_FORMAT(purchases.estimated_arrival , '%d-%M-%Y') as estimated_arrival_formatted"),
                'purchases.estimated_arrival',
                'purchases.shipping_carrier',
                'purchases.tracking_number',
                'purchases.tracking_url',
                'purchases.reference_number',
                'purchases.note_to_supplier',
                'purchases.taxes',
                'purchases.sub_total',
                'purchases.shipping',
                'purchases.discount',
                'purchases.total',
                'purchases.status',
                'purchases.total_items',
                'purchases.accepted',
                'purchases.rejected',
                'purchases.created_at',
                'purchases.is_active',
                DB::raw("
                CONCAT_WS(' ' , suppliers.firstname , suppliers.lastname) as suppliername
            "),
                'country.currency',
                'country.currency_symbol'
            )
            ->where('purchases.is_deleted', 0)
            ->orderBy('purchases.created_at', 'desc');

        if ($this->rp['inventorymodule']['purchase']['alldata'] != 1) {
            $purchaseres->where('purchases.created_by', $this->userId);
        }


        $totalcount = $purchaseres->get()->count(); // count total record
        $purchase = $purchaseres->get();

        if ($purchase->isEmpty()) {
            return DataTables::of($purchase)
                ->with([
                    'status' => 404,
                    'message' => 'No Data Found',
                    'recordsTotal' => $totalcount, // Total records count
                ])
                ->make(true);
        }

        return DataTables::of($purchase)
            ->with([
                'status' => 200,
                'recordsTotal' => $totalcount, // Total records count
            ])
            ->make(true);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {


        return $this->executeTransaction(function () use ($request) {

            //condition for check if user has permission to add new record
            if ($this->rp['inventorymodule']['purchase']['add'] != 1) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }

            $validator = Validator::make($request->all(), [
                'products' => 'required',
            ]);

            if ($validator->fails()) {
                return $this->errorresponse(422, $validator->messages());
            } else {

                $purchase = $this->purchaseModel::create([
                    'supplier_id' => $request->supplier,
                    'payment_terms' => $request->payment,
                    'currency' => $request->currency,
                    'estimated_arrival' => $request->estimated_arrival,
                    'shipping_carrier' => $request->shipping_carrier,
                    'tracking_number' => $request->tracking_number,
                    'tracking_url' => $request->tracking_url,
                    'reference_number' => $request->reference_number,
                    'note_to_supplier' => $request->note_to_supplier,
                    'taxes' => $request->taxes,
                    'sub_total' => $request->sub_total,
                    'shipping' => $request->shipping,
                    'discount' => $request->discount,
                    'total' => $request->total,
                    'total_items' => $request->itemcount,
                    'created_by' => $this->userId,
                ]);

                if ($purchase) {

                    $products = $request->products;

                    foreach ($products as $product) {

                        $purchase_order_details = $this->purchase_order_detailModel::create([
                            'purchase_id' => $purchase->id,
                            'product_id' => $product,
                            'product_name' => $request->{"product_name_$product"},
                            'supplier_sku' => $request->{"product_supplier_sku_$product"},
                            'quantity' => $request->{"product_quantity_$product"},
                            'price' => $request->{"product_price_$product"},
                            'tax' => $request->{"product_tax_$product"},
                            'total' => $request->{"product_total_amount_$product"},
                            'is_active' => 0
                        ]);

                    }

                    $purchase_history = $this->purchase_historyModel::create([
                        'purchase_id' => $purchase->id,
                        'action' => "You created this purchase order."
                    ]);

                    return $this->successresponse(200, 'message', 'purchase order succefully created', 'id', $purchase->id);

                } else {
                    throw new \Exception('purchase order creation failed!');
                }

            }

        });
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //condition for check if user has permission to search record
        if ($this->rp['inventorymodule']['purchase']['view'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $purchase =  $this->purchaseModel::leftJoin('suppliers', 'purchases.supplier_id', 'suppliers.id')
            ->leftJoin($this->masterdbname . '.country', 'purchases.currency', '=', $this->masterdbname . '.country.id')
            ->select(
                'purchases.id',
                'purchases.payment_terms',
                DB::raw("DATE_FORMAT(purchases.estimated_arrival , '%d-%M-%Y') as estimated_arrival_formatted"),
                'purchases.estimated_arrival',
                'purchases.shipping_carrier',
                'purchases.tracking_number',
                'purchases.tracking_url',
                'purchases.reference_number',
                'purchases.note_to_supplier',
                'purchases.taxes',
                'purchases.sub_total',
                'purchases.shipping',
                'purchases.discount',
                'purchases.total',
                'purchases.status',
                'purchases.total_items',
                'purchases.accepted',
                'purchases.rejected',
                'purchases.created_at',
                'purchases.is_active',
                DB::raw("
                    CONCAT_WS(' ' , suppliers.firstname , suppliers.lastname) as suppliername
                "),
                'suppliers.contact_no',
                DB::raw("
                    CONCAT_WS(', ' , suppliers.house_no_building_name , suppliers.road_name_area_colony,suppliers.pincode) as supplieraddress
                "),
                'country.currency',
                'country.currency_symbol'
            )
            ->where('purchases.id', $id)
            ->where('purchases.is_deleted', 0)
            ->first();

        if (!$purchase) {
            return $this->successresponse(404, 'message', "No such purchase order found!");
        }

        if ($this->rp['inventorymodule']['purchase']['alldata'] != 1) {
            if ($purchase->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }
      
        $purchase_order_details = $this->purchase_order_detailModel::join('products', 'products.id', 'purchase_order_details.product_id')
            ->where('purchase_order_details.purchase_id', $id)
            ->where('purchase_order_details.is_deleted', 0)
            ->select(
                'purchase_order_details.product_id',
                'purchase_order_details.product_name',
                'purchase_order_details.supplier_sku',
                'purchase_order_details.quantity',
                'purchase_order_details.price',
                'purchase_order_details.tax',
                'purchase_order_details.total',
                'purchase_order_details.accepted',
                'purchase_order_details.rejected',
                'products.track_quantity'
            )
            ->get();

        if ($purchase && $purchase_order_details) {
            return $this->successresponse(200, 'purchase', $purchase, 'purchase_order_details', $purchase_order_details);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //condition for check if user has permission to edit record
        if ($this->rp['inventorymodule']['purchase']['edit'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $purchase = $this->purchaseModel::where('purchases.id', $id)
            ->where('purchases.is_deleted', 0)
            ->first();

        if (!$purchase) {
            return $this->successresponse(404, 'message', "No such purchase order found!");
        }

        if ($this->rp['inventorymodule']['purchase']['alldata'] != 1) {
            if ($purchase->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }

        $purchase_order_details = $this->purchase_order_detailModel::join('products', 'products.id', 'purchase_order_details.product_id')
            ->where('purchase_order_details.purchase_id', $id)
            ->where('purchase_order_details.is_deleted', 0)
            ->select(
                'purchase_order_details.product_id',
                'purchase_order_details.product_name',
                'purchase_order_details.supplier_sku',
                'purchase_order_details.quantity',
                'purchase_order_details.price',
                'purchase_order_details.tax',
                'purchase_order_details.total',
                'purchase_order_details.accepted',
                'purchase_order_details.rejected',
                'products.track_quantity'
            )
            ->get();

        if ($purchase && $purchase_order_details) {
            return $this->successresponse(200, 'purchase', $purchase, 'purchase_order_details', $purchase_order_details);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

        return $this->executeTransaction(function () use ($request, $id) {
            //condition for check if user has permission to edit record
            if ($this->rp['inventorymodule']['purchase']['edit'] != 1) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            } 

            $validator = Validator::make($request->all(), [
                'products' => 'required',
            ]);

            if ($validator->fails()) {
                return $this->errorresponse(422, $validator->messages());
            } else {
                $purchase = $this->purchaseModel::find($id);

                if (!$purchase) {
                    return $this->successresponse(404, 'message', 'No Such purchase Found!');
                }

                $update = $purchase->update([
                    'supplier_id' => $request->supplier,
                    'payment_terms' => $request->payment,
                    'currency' => $request->currency,
                    'estimated_arrival' => $request->estimated_arrival,
                    'shipping_carrier' => $request->shipping_carrier,
                    'tracking_number' => $request->tracking_number,
                    'tracking_url' => $request->tracking_url,
                    'reference_number' => $request->reference_number,
                    'note_to_supplier' => $request->note_to_supplier,
                    'taxes' => $request->taxes,
                    'sub_total' => $request->sub_total,
                    'shipping' => $request->shipping,
                    'discount' => $request->discount,
                    'total' => $request->total,
                    'total_items' => $request->itemcount,
                    'updated_by' => $this->userId,
                ]);

                if ($update) {
                    $products = $request->products;

                    // Fetch the product IDs to be deleted
                    $productsToDelete = $this->purchase_order_detailModel::where('purchase_id', $id)
                        ->whereNotIn('product_id', $products)
                        ->where(function ($query) {
                            $query->whereNull('accepted')->orWhere('accepted', 0);  // 'accepted' is either NULL or 0
                        })
                        ->where(function ($query) {
                            $query->whereNull('rejected')->orWhere('rejected', 0);  // 'rejected' is either NULL or 0
                        })
                        ->pluck('product_id'); // Get the IDs of the products that will be updated


                    if ($productsToDelete->count() > 0) {
                        //delete removed products  
                        $deleteremovedproduct = $this->purchase_order_detailModel::where('purchase_id', $id)
                            ->whereIn('product_id', $productsToDelete)
                            ->update([
                                'is_deleted' => 1
                            ]);

                        $updateremovedproducthistory = $this->purchase_order_detail_historyModel::where('purchase_id', $id)
                            ->whereIn('product_id', $productsToDelete)
                            ->update([
                                'product_name' => "A removed item"
                            ]);
                    }


                    foreach ($products as $product) {

                        $purchase_order_details = $this->purchase_order_detailModel::updateOrInsert(
                            [
                                'purchase_id' => $purchase->id,
                                'product_id' => $product,
                                'is_deleted' => 0 // Check if the record has not been deleted
                            ],
                            [
                                'product_name' => $request->{"product_name_$product"},
                                'supplier_sku' => $request->{"product_supplier_sku_$product"},
                                'quantity' => $request->{"product_quantity_$product"},
                                'price' => $request->{"product_price_$product"},
                                'tax' => $request->{"product_tax_$product"},
                                'total' => $request->{"product_total_amount_$product"},
                                'is_active' => $purchase->is_active
                            ]
                        );

                    }

                    $purchase_history = $this->purchase_historyModel::create([
                        'purchase_id' => $purchase->id,
                        'action' => "You edited this purchase order."
                    ]);

                    return $this->successresponse(200, 'message', 'purchase order succesfully updated');

                } else {
                    throw new \Exception("Purchase order updation failed!");
                }
            }
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {

        return $this->executeTransaction(function () use ($id) {

            //condition for check if user has permission to delete record
            if ($this->rp['inventorymodule']['purchase']['delete'] != 1) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }

            $purchase = $this->purchaseModel::find($id);

            if (!$purchase) {
                return $this->successresponse(404, 'message', 'No such purchase order found!');
            }

            if ($this->rp['inventorymodule']['purchase']['alldata'] != 1) {
                if ($purchase->created_by != $this->userId) {
                    return $this->successresponse(500, 'message', 'You are Unauthorized');
                }
            }

            $purchase->update([
                'is_deleted' => 1
            ]);

            $purchase_history = $this->purchase_historyModel::where('purchase_id', $id)
                ->update([
                    'is_deleted' => 1
                ]);

            $purchase_order_details = $this->purchase_order_detailModel::where('purchase_id', $id)
                ->update([
                    'is_deleted' => 1
                ]);

            return $this->successresponse(200, 'message', 'purchase order succesfully deleted.');
        });
    }


    public function changestatus(Request $request, int $id)
    {
        //condition for check if user has permission to delete record
        if ($this->rp['inventorymodule']['purchase']['edit'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $purchase = $this->purchaseModel::find($id);

        if (!$purchase) {
            return $this->successresponse(404, 'message', "No such purchase order found!");
        }

        if ($this->rp['inventorymodule']['purchase']['alldata'] != 1) {
            if ($purchase->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }

        $message = '';

        switch ($request->status) {
            case "ordered":
                $purchase->status = $request->status;

                $purchase_order_details = $this->purchase_order_detailModel::where('purchase_id', $purchase->id)->update([
                    'is_active' => 1
                ]);

                $message = "Marked as ordered.";
                $purchase_history = $this->purchase_historyModel::create([
                    'purchase_id' => $id,
                    'action' => "Purchase order marked as ordered."
                ]);

                break;
            case "closed":
                $purchase->is_active = 0;

                $purchase_order_details = $this->purchase_order_detailModel::where('purchase_id', $purchase->id)->update([
                    'is_active' => 0
                ]);

                $message = "Purchase order closed.";
                $purchase_history = $this->purchase_historyModel::create([
                    'purchase_id' => $id,
                    'action' => "You closed this purchase order."
                ]);
                break;

            case "reopen":
                $purchase->is_active = 1;
                $purchase_order_details = $this->purchase_order_detailModel::where('purchase_id', $purchase->id)->update([
                    'is_active' => 1
                ]);
                $message = "Purchase order reopened.";
                $purchase_history = $this->purchase_historyModel::create([
                    'purchase_id' => $id,
                    'action' => "You reopened this purchase order."
                ]);
                break;
            default:
                break;

        }

        $purchase->save();

        return $this->successresponse(200, 'message', $message);


    }


    public function receiveinventory(Request $request, int $id)
    {

        return $this->executeTransaction(function () use ($request, $id) {
            //condition for check if user has permission to delete record
            if ($this->rp['inventorymodule']['purchase']['edit'] != 1) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }

            $purchase = $this->purchaseModel::find($id);

            if (!$purchase) {
                return $this->successresponse(404, 'message', "No such purchase order found!");
            }

            if ($this->rp['inventorymodule']['purchase']['alldata'] != 1) {
                if ($purchase->created_by != $this->userId) {
                    return $this->successresponse(500, 'message', 'You are Unauthorized');
                }
            }

            $productid = $this->purchase_order_detailModel::where('purchase_id', $id)
                ->where('is_deleted', 0)
                ->select('product_name', 'product_id')
                ->get();



            if ($productid->isEmpty()) {
                return $this->successresponse(500, 'message', 'Failed to receive inventory');
            }


            $acceptedcount = 0;

            $rejectedcount = 0;


            $purchase_history = $this->purchase_historyModel::create([
                'purchase_id' => $id,
                'action' => "yes"
            ]);


            foreach ($productid as $product) {

                if (
                    ((isset($request->{"accepted_" . $product->product_id}) && $request->{"accepted_" . $product->product_id} != '' && $request->{"accepted_" . $product->product_id} != 0))
                    ||
                    ((isset($request->{"rejected_" . $product->product_id}) && $request->{"rejected_" . $product->product_id} != '' && $request->{"rejected_" . $product->product_id} != 0))
                ) {

                    $create_purchase_order_detail_history = $this->purchase_order_detail_historyModel::create([
                        'purchase_id' => $id,
                        "purchase_history_id" => $purchase_history->id,
                        "product_id" => $product->product_id,
                        "product_name" => $product->product_name,
                        "accepted" => $request->{"accepted_" . $product->product_id},
                        "rejected" => $request->{"rejected_" . $product->product_id}
                    ]);

                    $fetchproduct = $this->purchase_order_detailModel::where('purchase_id', $id)
                        ->where('product_id', $product->product_id)
                        ->where('is_deleted', 0)
                        ->first();

                    $updateinventory = $this->inventoryModel::where('product_id', $product->product_id)
                        ->where('is_deleted', 0)
                        ->first();

                    if (
                        (isset($request->{"accepted_" . $product->product_id}) && $request->{"accepted_" . $product->product_id} != '' && $request->{"accepted_" . $product->product_id} != 0)
                        && (isset($request->{"rejected_" . $product->product_id}) && $request->{"rejected_" . $product->product_id} != '' && $request->{"rejected_" . $product->product_id} != 0)
                    ) {

                        if ($fetchproduct) {
                            $fetchproduct->accepted += ($request->{"accepted_" . $product->product_id});
                            $fetchproduct->rejected += ($request->{"rejected_" . $product->product_id});
                        }

                        if ($updateinventory) {
                            $updateinventory->available += ($request->{"accepted_" . $product->product_id});
                            $updateinventory->on_hand += ($request->{"accepted_" . $product->product_id});
                        }

                        $acceptedcount += ($request->{"accepted_" . $product->product_id});
                        $rejectedcount += ($request->{"rejected_" . $product->product_id});


                    } elseif (isset($request->{"accepted_" . $product->product_id}) && $request->{"accepted_" . $product->product_id} != '' && $request->{"accepted_" . $product->product_id} != 0) {

                        if ($fetchproduct) {
                            $fetchproduct->accepted += ($request->{"accepted_" . $product->product_id});
                        }

                        if ($updateinventory) {
                            $updateinventory->available += ($request->{"accepted_" . $product->product_id});
                            $updateinventory->on_hand += ($request->{"accepted_" . $product->product_id});
                        }

                        $acceptedcount += ($request->{"accepted_" . $product->product_id});

                    } elseif (isset($request->{"rejected_" . $product->product_id}) && $request->{"rejected_" . $product->product_id} != '' && $request->{"rejected_" . $product->product_id} != 0) {

                        if ($fetchproduct) {
                            $fetchproduct->rejected += ($request->{"rejected_" . $product->product_id});
                        }
                        $rejectedcount += ($request->{"rejected_" . $product->product_id});
                    }

                    $fetchproduct->save();
                    $updateinventory->save();

                }

            }

            if ($acceptedcount != 0 && $rejectedcount != 0) {

                $action = "You accepted and rejected products";

            } elseif ($acceptedcount != 0) {

                $action = "You accepted $acceptedcount products";

            } elseif ($rejectedcount != 0) {

                $action = "You rejected $rejectedcount  products";

            }


            $purchase_history->action = $action;

            $purchase_history->save();

            $purchase->accepted += $acceptedcount;
            $purchase->rejected += $rejectedcount;

            $purchase->status = 'partial';

            if ($purchase->accepted + $purchase->rejected == $purchase->total_items) {
                $purchase->status = 'received';
            }

            if ($purchase->save()) {
                return $this->successresponse(200, 'message', 'Inventory received');
            }

            return $this->successresponse(500, 'message', 'Failed to receive inventory');
        });

    }


    public function timeline(int $id)
    {

        //condition for check if user has permission to delete record
        if ($this->rp['inventorymodule']['purchase']['edit'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $timeline = $this->purchase_historyModel::leftJoin('purchase_order_detail_history as podhistory', 'purchase_history.id', 'podhistory.purchase_history_id')
            ->where('purchase_history.purchase_id', $id)
            ->where('purchase_history.is_deleted', 0)
            ->orderBy('purchase_history.created_at', 'desc')
            ->select(
                'purchase_history.id',
                'purchase_history.action',
                'podhistory.product_name',
                'podhistory.accepted',
                'podhistory.rejected',
                'purchase_history.created_at'
            )
            ->groupBy(
                'purchase_history.id',
                'purchase_history.action',
                'podhistory.product_name',
                'podhistory.accepted',
                'podhistory.rejected',
                'purchase_history.created_at'
            )->get();


        if ($timeline->isEmpty()) {
            return $this->successresponse(500, 'message', 'No timeline Found');
        }

        return $this->successresponse(200, 'timeline', $timeline);

    }
}
