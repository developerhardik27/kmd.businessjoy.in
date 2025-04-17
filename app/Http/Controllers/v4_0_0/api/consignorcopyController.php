<?php

namespace App\Http\Controllers\v4_0_0\api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class consignorcopyController extends commonController
{

    public $userId, $companyId, $masterdbname, $rp, $consignorModel, $consigneeModel, $consignor_copyModel, $logistic_settingModel, $consignor_copy_terms_and_conditionModel;

    public function __construct(Request $request)
    {
        if ($request->company_id) {
            $this->dbname($request->company_id);
            $this->companyId = $request->company_id;
        } else {
            $this->dbname(session()->get('company_id'));
        }
        if ($request->user_id) {
            $this->userId = $request->user_id;
        } else {
            $this->userId = session()->get('user_id');
        }
        $this->masterdbname = DB::connection()->getDatabaseName();

        $user_rp = DB::connection('dynamic_connection')
            ->table('user_permissions')
            ->select('rp')
            ->where('user_id', $this->userId)
            ->get();
        $permissions = json_decode($user_rp, true);
        $this->rp = json_decode($permissions[0]['rp'], true);

        $this->consignorModel = $this->getmodel('consignor');
        $this->consigneeModel = $this->getmodel('consignee');
        $this->consignor_copyModel = $this->getmodel('consignor_copy');
        $this->logistic_settingModel = $this->getmodel('logistic_setting');
        $this->consignor_copy_terms_and_conditionModel = $this->getmodel('consignor_copy_terms_and_condition');
    }


    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $consignorcopy = $this->consignor_copyModel::leftjoin('consignees', 'consignor_copy.consignee_id', 'consignees.id')
            ->leftjoin('consignors', 'consignor_copy.consignor_id', 'consignors.id')
            ->select(
                'consignor_copy.id',
                'consignor_copy.consignment_note_no',
                'consignor_copy.loading_date',
                'consignor_copy.stuffing_date',
                'consignor_copy.truck_number',
                'consignor_copy.driver_name',
                'consignor_copy.licence_number',
                'consignor_copy.mobile_number',
                'consignor_copy.from',
                'consignor_copy.to',
                'consignor_copy.to_2',
                'consignor_copy.gst_tax_payable_by',
                DB::raw("
                    CONCAT_WS(' ', 
                        consignees.firstname, 
                        consignees.lastname 
                    ) as consignee
                "),
                DB::raw("
                    CONCAT_WS(' ', 
                        consignors.firstname, 
                        consignors.lastname 
                    ) as consignor
                "),
                'consignor_copy.cha',
                'consignor_copy.type',
                'consignor_copy.container_no',
                'consignor_copy.size',
                'consignor_copy.shipping_line',
                'consignor_copy.seal_no',
                'consignor_copy.be_inv_no',
                'consignor_copy.port',
                'consignor_copy.pod',
                'consignor_copy.service',
                'consignor_copy.sac_code',
                'consignor_copy.weight_type',
                'consignor_copy.actual',
                'consignor_copy.charged',
                'consignor_copy.value',
                'consignor_copy.paid',
                'consignor_copy.to_pay',
                'consignor_copy.reached_at_factory_date',
                'consignor_copy.reached_at_factory_time',
                'consignor_copy.left_from_factory_date',
                'consignor_copy.left_from_factory_time',
                'consignor_copy.t_and_c_id',
                DB::raw("DATE_FORMAT(consignor_copy.created_at, '%d-%M-%Y %h:%i %p') as created_at_formatted"),
            )
            ->where('consignor_copy.is_deleted', 0)
            ->where('consignors.is_deleted', 0)
            ->where('consignees.is_deleted', 0);

        if ($this->rp['logisticmodule']['consignorcopy']['alldata'] != 1) {
            $consignorcopy->where('consignor_copy.created_by', $this->userId);
        }


        //applyfilters

        $filters = [
            'filter_consignment_no' => 'consignment_note_no',
            'filter_container_no' => 'container_no',
            'filter_loading_date_from' => 'loading_date',
            'filter_loading_date_to' => 'loading_date',
            'filter_stuffing_date_from' => 'stuffing_date',
            'filter_stuffing_date_to' => 'stuffing_date',
            'filter_truck_no' => 'truck_number',
            'filter_location_from' => 'from',
            'filter_location_to' => 'to',
            'filter_location_to_2' => 'to_2',
            'filter_consignee' => 'consignee_id',
            'filter_consignor' => 'consignor_id',
        ];

        // Loop through the filters and apply them conditionally
        foreach ($filters as $requestKey => $column) {
            $value = $request->$requestKey;

            if (isset($value)) {
                if (strpos($requestKey, 'from') !== false || strpos($requestKey, 'to') !== false) {
                    // For date filters (loading_date, stuffing_date), we apply range conditions
                    $operator = strpos($requestKey, 'from') !== false ? '>=' : '<=';
                    $consignorcopy->where("consignor_copy.$column", $operator, $value);
                } else {
                    // For other filters, apply simple equality checks
                    $consignorcopy->where("consignor_copy.$column", $value);
                }
            }
        }

        // Handle location filters separately, to avoid conflicts
        if (isset($request->filter_location_from)) {
            $consignorcopy->where('consignor_copy.from', $request->filter_location_from);
        }

        if (isset($request->filter_location_to)) {
            $consignorcopy->where('consignor_copy.to', $request->filter_location_to);
        }

        if (isset($request->filter_location_to_2)) {
            $consignorcopy->where('consignor_copy.to_2', $request->filter_location_to_2);
        }


        $consignorcopy = $consignorcopy->get();

        if ($consignorcopy->isEmpty()) {
            return $this->successresponse(404, 'consignorcopy', 'No records found');
        }

        $latesttcid = $this->consignor_copy_terms_and_conditionModel::where('is_active', 1)->first();

        if ($this->rp['logisticmodule']['consignorcopy']['view'] != 1) {
            return $this->successresponse(500, 'message', 'You are unauthorized');
        }

        $data = [
            'consignorcopy' => $consignorcopy,
            'latesttcid' => $latesttcid
        ];

        return $this->successresponse(200, 'consignorcopy', $data);

    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'loading_date' => 'required|date',
            'stuffing_date' => 'required|date',
            'truck_number' => 'nullable|string',
            'driver_name' => 'nullable|string',
            'licence_number' => 'nullable|string',
            "mobile_number" => "nullable|numeric",
            "from" => "required|string",
            "to" => "required|string",
            "to_2" => "nullable|string",
            "gst_tax_payable_by" => "nullable|string",
            "consignor" => "required|numeric",
            "consignee" => "required|numeric",
            "cha" => "nullable|string",
            "weight" => "nullable|string",
            "type" => "required|string",
            "container_no" => "required|string",
            "size" => "nullable|string",
            "shipping_line" => "nullable|string",
            "seal_no" => "nullable|string",
            "be_inv_no" => "nullable|string",
            "port" => "nullable|string",
            "pod" => "nullable|string",
            "service" => "nullable|string",
            "sac_code" => "nullable|string",
            "actual" => "nullable|numeric",
            "charged" => "nullable|numeric",
            "paid" => "nullable|numeric",
            "pay" => "nullable|numeric",
            "value" => "nullable|numeric",
            "reached_at_factory_date" => "nullable|date",
            "reached_at_factory_time" => "nullable|date_format:H:i",
            "left_from_factory_date" => "nullable|date",
            "left_from_factory_time" => "nullable|date_format:H:i",
        ]);


