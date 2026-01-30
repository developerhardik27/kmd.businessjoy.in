<?php

namespace App\Http\Controllers\v4_3_2\api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class brokeragebillController extends commonController
{
    public $userId, $companyId, $masterdbname, $rp, $brokerpurchaseModel, $order_detailModel, $gradenModel;

    public function __construct(Request $request)
    {

        $this->companyId = $request->company_id;
        $this->userId = $request->user_id;

        $this->dbname($this->companyId);
        $user_rp = DB::connection('dynamic_connection')->table('user_permissions')->where('user_id', $this->userId)->value('rp');

        if (empty($user_rp)) {
            $this->customerrorresponse();
        }

        $this->rp = json_decode($user_rp, true);

        $this->masterdbname = DB::connection()->getDatabaseName();

        $this->brokerpurchaseModel = $this->getmodel('broker_purchase');
        $this->order_detailModel = $this->getmodel('order_detail');
        $this->gradenModel = $this->getmodel('graden');
    }
    public function getGardens()
    {
        if ($this->rp['teamodule']['brokeragebill']['view'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }
        $gardens = $this->order_detailModel
            ::join('gardens', 'gardens.id', '=', 'order_details.garden_id')
            ->where('order_details.is_delete', 0)
            ->select(
                'gardens.id as garden_id',
                'gardens.garden_name as garden_name'
            )
            ->distinct()
            ->orderBy('gardens.garden_name', 'ASC')
            ->get();

        return $this->successresponse(200, 'data', $gardens);
    }


    public function getOtherData(Request $request)
    {
        if ($this->rp['teamodule']['brokeragebill']['view'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }
        $usedInvoices = $this->brokerpurchaseModel
            ::where('broker_purchases.is_delete', 0)
            ->where('broker_purchases.garden_id', $request->garden_id)
            ->leftJoin('gardens', 'gardens.id', '=', 'broker_purchases.garden_id')
            ->leftJoin('grades', 'grades.id', '=', 'broker_purchases.grade')
            ->select(
                'broker_purchases.*',
                'gardens.garden_name as garden_name',
                'grades.grade as grade'
            )
            ->get();

        return $this->successresponse(200, 'data', $usedInvoices);
    }

    public function index()
    {
        if ($this->rp['teamodule']['brokeragebill']['view'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }
        $brokerpurchase = $this->brokerpurchaseModel
            ::join('gardens', 'gardens.id', '=', 'broker_purchases.garden_id')
            ->select(
                'broker_purchases.garden_id',
                'gardens.garden_name',
                DB::raw('SUM(broker_purchases.bags) as total_bags'),
                DB::raw('SUM(broker_purchases.net_kg) as total_net_kg'),
                DB::raw('SUM(broker_purchases.brokerage) as total_brokerage')
            )
            ->where('broker_purchases.is_delete', 0)
            ->groupBy('broker_purchases.garden_id', 'gardens.garden_name')
            ->get();

        if ($brokerpurchase->isEmpty()) {
            return DataTables::of($brokerpurchase)
                ->with([
                    'status' => 404,
                    'message' => 'No Data Found',
                ])
                ->make(true);
        }
        return DataTables::of($brokerpurchase)
            ->with([
                'status' => 200,
            ])
            ->make(true);
    }

    public function store(Request $request)
    {
        if ($this->rp['teamodule']['brokeragebill']['add'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }
        foreach ($request->rows as $row) {
            $create = $this->brokerpurchaseModel
                ::where('id', $row['id'])
                ->update([
                    'brokerage'  => $row['brokerage'],
                    'updated_by' => $request->user_id,
                ]);
        }
        if ($create) {
            return $this->successresponse(200, 'message', 'Broker Bill succesfully ');
        } else {
            return $this->successresponse(500, 'message', 'Broker Bill not succesfully  !');
        }
    }
    public function edit($id)
    {
        if ($this->rp['teamodule']['brokeragebill']['edit'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }
        $brokerpurchase = $this->brokerpurchaseModel::find($id);
        if ($this->rp['teamodule']['brokeragebill']['alldata'] != 1) {
            if ($brokerpurchase->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }
        if (!$brokerpurchase) {
            return $this->successresponse(500, 'message', 'Broker Purchase not found !');
        }
        return $this->successresponse(200, 'brokerpurchase', $brokerpurchase);
    }
  
    public function destroy($id)
    {
        if ($this->rp['teamodule']['brokerpurchase']['delete'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }
        $brokerpurchase = $this->brokerpurchaseModel::find($id);
        if ($this->rp['teamodule']['brokerpurchase']['alldata'] != 1) {
            if ($brokerpurchase->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }
        if (!$brokerpurchase) {
            return $this->successresponse(500, 'message', 'Broker Purchase not found !');
        }
        $brokerpurchase->update(
            [
                "is_delete" => 1
            ]
        );

        return $this->successresponse(200, 'message', 'Broker Purchase succesfully deleted');
    }
}
