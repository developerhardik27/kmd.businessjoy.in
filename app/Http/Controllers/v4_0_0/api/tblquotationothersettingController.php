<?php

namespace App\Http\Controllers\v4_0_0\api;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class tblquotationothersettingController extends commonController
{
    public $userId, $companyId, $masterdbname, $rp, $quotation_other_settingModel, $quotation_terms_and_conditionModel, $quotation_number_patternModel;

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

        $this->quotation_other_settingModel = $this->getmodel('quotation_other_setting');
        $this->quotation_terms_and_conditionModel = $this->getmodel('quotation_terms_and_condition');
        $this->quotation_number_patternModel = $this->getmodel('quotation_number_pattern');
    }

    public function getoverduedays(Request $request)
    {
        $overdueday = $this->quotation_other_settingModel::where('is_deleted', 0)->get();

        if ($overdueday->isEmpty()) {
            return $this->successresponse(404, 'overdueday', 'No records found');
        }
        return $this->successresponse(200, 'overdueday', $overdueday);
    }

    public function quotationnumberpatternindex()
    {
        $pattern = $this->quotation_number_patternModel::where('is_deleted', 0)
        ->select('quotation_pattern', 'pattern_type', 'start_increment_number', 'increment_type')
        ->get();
        $customer_id = $this->quotation_other_settingModel::where('is_deleted', 0)
        ->select('customer_id')
        ->get();
 
        if ($pattern->isEmpty()) {
            return $this->successresponse(404, 'pattern', 'No records found');
        }
            return $this->successresponse(200, 'pattern', [$pattern, $customer_id[0]]);
    }

    public function termsandconditionsindex(Request $request)
    {

        $termsandcondition = $this->quotation_terms_and_conditionModel::where('is_deleted', 0)->get();

        if ($termsandcondition->isEmpty()) {
            return $this->successresponse(404, 'termsandconditions', 'No records found');
        }
            return $this->successresponse(200, 'termsandconditions', $termsandcondition);
    }
    
    public function overduedayupdate(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'expired_day' => 'required|string',
            'year_start_date' => 'required|date',
            'user_id' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return $this->errorresponse(422, $validator->messages());
        } else {

            //condition for check if user has permission to search  record
            if ($this->rp['quotationmodule']['quotationstandardsetting']['edit'] != 1) {
                return $this->successresponse(500, 'message', 'You are unauthorized');
            }

            $overdueday = $this->quotation_other_settingModel::find($id);

            if (!$overdueday) { 
                return $this->successresponse(404, 'message', 'No such Expired days found!');
            }
            date_default_timezone_set('Asia/Kolkata');
            $overdueday->update([
                'overdue_day' => $request->expired_day,
                'year_start' => $request->year_start_date,
                'updated_by' => $this->userId,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            return $this->successresponse(200, 'message', 'Expired days succesfully updated');
        }
    }

    public function gstsettingsupdate(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'sgst' => 'nullable|numeric',
            'cgst' => 'nullable|numeric',
            'gst' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return $this->errorresponse(422, $validator->messages());
        } else {

            $gst = 0;
            if (isset($request->gst)) {
                $gst = $request->gst;
            }
            //condition for check if user has permission to search  record
            if ($this->rp['quotationmodule']['quotationgstsetting']['edit'] != 1) {
                return $this->successresponse(500, 'message', 'You are unauthorized');
            }

            $overdueday = $this->quotation_other_settingModel::find($id);

            if (!$overdueday) { 
                return $this->successresponse(404, 'message', 'No such GST setting Found!');
            }
            date_default_timezone_set('Asia/Kolkata');
            $overdueday->sgst = $request->sgst;
            $overdueday->cgst = $request->cgst;
            $overdueday->gst = $gst;
            $overdueday->updated_by = $this->userId;
            $overdueday->updated_at = date('Y-m-d H:i:s');
            $overdueday->save();

            return $this->successresponse(200, 'message', 'GST settings succesfully updated');
        }
    }

    public function quotationtcstore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            't_and_c' => 'required|string',
            'company_id' => 'required|numeric',
            'user_id' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return $this->errorresponse(422, $validator->messages());
        } else {

            if ($this->rp['quotationmodule']['quotationtandcsetting']['add'] != 1) {
                return $this->successresponse(500, 'message', 'You are unauthorized');
            }

            $all_old_t_and_c = $this->quotation_terms_and_conditionModel::query()->update([
                'is_actove' => 0
            ]);

            

            $t_and_c = $this->quotation_terms_and_conditionModel::create([
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
        if ($this->rp['quotationmodule']['quotationtandcsetting']['edit'] != 1) {
            return $this->successresponse(500, 'message', 'You are unauthorized');
        }

        $termsandcondition = $this->quotation_terms_and_conditionModel::find($id);

        if (!$termsandcondition) {
            return $this->successresponse(404, 'message', "No such terms and conditions found!");
        }
        if ($this->rp['quotationmodule']['quotationtandcsetting']['alldata'] != 1) {
            if ($termsandcondition->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are unauthorized');
            }
        }

            return $this->successresponse(200, 'termsandcondition', $termsandcondition);
    }

    public function tcupdate(Request $request, string $id)
    {

        $validator = Validator::make($request->all(), [
            't_and_c' => 'required|string',
            'user_id' => 'required|numeric',

        ]);

        if ($validator->fails()) {
            return $this->errorresponse(422, $validator->messages());
        } else {

            //condition for check if user has permission to search  record
            if ($this->rp['quotationmodule']['quotationtandcsetting']['edit'] != 1) {
                return $this->successresponse(500, 'message', 'You are unauthorized');
            }

            $termsandcondition = $this->quotation_terms_and_conditionModel::find($id);

            if (!$termsandcondition) { 
                return $this->successresponse(404, 'message', 'No such terms and condition found!');
            }
            if ($this->rp['quotationmodule']['quotationtandcsetting']['alldata'] != 1) {
                if ($termsandcondition->created_by != $this->userId) {
                    return $this->successresponse(500, 'message', 'You are unauthorized');
                }
            }

            date_default_timezone_set('Asia/Kolkata');
            $termsandcondition->update([
                't_and_c' => $request->t_and_c,
                'updated_by' => $this->userId,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            return $this->successresponse(200, 'message', 'Terms and Condition succesfully updated');
        }
    }

    public function tcstatusupdate(Request $request, string $id)
    {
        $termsandcondition = $this->quotation_terms_and_conditionModel::find($id);

        if (!$termsandcondition) {

            return $this->successresponse(404, 'message', 'No such terms and condition found!');
        }
        if ($this->rp['quotationmodule']['quotatointandcsetting']['alldata'] != 1) {
            if ($termsandcondition->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are unauthorized');
            }
        }
        $this->quotation_terms_and_conditionModel::where('id', '!=', $id)->update(['is_active' => 0]);

        if ($this->rp['quotationmodule']['quotatointandcsetting']['edit'] != 1) {
            return $this->successresponse(500, 'message', 'You are unauthorized');
        }
        $termsandcondition->update([
            'is_active' => $request->status
        ]);
        return $this->successresponse(200, 'message', 'status succesfully updated');

    }

    public function tcdestroy(Request $request, string $id)
    {

        $termsandcondition = $this->quotation_terms_and_conditionModel::find($id);

        if (!$termsandcondition) { 
            return $this->successresponse(404, 'message', 'No such terms and conditions found!');
        }
        if ($this->rp['quotationmodule']['quotationtandcsetting']['alldata'] != 1) {
            if ($termsandcondition->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are unauthorized');
            }
        }
        if ($this->rp['quotationmodule']['quotationtandcsetting']['delete'] != 1) {
            return $this->successresponse(500, 'message', 'You are unauthorized');
        }
        $termsandcondition->update([
            'is_deleted' => 1
        ]);
        return $this->successresponse(200, 'message', 'Terms And Conditions succesfully deleted');
    }

    public function quotationpatternstore(Request $request)
    {

        $pattern = "";
        $startincrement = '';
        $incrementtype = '';

        foreach ($request->inputs as $input => $index) {
            if ($index["type"] == "ai") {
                $pattern .= "ai";
                $startincrement = $index["value"];
                $incrementtype = 1;
            } else if ($index["type"] == "cidai") {
                $pattern .= "cidai";
                $startincrement = $index["value"];
                $incrementtype = 2;
            } else {
                $pattern .= $index["value"];
            }
        }

        //condition for check if user has permission to edit  record
        if ($this->rp['quotationmodule']['quotationnumbersetting']['edit'] != 1) {
            return $this->successresponse(500, 'message', 'You are unauthorized');
        }


        $sanitizedPattern = preg_replace('/[^A-Za-z0-9]/', '', $pattern);

        $matchingRecords = $this->quotation_number_patternModel::where('quotation_pattern', $pattern)
            ->orderBy('id', 'desc')  // Orders by in descending order
            ->first();

        if ($matchingRecords) {
            if ($matchingRecords->increment_type == 1 && $startincrement < $matchingRecords->current_increment_number) {
                return $this->successresponse(500, 'message', 'A record with a matching quotation pattern already exists.!If you want use this pattern so then you can start increment from' . $matchingRecords->current_increment_number);
            }
            if ($matchingRecords->increment_type == 2 && !isset($request->onconfirm)) {
                return $this->successresponse(1, 'message', 'A record with a matching quotation pattern already exists.!If you want use this pattern so increment will start from old record');
            }
        }


        $this->quotation_number_patternModel::where('pattern_type', $request->pattern_type)
            ->where('is_deleted', 0)
            ->update(['is_deleted' => 1]);

        date_default_timezone_set('Asia/Kolkata');

        if (isset($request->onconfirm)) {
            $this->quotation_number_patternModel::create([
                'quotation_pattern' => $pattern,
                'pattern_type' => $request->pattern_type,
                'increment_type' => $incrementtype,
                'created_at' => date('Y-m-d'),
                'created_by' => $this->userId,
            ]);
            return $this->successresponse(200, 'message', 'Quotation pattern succesfully updated');
        }

        $this->quotation_number_patternModel::create([
            'quotation_pattern' => $pattern,
            'start_increment_number' => $startincrement,
            'current_increment_number' => $startincrement,
            'pattern_type' => $request->pattern_type,
            'increment_type' => $incrementtype,
            'created_at' => date('Y-m-d'),
            'created_by' => $this->userId,
        ]);

        return $this->successresponse(200, 'message', 'Quotation pattern succesfully updated');

    }

    public function manual_quotation_number(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'status' => 'required|string',
            'user_id' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return $this->errorresponse(422, $validator->messages());
        } else {

            //condition for check if user has permission to edit  record
            if ($this->rp['quotationmodule']['quotationnumbersetting']['edit'] != 1) {
                return $this->successresponse(500, 'message', 'You are unauthorized');
            }

            $getsettings = $this->quotation_other_settingModel::find(1);

            if (!$getsettings) { 
                return $this->successresponse(404, 'message', 'No such quotation number setting found!');
            }
            date_default_timezone_set('Asia/Kolkata');
            $getsettings->update([
                'quotation_number' => $request->status,
                'updated_by' => $this->userId,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            return $this->successresponse(200, 'message', 'Quotation number settings succesfully updated');
        }
    }
    public function manual_quotation_date(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'status' => 'required|string',
            'user_id' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return $this->errorresponse(422, $validator->messages());
        } else {

            //condition for check if user has permission to edit  record
            if ($this->rp['quotationmodule']['quotationnumbersetting']['edit'] != 1) {
                return $this->successresponse(500, 'message', 'You are unauthorized');
            }

            $getsettings = $this->quotation_other_settingModel::find(1);

            if (!$getsettings) { 
                return $this->successresponse(404, 'message', 'No such quotation date setting found!');
            }
            date_default_timezone_set('Asia/Kolkata');
            $getsettings->update([
                'quotation_date' => $request->status,
                'updated_by' => $this->userId,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            return $this->successresponse(200, 'message', 'Quotation date settings succesfully updated');
        }
    }

}

