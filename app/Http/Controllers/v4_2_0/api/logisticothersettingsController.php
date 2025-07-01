<?php

namespace App\Http\Controllers\v4_2_0\api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class logisticothersettingsController extends commonController
{
    public $userId, $companyId, $masterdbname, $rp, $logistic_settingModel, $consignor_copy_terms_and_conditionModel;

    public function __construct(Request $request)
    {
        if (session()->get('company_id')) {
            $this->dbname(session()->get('company_id'));
        } else {
            $this->dbname($request->company_id);
        }
        if (session()->get('user_id')) {
            $this->userId = session()->get('user_id');
        } else {
            $this->userId = $request->user_id;
        }
        $this->companyId = $request->company_id;
        $this->masterdbname = DB::connection()->getDatabaseName();

        // **** for checking user has permission to action on all data 
        $user_rp = DB::connection('dynamic_connection')->table('user_permissions')->select('rp')->where('user_id', $this->userId)->get();
        $permissions = json_decode($user_rp, true);
        if(empty($permissions)){
            $this->customerrorresponse();
        }
        $this->rp = json_decode($permissions[0]['rp'], true);

        $this->logistic_settingModel = $this->getmodel('logistic_setting');
        $this->consignor_copy_terms_and_conditionModel = $this->getmodel('consignor_copy_terms_and_condition');
    }

    public function getlogisticothersettings(Request $request)
    {

        $logisticsettings = $this->logistic_settingModel::where('is_deleted', 0)->first();

        if (!$logisticsettings) {
            return $this->successresponse(404, 'logisticsettings', 'No Records Found');
        }

        return $this->successresponse(200, 'logisticsettings', $logisticsettings);
    }

    public function termsandconditionsindex(Request $request)
    {

        $termsandcondition = $this->consignor_copy_terms_and_conditionModel::where('is_deleted', 0)->get();

        if ($termsandcondition->isEmpty()) {
            return $this->successresponse(404, 'termsandconditions', 'No Records Found');
        }
        return $this->successresponse(200, 'termsandconditions', $termsandcondition);
    }

