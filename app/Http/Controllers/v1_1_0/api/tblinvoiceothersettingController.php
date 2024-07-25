<?php

namespace App\Http\Controllers\v1_1_0\api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class tblinvoiceothersettingController extends commonController
{
    public $userId, $companyId, $masterdbname, $rp, $invoice_other_settingModel, $invoice_terms_and_conditionModel, $invoice_number_patternModel;

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
        $this->rp = json_decode($permissions[0]['rp'], true);

        $this->invoice_other_settingModel = $this->getmodel('invoice_other_setting');
        $this->invoice_terms_and_conditionModel = $this->getmodel('invoice_terms_and_condition');
        $this->invoice_number_patternModel = $this->getmodel('invoice_number_pattern');
    }

    public function getoverduedays(Request $request)
    {
        //condition for check if user has permission to view record
        // if ($this->rp['invoicemodule']['invoicesetting']['view'] != 1) {
        //     return $this->successresponse(500, 'message', 'You are Unauthorized');
        // }


        $overdueday = $this->invoice_other_settingModel::where('is_deleted', 0)->get();


        if ($overdueday->count() > 0) {
            return $this->successresponse(200, 'overdueday', $overdueday);
        } else {
            return $this->successresponse(404, 'overdueday', 'No Records Found');
        }
    }
    public function invoicenumberpatternindex()
    {
        //condition for check if user has permission to view record
        // if ($this->rp['invoicemodule']['invoicesetting']['view'] != 1) {
        //     return $this->successresponse(500, 'message', 'You are Unauthorized');
        // }


        $pattern = $this->invoice_number_patternModel::where('is_deleted', 0)->select('invoice_pattern', 'pattern_type','start_increment_number','increment_type')->get();
        $customer_id = $this->invoice_other_settingModel::where('is_deleted',0)->select('customer_id')->get();
       
        if ($pattern->count() > 0) {
            return $this->successresponse(200, 'pattern', [$pattern,$customer_id[0]]);
        } else {
            return $this->successresponse(404, 'pattern', 'No Records Found');
        }
    }

    public function termsandconditionsindex(Request $request)
    {
        //condition for check if user has permission to view record
        // if ($this->rp['invoicemodule']['invoicesetting']['view'] != 1) {
        //     return $this->successresponse(500, 'message', 'You are Unauthorized');
        // }


        $termsandcondition = $this->invoice_terms_and_conditionModel::where('is_deleted', 0)->get();

        // if ($this->rp['invoicemodule']['invoicesettings']['alldata'] != 1) {
        //     $termsandcondition->where('created_by', $this->userId);
        // }

        if ($termsandcondition->count() > 0) {
            return $this->successresponse(200, 'termsandconditions', $termsandcondition);
        } else {
            return $this->successresponse(404, 'termsandconditions', 'No Records Found');
        }
    }
    public function overduedayupdate(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'overdue_day' => 'required|string',
            'year_start_date' => 'required|date',
            'user_id' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return $this->errorresponse(422, $validator->messages());
        } else {

            //condition for check if user has permission to search  record
            if ($this->rp['invoicemodule']['invoicesetting']['edit'] != 1) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }

            $overdueday = $this->invoice_other_settingModel::find($id);

            if ($this->rp['invoicemodule']['invoicesetting']['alldata'] != 1) {
                if ($overdueday->created_by != $this->userId) {
                    return $this->successresponse(500, 'message', 'You are Unauthorized');
                }
            }

            if ($overdueday) {
                date_default_timezone_set('Asia/Kolkata');
                $overdueday->update([
                    'overdue_day' => $request->overdue_day,
                    'year_start' => $request->year_start_date,
                    'updated_by' => $this->userId,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

                return $this->successresponse(200, 'message', 'Overdue days succesfully updated');
            } else {
                return $this->successresponse(404, 'message', 'No Such Overdue days Found!');
            }
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

            if (isset($request->gst)) {
                $gst = $request->gst;
            } else {
                $gst = 0;
            }
            //condition for check if user has permission to search  record
            if ($this->rp['invoicemodule']['invoicesetting']['edit'] != 1) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }

            $overdueday = $this->invoice_other_settingModel::find($id);

            if ($this->rp['invoicemodule']['invoicesetting']['alldata'] != 1) {
                if ($overdueday->created_by != $this->userId) {
                    return $this->successresponse(500, 'message', 'You are Unauthorized');
                }
            }

            if ($overdueday) {
                date_default_timezone_set('Asia/Kolkata');

                $overdueday->sgst = $request->sgst;
                $overdueday->cgst = $request->cgst;
                $overdueday->gst = $gst;
                $overdueday->updated_by = $this->userId;
                $overdueday->updated_at = date('Y-m-d H:i:s');
                $overdueday->save();

                return $this->successresponse(200, 'message', 'GST ettings succesfully updated');
            } else {
                return $this->successresponse(404, 'message', 'No Such GST Settig Found!');
            }
        }
    }

    public function invoicetcstore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            't_and_c' => 'required|string',
            'company_id' => 'required|numeric',
            'user_id' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return $this->errorresponse(422, $validator->messages());
        } else {

            if ($this->rp['invoicemodule']['invoicesetting']['add'] != 1) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }

            $all_old_t_and_c = $this->invoice_terms_and_conditionModel::all();

            if ($all_old_t_and_c->count() > 0) {
                foreach ($all_old_t_and_c as $old_t_and_c) {
                    $old_t_and_c->is_active = 0;
                    $old_t_and_c->save();
                }
            }

            $t_and_c = $this->invoice_terms_and_conditionModel::create([
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
        if ($this->rp['invoicemodule']['invoicesetting']['edit'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $termsandcondition = $this->invoice_terms_and_conditionModel::find($id);

        if ($this->rp['invoicemodule']['invoicesetting']['alldata'] != 1) {
            if ($termsandcondition->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }

        if ($termsandcondition->count() > 0) {
            return $this->successresponse(200, 'termsandcondition', $termsandcondition);
        } else {
            return $this->successresponse(404, 'message', "No Such Terms and Conditions Found!");
        }
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
            if ($this->rp['invoicemodule']['invoicesetting']['edit'] != 1) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }

            $termsandcondition = $this->invoice_terms_and_conditionModel::find($id);

            if ($this->rp['invoicemodule']['invoicesetting']['alldata'] != 1) {
                if ($termsandcondition->created_by != $this->userId) {
                    return $this->successresponse(500, 'message', 'You are Unauthorized');
                }
            }

            if ($termsandcondition) {
                date_default_timezone_set('Asia/Kolkata');
                $termsandcondition->update([
                    't_and_c' => $request->t_and_c,
                    'updated_by' => $this->userId,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

                return $this->successresponse(200, 'message', 'Terms and Condition succesfully updated');
            } else {
                return $this->successresponse(404, 'message', 'No Such Terms and Condition Found!');
            }
        }
    }

    public function tcstatusupdate(Request $request, string $id)
    {
        $termsandcondition = $this->invoice_terms_and_conditionModel::find($id);

        if ($this->rp['invoicemodule']['invoicesetting']['alldata'] != 1) {
            if ($termsandcondition->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }
        if ($termsandcondition) {

            $this->invoice_terms_and_conditionModel::where('id', '!=', $id)->update(['is_active' => 0]);

            if ($this->rp['invoicemodule']['invoicesetting']['edit'] != 1) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
            $termsandcondition->update([
                'is_active' => $request->status
            ]);
            return $this->successresponse(200, 'message', 'status succesfully updated');
        } else {
            return $this->successresponse(404, 'message', 'No Such Terms And Condition Found!');
        }

    }

    public function tcdestroy(Request $request, string $id)
    {

        $termsandcondition = $this->invoice_terms_and_conditionModel::find($id);

        if ($this->rp['invoicemodule']['invoicesetting']['alldata'] != 1) {
            if ($termsandcondition->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }
        if ($termsandcondition) {
            if ($this->rp['invoicemodule']['invoicesetting']['delete'] != 1) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
            $termsandcondition->update([
                'is_deleted' => 1
            ]);
            return $this->successresponse(200, 'message', 'Terms And Conditions succesfully deleted');
        } else {
            return $this->successresponse(404, 'message', 'No Such Terms And Conditions Found!');
        }
    }

    public function invoicepatternstore(Request $request)
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
        if ($this->rp['invoicemodule']['invoicesetting']['edit'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }


        $sanitizedPattern = preg_replace('/[^A-Za-z0-9]/', '', $pattern);

        $matchingRecords = $this->invoice_number_patternModel::where('invoice_pattern', $pattern)
            ->orderBy('id', 'desc')  // Orders by in descending order
            ->first();

        if ($matchingRecords) {
            if ($matchingRecords->increment_type == 1 && $startincrement < $matchingRecords->current_increment_number) {
                return $this->successresponse(500, 'message', 'A record with a matching invoice pattern already exists.!If you want use this pattern so then you can start increment from' . $matchingRecords->current_increment_number);
            }
            if ($matchingRecords->increment_type == 2 && !isset($request->onconfirm)) {
                return $this->successresponse(1, 'message', 'A record with a matching invoice pattern already exists.!If you want use this pattern so increment will start from old record');
            }
        }


        $this->invoice_number_patternModel::where('pattern_type', $request->pattern_type)
            ->where('is_deleted', 0)
            ->update(['is_deleted' => 1]);

        date_default_timezone_set('Asia/Kolkata');

        if (isset($request->onconfirm)) {
            $this->invoice_number_patternModel::create([
                'invoice_pattern' => $pattern,
                'pattern_type' => $request->pattern_type,
                'increment_type' => $incrementtype,
                'created_at' => date('Y-m-d'),
                'created_by' => $this->userId,
            ]);
            return $this->successresponse(200, 'message', 'Inovice Pattern succesfully updated');
        }

        $this->invoice_number_patternModel::create([
            'invoice_pattern' => $pattern,
            'start_increment_number' => $startincrement,
            'current_increment_number' => $startincrement,
            'pattern_type' => $request->pattern_type,
            'increment_type' => $incrementtype,
            'created_at' => date('Y-m-d'),
            'created_by' => $this->userId,
        ]);

        return $this->successresponse(200, 'message', 'Inovice Pattern succesfully updated');

    }

    public function customeridstore(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'customer_id' => 'required|string',
            'user_id' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return $this->errorresponse(422, $validator->messages());
        } else {

            $customerid = $this->invoice_other_settingModel::find(1);

            if ($customerid) {

                if ($customerid->current_customer_id >= $request->customer_id) {
                    return $this->successresponse(500, 'message', 'Please enter  customer id higher than ' . $customerid->current_customer_id);
                }
                date_default_timezone_set('Asia/Kolkata');
                $customerid->update([
                    'customer_id' => $request->customer_id,
                    'current_customer_id' => $request->customer_id,
                    'updated_by' => $this->userId,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

                return $this->successresponse(200, 'message', 'Customer id settings succesfully updated');
            } else {
                return $this->successresponse(404, 'message', 'No Such Customer Setting Found!');
            }
        }
    }

}