        if ($validator->fails()) {
            return $this->errorresponse(422, $validator->messages());
        } else {

            if ($this->rp['logisticmodule']['consignorcopy']['add'] != 1) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }


            $consigment_note_number = 1;
            $t_and_c = null;

            $getconsigment_note_number = $this->logistic_settingModel::first();

            if ($getconsigment_note_number) {
                $consigment_note_number = $getconsigment_note_number->current_consignment_note_no;
            }

            $gettandc = $this->consignor_copy_terms_and_conditionModel::where('is_active', 1)->first();

            if ($gettandc) {
                $t_and_c = $gettandc->id;
            }

            $consignorcopy = $this->consignor_copyModel::create([ //insert consignorcopy record 
                'consignment_note_no' => $consigment_note_number,
                'loading_date' => $request->loading_date,
                'stuffing_date' => $request->stuffing_date,
                'truck_number' => $request->truck_number,
                'driver_name' => $request->driver_name,
                'licence_number' => $request->licence_number,
                "mobile_number" => $request->mobile_number,
                "from" => $request->from,
                "to" => $request->to,
                "to_2" => $request->to_2,
                "gst_tax_payable_by" => $request->gst_tax_payable_by,
                "consignor_id" => $request->consignor,
                "consignee_id" => $request->consignee,
                "cha" => $request->cha,
                "type" => $request->type,
                "container_no" => $request->container_no,
                "size" => $request->size,
                "shipping_line" => $request->shipping_line,
                "seal_no" => $request->seal_no,
                "be_inv_no" => $request->be_inv_no,
                "port" => $request->port,
                "pod" => $request->pod,
                "service" => $request->service,
                "sac_code" => $request->sac_code,
                "weight_type" => $request->weight,
                "actual" => $request->actual,
                "charged" => $request->charged,
                "value" => $request->value,
                "paid" => $request->paid,
                "to_pay" => $request->pay,
                "reached_at_factory_date" => $request->reached_at_factory_date,
                "reached_at_factory_time" => $request->reached_at_factory_time,
                "left_from_factory_date" => $request->left_from_factory_date,
                "left_from_factory_time" => $request->left_from_factory_time,
                't_and_c_id' => $t_and_c,
                'created_by' => $this->userId
            ]);