    public function consignorcopytcstore(Request $request)
    {
        if ($this->rp['logisticmodule']['consignorcopytandcsettings']['add'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $validator = Validator::make($request->all(), [
            't_and_c' => 'required|string',
            'company_id' => 'required|numeric',
            'user_id' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return $this->errorresponse(422, $validator->messages());
        } else {
 
            $all_old_t_and_c = $this->consignor_copy_terms_and_conditionModel::query()->update([
                'is_active' => 0
            ]);


            $t_and_c = $this->consignor_copy_terms_and_conditionModel::create([
                't_and_c' => $request->t_and_c,
                'created_by' => $this->userId,
            ]);

            if ($t_and_c) {
                return $this->successresponse(200, 'message', 'Terms & Conditions succesfully added');
            } else {
                return $this->successresponse(500, 'message', 'Terms & Conditions not succesfully added');
            }
        }
    }

    public function tcedit(string $id)
    {

        //condition for check if user has permission to search  record
        if ($this->rp['logisticmodule']['consignorcopytandcsettings']['edit'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $termsandcondition = $this->consignor_copy_terms_and_conditionModel::find($id);

        if (!$termsandcondition) {
            return $this->successresponse(404, 'message', "No Such Terms and Conditions Found!");
        }

        if ($this->rp['logisticmodule']['consignorcopytandcsettings']['alldata'] != 1) {
            if ($termsandcondition->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }

        return $this->successresponse(200, 'termsandcondition', $termsandcondition);
    }

    public function tcupdate(Request $request, string $id)
    {
        //condition for check if user has permission to search  record
        if ($this->rp['logisticmodule']['consignorcopytandcsettings']['edit'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $validator = Validator::make($request->all(), [
            't_and_c' => 'required|string',
            'user_id' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return $this->errorresponse(422, $validator->messages());
        } else {
 
            $termsandcondition = $this->consignor_copy_terms_and_conditionModel::find($id);

            if (!$termsandcondition) {
                return $this->successresponse(404, 'message', 'No Such Terms and Condition Found!');
            }
            if ($this->rp['logisticmodule']['consignorcopytandcsettings']['alldata'] != 1) {
                if ($termsandcondition->created_by != $this->userId) {
                    return $this->successresponse(500, 'message', 'You are Unauthorized');
                }
            }

            $termsandcondition->update([
                't_and_c' => $request->t_and_c,
                'updated_by' => $this->userId,
            ]);

            return $this->successresponse(200, 'message', 'Terms and Condition succesfully updated');
        }
    }

    public function tcstatusupdate(Request $request, string $id)
    {
        if ($this->rp['logisticmodule']['consignorcopytandcsettings']['edit'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $termsandcondition = $this->consignor_copy_terms_and_conditionModel::find($id);

        if (!$termsandcondition) {
            return $this->successresponse(404, 'message', 'No Such Terms And Condition Found!');
        }

        if ($this->rp['logisticmodule']['consignorcopytandcsettings']['alldata'] != 1) {
            if ($termsandcondition->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }

        $this->consignor_copy_terms_and_conditionModel::where('id', '!=', $id)->update(['is_active' => 0]);

        $termsandcondition->update([
            'is_active' => $request->status
        ]);

        return $this->successresponse(200, 'message', 'status succesfully updated');

    }

    public function tcdestroy(Request $request, string $id)
    {

        if ($this->rp['logisticmodule']['consignorcopytandcsettings']['delete'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $termsandcondition = $this->consignor_copy_terms_and_conditionModel::find($id);

        if (!$termsandcondition) {
            return $this->successresponse(404, 'message', 'No Such Terms And Conditions Found!');
        }

        if ($this->rp['logisticmodule']['consignorcopytandcsettings']['alldata'] != 1) {
            if ($termsandcondition->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }
 
        $termsandcondition->update([
            'is_deleted' => 1
        ]);

        return $this->successresponse(200, 'message', 'Terms And Conditions succesfully deleted');
    }

    /**
     * Summary of consignmentnotenumberstore
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function consignmentnotenumberstore(Request $request)
    {

        //condition for check if user has permission to edit  record
        if ($this->rp['logisticmodule']['consignmentnotenumbersettings']['edit'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $validator = Validator::make($request->all(), [
            'consignment_note_number' => 'required|numeric',
            'user_id' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return $this->errorresponse(422, $validator->messages());
        } else {
           

            $logisticsetting = $this->logistic_settingModel::find(1);

            if (!$logisticsetting) {
                return $this->successresponse(404, 'message', 'No such consignment note number setting found!');
            }

            if ($logisticsetting->current_consignment_note_no > $request->consignment_note_number) {
                return $this->successresponse(500, 'message', 'Please enter consignment note number higher than or equal to ' . $logisticsetting->current_consignment_note_no);
            }

            $logisticsetting->update([
                'start_consignment_note_no' => $request->consignment_note_number,
                'current_consignment_note_no' => $request->consignment_note_number,
                'updated_by' => $this->userId
            ]);

            return $this->successresponse(200, 'message', 'Consignment note number settings succesfully updated');
        }
    }

    /**
     * Summary of consignmentnotenumberstore
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function logisticothersettingsstore(Request $request)
    {

        //condition for check if user has permission to edit  record
        if ($this->rp['logisticmodule']['logisticothersettings']['edit'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $validator = Validator::make($request->all(), [
            'gst_tax_payable_by' => 'required|string',
            'weight' => 'required|string',
            'authorized_signatory' => 'required|string'
        ]);

        if ($validator->fails()) {
            return $this->errorresponse(422, $validator->messages());
        } else {
           

            $logisticsetting = $this->logistic_settingModel::find(1);

            if (!$logisticsetting) {
                return $this->successresponse(404, 'message', 'No such setting found!');
            }

            $logisticsetting->update([
                'gst_tax_payable_by' => $request->gst_tax_payable_by,
                'weight' => $request->weight,
                'authorized_signatory' => $request->authorized_signatory,
                'updated_by' => $this->userId
            ]);

            return $this->successresponse(200, 'message', 'Settings succesfully updated');
        }
    }

}

