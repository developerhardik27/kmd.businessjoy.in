<?php

namespace App\Http\Controllers\v4_3_2\api;

use App\Models\company;
use App\Models\company_detail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class logisticothersettingsController extends commonController
{
    public $userId, $companyId, $masterdbname, $rp, $logistic_settingModel, $consignor_copy_terms_and_conditionModel, $consignor_copyModel;

    public function __construct(Request $request)
    {
        $this->companyId = $request->company_id;
        $this->userId = $request->user_id;

        $this->dbname($this->companyId);
        // **** for checking user has permission to action on all data 
        $user_rp = DB::connection('dynamic_connection')->table('user_permissions')->where('user_id', $this->userId)->value('rp');

        if (empty($user_rp)) {
            $this->customerrorresponse();
        }

        $this->rp = json_decode($user_rp, true);

        $this->masterdbname = DB::connection()->getDatabaseName();

        $this->consignor_copyModel = $this->getmodel('consignor_copy');
        $this->logistic_settingModel = $this->getmodel('logistic_setting');
        $this->consignor_copy_terms_and_conditionModel = $this->getmodel('consignor_copy_terms_and_condition');
    }

    public function getlogisticothersettings(Request $request)
    {
        $logisticsettings = $this->logistic_settingModel::where('is_deleted', 0)->first();

        if (!$logisticsettings) {
            return $this->successresponse(404, 'logisticsettings', 'No Records Found');
        }

        // Check if it's the latest record (by consignment number)
        $lrCount = $this->consignor_copyModel::where('is_deleted', 0)->count();

        return $this->successresponse(200, 'logisticsettings', $logisticsettings, 'lrcount', $lrCount);
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


    public function getwatermark(Request $request)
    {

        if ($this->rp['logisticmodule']['watermark']['add'] != 1 && $this->rp['logisticmodule']['watermark']['edit'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $company = company::find($this->companyId);

        if (!$company) {
            return $this->successresponse(500, 'message', 'No Record Found');
        }
        config([
            'database.connections.dynamic_connection.database' => $company->dbname
        ]);

        DB::purge('dynamic_connection');
        DB::reconnect('dynamic_connection');


        $companyDetails = company_detail::where('id', $company->company_details_id)
            ->select('watermark_img', 'god_names')
            ->first();

        if (!$companyDetails) {
            return $this->successresponse(404, 'watermarksettings', 'No Records Found');
        }

        return $this->successresponse(200, 'watermarksettings', [
            'watermark_img' => $companyDetails->watermark_img,
            'god_names'     => $companyDetails->god_names,
        ]);
    }


    public function updatewatermark(Request $request)
    {
        //condition for check if user has permission to edit  record
        if ($this->rp['logisticmodule']['watermark']['add'] != 1 && $this->rp['logisticmodule']['watermark']['edit'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $validator = Validator::make($request->all(), [
            'watermark_image' => 'nullable|image|mimes:jpg,jpeg,png|max:1024',
        ]);

        if ($validator->fails()) {
            return $this->errorresponse(422, $validator->messages());
        } else {

            $imageName = null;

            $company = company::find($this->companyId);

            if (!$company) {
                return $this->successresponse(500, 'message', 'No such reocrd Found');
            }

            $watermarkImage = company_detail::find($company->company_details_id);

            if (!$watermarkImage) {
                return $this->successresponse(404, 'message', 'No such record found!');
            }

            if (($request->hasFile('watermark_image') && $request->file('watermark_image') != null)) {

                $image = $request->file('watermark_image');
                $dirPath = public_path('uploads/') . $this->companyId . '/';
                // Check if image file is uploaded
                if ($image) {
                    $imageName = 'watermark_' . $watermarkImage->name . $request->name . time() . '.' . $image->getClientOriginalExtension();
                    $image->move($dirPath, $imageName); // upload image
                    $imageName = $this->companyId . '/' . $imageName;
                }
            }

            $updateimg = $watermarkImage->update([
                'watermark_img' => $imageName,
            ]);

            if (!$updateimg) {
                return $this->successresponse(500, 'message', 'Settings not succesfully updated', 'watermarksettings', null);
            }

            return $this->successresponse(200, 'message', 'Settings succesfully updated', 'watermarksettings', $imageName);
        }
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

            // Check if it's the latest record (by consignment number)
            $latestCopy = $this->consignor_copyModel::where('is_deleted', 0)->orderBy('consignment_note_no', 'desc')->first();

            if ($latestCopy) {
                if ($logisticsetting->current_consignment_note_no > $request->consignment_note_number) {
                    return $this->successresponse(500, 'message', 'Please enter consignment note number higher than or equal to ' . $logisticsetting->current_consignment_note_no);
                }
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
            'gst_tax_payable_by' => 'nullable|string',
            'weight' => 'nullable|string',
            'authorized_signatory' => 'nullable|string',
            'god_name' => 'required|numeric',
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
                'updated_by' => $this->userId,
                'god_name_show/hide' => $request->god_name,
            ]);

            return $this->successresponse(200, 'message', 'Settings succesfully updated');
        }
    }

    /**
     * Summary of consignmentnotenumberstore
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function customerdropdown(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_type' => 'required|array',
            'customer_type.*' => 'string',
            'user_id' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return $this->errorresponse(422, $validator->messages());
        }

        // Permission check
        if ($this->rp['logisticmodule']['logisticothersettings']['edit'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $getsettings = $this->logistic_settingModel::find(1);

        if (!$getsettings) {
            return $this->successresponse(404, 'message', 'No Such Customer Dropdown Setting Found!');
        }

        date_default_timezone_set('Asia/Kolkata');
        // dd($request->customer_type);
        $getsettings->update([
            // store as JSON
            'customer_dropdown' => json_encode($request->customer_type),
            'updated_by' => $this->userId,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return $this->successresponse(200, 'message', 'Customer dropdown settings successfully updated');
    }

    public function downloadcopysettingstore(Request $request)
    {
        //condition for check if user has permission to edit  record
        if ($this->rp['logisticmodule']['downloadcopysetting']['edit'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $validator = Validator::make($request->all(), [
            'download_copy' => 'required|array',
        ]);

        if ($validator->fails()) {
            return $this->errorresponse(422, $validator->messages());
        } else {


            $logisticsetting = $this->logistic_settingModel::find(1);

            if (!$logisticsetting) {
                return $this->successresponse(404, 'message', 'No such setting found!');
            }

            $logisticsetting->update([
                'download_copy' => implode(',', $request->download_copy),
                'updated_by' => $this->userId
            ]);

            return $this->successresponse(200, 'message', 'Settings succesfully updated');
        }
    }
}