            if ($consignorcopy) {

                $getconsigment_note_number->current_consignment_note_no++;
                $getconsigment_note_number->save();

                return $this->successresponse(200, 'message', 'consignor copy succesfully added');
            } else {
                return $this->successresponse(500, 'message', 'consignor copy not succesfully added !');
            }


        }
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $consignorcopy = $this->consignor_copyModel::leftjoin('consignees', 'consignor_copy.consignee_id', 'consignees.id')
            ->leftjoin('consignors', 'consignor_copy.consignor_id', 'consignors.id')
            ->select(
                'consignor_copy.id',
                'consignor_copy.consignment_note_no',
                DB::raw("DATE_FORMAT(consignor_copy.loading_date, '%d-%M-%Y') as loading_date_formatted"),
                DB::raw("DATE_FORMAT(consignor_copy.stuffing_date, '%d-%M-%Y') as stuffing_date_formatted"),
                'consignor_copy.truck_number',
                'consignor_copy.driver_name',
                'consignor_copy.licence_number',
                'consignor_copy.mobile_number',
                'consignor_copy.from',
                'consignor_copy.to',
                'consignor_copy.to_2',
                'consignor_copy.gst_tax_payable_by',
                'consignor_copy.consignee_id',
                'consignor_copy.consignor_id',
                DB::raw("
                    CONCAT_WS(' ', 
                        consignees.firstname, 
                        consignees.lastname 
                    ) as consignee
                "),

                DB::raw("
                    CONCAT_WS(' ', 
                        consignors.firstname, 
                        consignors.lastname 
                    ) as consignor
                "),
                'consignor_copy.cha',
                'consignor_copy.type',
                'consignor_copy.container_no',
                'consignor_copy.size',
                'consignor_copy.shipping_line',
                'consignor_copy.seal_no',
                'consignor_copy.be_inv_no',
                'consignor_copy.port',
                'consignor_copy.pod',
                'consignor_copy.service',
                'consignor_copy.sac_code',
                'consignor_copy.weight_type',
                'consignor_copy.actual',
                'consignor_copy.charged',
                'consignor_copy.value',
                'consignor_copy.paid',
                'consignor_copy.to_pay',
                DB::raw("DATE_FORMAT(consignor_copy.reached_at_factory_date, '%d-%M-%Y') as reached_at_factory_date_formatted"),
                DB::raw("DATE_FORMAT(consignor_copy.reached_at_factory_time, '%h:%i %p') as reached_at_factory_time_formatted"),
                DB::raw("DATE_FORMAT(consignor_copy.left_from_factory_date, '%d-%M-%Y') as left_from_factory_date_formatted"),
                DB::raw("DATE_FORMAT(consignor_copy.left_from_factory_time, '%h:%i %p') as left_from_factory_time_formatted"),
                DB::raw("DATE_FORMAT(consignor_copy.created_at, '%d-%M-%Y %h:%i %p') as created_at_formatted"),
                'consignor_copy.t_and_c_id',
                'consignor_copy.created_by'
            )
            ->where('consignor_copy.is_deleted', 0);

        if ($this->rp['logisticmodule']['consignorcopy']['alldata'] != 1) {
            $consignorcopy->where('consignor_copy.created_by', $this->userId);
        }

        $consignorcopy = $consignorcopy->first();

        if (!$consignorcopy) {
            return $this->successresponse(404, 'message', "No Such consignor Found!");
        }

        if ($this->rp['logisticmodule']['consignorcopy']['alldata'] != 1) {
            if ($consignorcopy->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }

        if ($this->rp['logisticmodule']['consignorcopy']['view'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        // get company details
        $companydetails = DB::table('company')->join('company_details', 'company.company_details_id', 'company_details.id')
            ->join('users', 'users.company_id', 'company.id')
            ->join('country', 'company_details.country_id', 'country.id')
            ->join('state', 'company_details.state_id', 'state.id')
            ->join('city', 'company_details.city_id', 'city.id')
            ->where('users.id', $consignorcopy->created_by)
            ->select(
                'company_details.*',
                'country.country_name',
                'state.state_name',
                'state.state_code',
                'city.city_name'
            )
            ->first();



        if (!$companydetails) {
            return $this->successresponse(404, 'message', "Company details not found!");
        }

        // get t and c
        $t_and_c = null;

        if (isset($consignorcopy->t_and_c_id)) {
            $t_and_c = $this->consignor_copy_terms_and_conditionModel::where('id', $consignorcopy->t_and_c_id)
                ->pluck('t_and_c');
        }

        // get consignor 
        $consignor = $this->consignorModel::join($this->masterdbname . '.city', 'consignors.city_id', $this->masterdbname . '.city.id')
            ->where('consignors.id', $consignorcopy->consignor_id)
            ->select(
                'consignors.*',
                'city.city_name'
            )
            ->first();

        // get consignee 
        $consignee = $this->consigneeModel::join($this->masterdbname . '.city', 'consignees.city_id', $this->masterdbname . '.city.id')
            ->where('consignees.id', $consignorcopy->consignee_id)->select(
                'consignees.*',
                'city.city_name'
            )
            ->first();

        $data = [
            'consignorcopy' => $consignorcopy,
            'companydetails' => $companydetails,
            't_and_c' => $t_and_c,
            'consignee' => $consignee,
            'consignor' => $consignor
        ];

        return $this->successresponse(200, 'data', $data);


    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $consignorcopy = $this->consignor_copyModel::find($id);

        if (!$consignorcopy) {
            return $this->successresponse(404, 'message', "No such consignor copy found!");
        }

        if ($this->rp['logisticmodule']['consignorcopy']['alldata'] != 1) {
            if ($consignorcopy->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }

        if ($this->rp['logisticmodule']['consignorcopy']['edit'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }


        return $this->successresponse(200, 'consignorcopy', $consignorcopy);


    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

        $validator = Validator::make($request->all(), [
            'loading_date' => 'required|date',
            'stuffing_date' => 'required|date',
            'truck_number' => 'nullable|string',
            'driver_name' => 'nullable|string',
            'licence_number' => 'nullable|string',
            "mobile_number" => "nullable|numeric",
            "from" => "required|string",
            "to" => "required|string",
            "to_2" => "nullable|string",
            "gst_tax_payable_by" => "nullable|string",
            "consignor" => "required|numeric",
            "consignee" => "required|numeric",
            "cha" => "nullable|string",
            "weight" => "nullable|string",
            "type" => "required|string",
            "container_no" => "required|string",
            "size" => "nullable|string",
            "shipping_line" => "nullable|string",
            "seal_no" => "nullable|string",
            "be_inv_no" => "nullable|string",
            "port" => "nullable|string",
            "pod" => "nullable|string",
            "service" => "nullable|string",
            "sac_code" => "nullable|string",
            "actual" => "nullable|numeric",
            "charged" => "nullable|numeric",
            "paid" => "nullable|numeric",
            "pay" => "nullable|numeric",
            "value" => "nullable|numeric",
            "reached_at_factory_date" => "nullable|date",
            "reached_at_factory_time" => "nullable|date_format:H:i",
            "left_from_factory_date" => "nullable|date",
            "left_from_factory_time" => "nullable|date_format:H:i",
        ]);

        if ($validator->fails()) {
            return $this->errorresponse(422, $validator->messages());
        } else {

            $consignorcopy = $this->consignor_copyModel::find($id); // find consignor record

            if (!$consignorcopy) {
                return $this->successresponse(404, 'message', 'No such consignor copy found!');
            }

            if ($this->rp['logisticmodule']['consignorcopy']['alldata'] != 1) {
                if ($consignorcopy->created_by != $this->userId) {
                    return $this->successresponse(500, 'message', 'You are unauthorized');
                }
            }

            if ($this->rp['logisticmodule']['consignor']['edit'] != 1) {
                return $this->successresponse(500, 'message', 'You are unauthorized');

            }



            $consignorcopy->update([  // update consignor data
                'loading_date' => $request->loading_date,
                'stuffing_date' => $request->stuffing_date,
                'truck_number' => $request->truck_number,
                'driver_name' => $request->driver_name,
                'licence_number' => $request->licence_number,
                "mobile_number" => $request->mobile_number,
                "from" => $request->from,
                "to" => $request->to,
                "to_2" => $request->to_2,
                "gst_tax_payable_by" => $request->gst_tax_payable_by,
                "consignor_id" => $request->consignor,
                "consignee_id" => $request->consignee,
                "cha" => $request->cha,
                "type" => $request->type,
                "container_no" => $request->container_no,
                "size" => $request->size,
                "shipping_line" => $request->shipping_line,
                "seal_no" => $request->seal_no,
                "be_inv_no" => $request->be_inv_no,
                "port" => $request->port,
                "pod" => $request->pod,
                "service" => $request->service,
                "sac_code" => $request->sac_code,
                "weight_type" => $request->weight,
                "actual" => $request->actual,
                "charged" => $request->charged,
                "value" => $request->value,
                "paid" => $request->paid,
                "to_pay" => $request->pay,
                "reached_at_factory_date" => $request->reached_at_factory_date,
                "reached_at_factory_time" => $request->reached_at_factory_time,
                "left_from_factory_date" => $request->left_from_factory_date,
                "left_from_factory_time" => $request->left_from_factory_time,
                'updated_by' => $this->userId,
            ]);

            return $this->successresponse(200, 'message', 'consignor copy succesfully updated');


        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $consginorcopy = $this->consignor_copyModel::find($id);

        $getconsigment_note_number = $this->logistic_settingModel::first();

        if (!$consginorcopy) {
            return $this->successresponse(404, 'message', 'No such consignor copy found!');
        }

        if ($this->rp['logisticmodule']['consignorcopy']['alldata'] != 1) {
            if ($consginorcopy->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }
        if ($this->rp['logisticmodule']['consignorcopy']['delete'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        // Check if it's the latest record (by consignment number)
        $latestCopy = $this->consignor_copyModel::where('is_deleted', 0)->orderBy('consignment_note_no', 'desc')->first();

        if ($latestCopy && $latestCopy->id == $consginorcopy->id) {
            // Decrement the setting value if this is the latest record
            if ($getconsigment_note_number->current_consignment_note_no > 1) {
                $getconsigment_note_number->current_consignment_note_no--;
                $getconsigment_note_number->save();
            }
        }

        $consginorcopy->update([
            'is_deleted' => 1
        ]);


        return $this->successresponse(200, 'message', 'Consignor copy succesfully deleted');
    }


    /**
     * update t and c the specified resource.
     */
    public function updatetandc(string $id)
    {
        $consginorcopy = $this->consignor_copyModel::find($id);

        $t_and_c = $this->consignor_copy_terms_and_conditionModel::where('is_active', 1)
            ->first('id');

        if (!$consginorcopy) {
            return $this->successresponse(404, 'message', 'No such consignor copy found!');
        }

        if ($this->rp['logisticmodule']['consignorcopy']['alldata'] != 1) {
            if ($consginorcopy->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }
        if ($this->rp['logisticmodule']['consignorcopy']['delete'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }


        if (!$t_and_c) {
            return $this->successresponse(500, 'message', 'No terms and conditions found');
        }

        $consginorcopy->update([
            't_and_c_id' => $t_and_c->id
        ]);


        return $this->successresponse(200, 'message', 'Terms and conditions successfully updated');
    }


}
