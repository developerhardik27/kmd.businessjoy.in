<?php

namespace App\Http\Controllers\v4_3_2\api;

use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class HrController extends commonController
{
    public $userId, $companyId, $masterdbname, $rp, $employeeModel, $companiesholidayModel, $letterModel;

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
        $this->employeeModel = $this->getmodel('employee');
        $this->companiesholidayModel = $this->getmodel('companiesholiday');
        $this->letterModel = $this->getmodel('letter');
    }
    public function index()
    {
        if ($this->rp['hrmodule']['employees']['view'] != 1) {
            return response()->json([
                'status' => 500,
                'message' => 'You are Unauthorized',
                'data' => [],
                'recordsTotal' => 0,
                'recordsFiltered' => 0
            ]);
        }
        $employees = $this->employeeModel::where("is_deleted", 0)->get();
        return DataTables::of($employees)->make(true);
    }
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'mobile' => 'required|numeric|digits_between:10,15',
            'address' => 'nullable|string',
            'bank_details' => 'nullable|string',
            'cv_resume' => 'nullable|file|mimes:pdf,doc,docx|max:2048',

            'id_proofs' => 'nullable|array',
            'id_proofs.*' => 'file|mimes:jpg,jpeg,png,pdf|max:2048',

            'address_proofs' => 'nullable|array',
            'address_proofs.*' => 'file|mimes:jpg,jpeg,png,pdf|max:2048',

            'other_attachments' => 'nullable|array',
            'other_attachments.*' => 'file|mimes:jpg,jpeg,png,pdf,zip,doc,docx|max:2048',
        ]);
        unset(
            $validatedData['cv_resume'],
            $validatedData['id_proofs'],
            $validatedData['address_proofs'],
            $validatedData['other_attachments']
        );

        $validatedData['created_by'] = $request->user_id;
        $employee = $this->employeeModel::create($validatedData);

        $companyId = session('company_id');
        $id = $employee->id;
        $basePath = "uploads/{$companyId}/hr/{$id}";
        if ($request->hasFile('cv_resume')) {
            $file = $request->file('cv_resume');
            $name = time() . '_' . $file->getClientOriginalName();

            $file->move(public_path($basePath . '/cv_resume'), $name);

            $employee->update([
                'cv_resume' => $basePath . '/cv_resume/' . $name
            ]);
        }

        if ($request->hasFile('id_proofs')) {
            $paths = [];

            foreach ($request->file('id_proofs') as $file) {
                $name = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path($basePath . '/id_proofs'), $name);
                $paths[] = $basePath . '/id_proofs/' . $name;
            }
            $employee->update([
                'id_proofs' => json_encode($paths)
            ]);
        }


        if ($request->hasFile('address_proofs')) {
            $paths = [];
            foreach ($request->file('address_proofs') as $file) {
                $name = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path($basePath . '/address_proofs'), $name);
                $paths[] = $basePath . '/address_proofs/' . $name;
            }
            $employee->update([
                'address_proofs' => json_encode($paths)
            ]);
        }

        if ($request->hasFile('other_attachments')) {
            $paths = [];
            foreach ($request->file('other_attachments') as $file) {
                $name = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path($basePath . '/other_attachments'), $name);
                $paths[] = $basePath . '/other_attachments/' . $name;
            }
            $employee->update([
                'other_attachments' => json_encode($paths)
            ]);
        }
        return response()->json(['status' => 'success', 'message' => 'Employee created successfully', 'data' => $employee], 201);
    }
    public function edit($id)
    {
        $employee = $this->employeeModel::find($id);

        if (!$employee) {
            return response()->json(['status' => 'error', 'message' => 'Employee not found'], 404);
        }

        return response()->json(['status' => 'success', 'data' => $employee], 200);
    }
    public function update(Request $request, $id)
    {
        $employee = $this->employeeModel::find($id);

        if (!$employee) {
            return response()->json([
                'status' => 'error',
                'message' => 'Employee not found'
            ], 404);
        }

        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'surname' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'mobile' => 'required|numeric|digits_between:10,15',
            'address' => 'nullable|string',
            'bank_details' => 'nullable|string',
            'cv_resume' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'id_proofs' => 'nullable|array',
            'id_proofs.*' => 'file|mimes:jpg,jpeg,png,pdf|max:2048',
            'address_proofs' => 'nullable|array',
            'address_proofs.*' => 'file|mimes:jpg,jpeg,png,pdf|max:2048',
            'other_attachments' => 'nullable|array',
            'other_attachments.*' => 'file|mimes:jpg,jpeg,png,pdf,zip,doc,docx|max:2048',
            'remove_id_proofs_preview' => 'nullable|array',
            'remove_address_proofs_preview' => 'nullable|array',
            'remove_other_attachments_preview' => 'nullable|array',
        ]);

        unset(
            $validatedData['cv_resume'],
            $validatedData['id_proofs'],
            $validatedData['address_proofs'],
            $validatedData['other_attachments'],
            $validatedData['remove_id_proofs_preview'],
            $validatedData['remove_address_proofs_preview'],
            $validatedData['remove_other_attachments_preview']
        );

        $id = $employee->id;

        $basePath = "uploads//hr/{$id}";
        $removeFields = [
            'id_proofs' => $request->remove_id_proofs_preview ?? [],
            'address_proofs' => $request->remove_address_proofs_preview ?? [],
            'other_attachments' => $request->remove_other_attachments_preview ?? [],
        ];

        foreach ($removeFields as $field => $files) {

            if (!empty($files)) {

                $existingFiles = json_decode($employee->$field, true) ?? [];
                foreach ($files as $fileToRemove) {

                    $fileToRemove = $basePath . '/' . $field . '/' . $fileToRemove;
                    $key = array_search($fileToRemove, $existingFiles);
                    if ($key !== false) {
                        unset($existingFiles[$key]);
                    }

                    if (file_exists(public_path($fileToRemove))) {
                        @unlink(public_path($fileToRemove)); // delete file
                    }
                }
                $validatedData[$field] = json_encode(array_values($existingFiles));
            }
        }
        if ($request->hasFile('cv_resume')) {
            $cvFile = $request->file('cv_resume');
            $cvName = time() . '_' . $cvFile->getClientOriginalName();
            $cvFile->move(public_path($basePath . '/cv_resume'), $cvName);
            $validatedData['cv_resume'] = $basePath . '/cv_resume/' . $cvName;
        }

        foreach (['id_proofs', 'address_proofs', 'other_attachments'] as $field) {
            if ($request->hasFile($field)) {
                $filesArr = [];
                foreach ($request->file($field) as $file) {
                    $name = time() . '_' . $file->getClientOriginalName();
                    $file->move(public_path($basePath . '/' . $field), $name);
                    $filesArr[] = $basePath . '/' . $field . '/' . $name;
                }
                $existingFiles = json_decode($employee->$field, true) ?? [];
                $validatedData[$field] = json_encode(array_merge($existingFiles, $filesArr));
            }
        }
        $validatedData['updated_by'] = $request->user_id;
        $employee->update($validatedData);

        return response()->json([
            'status' => 'success',
            'message' => 'Employee updated successfully'
        ]);
    }
    public function destroy($id)
    {
        $employee = $this->employeeModel::find($id);
        if (!$employee) {
            return response()->json(['status' => 'error', 'message' => 'Employee not found'], 404);
        }
        $employee->update(
            [
                "is_deleted" => 1
            ]
        );
        return response()->json(['status' => 'success', 'message' => 'Employee deleted successfully'], 200);
    }
    public function holidayindex()
    {
        if ($this->rp['hrmodule']['companiesholidays']['view'] != 1) {
            return response()->json([
                'status' => 500,
                'message' => 'You are Unauthorized',
                'data' => [],
            ]);
        }
        $holiday = $this->companiesholidayModel::where("is_delete", 0)->get();
        return response()->json([
            'status' => 'success',
            'holiday' => $holiday,
        ]);
    }
    public function holidaystore(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'date' => 'required',
            'description' => 'required'
        ]);
        $validatedData['created_by'] = $request->user_id;
        $this->companiesholidayModel::create($validatedData);

        return response()->json([
            'status' => 'success',
            'message' => 'comapnie holiday created successfully '
        ]);
    }

    public function holidayedit($id)
    {
        $hodiday = $this->companiesholidayModel::find($id);

        if (!$hodiday) {
            return response()->json(['status' => 'error', 'message' => 'hodiday not found'], 404);
        }

        return response()->json(['status' => 'success', 'data' => $hodiday], 200);
    }
    public function holidayupdate(Request $request, $id)
    {
        $hodiday = $this->companiesholidayModel::find($id);

        if (!$hodiday) {
            return response()->json(['status' => 'error', 'message' => 'hodiday not found'], 404);
        }
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'date' => 'required',
            'description' => 'required'
        ]);
        $hodiday->update($validatedData);
        return response()->json([
            'status' => 'success',
            'message' => 'hodiday updated successfully'
        ]);
    }
    public function holidaydestroy($id)
    {
        $hodiday = $this->companiesholidayModel::find($id);
        if (!$hodiday) {
            return response()->json(['status' => 'error', 'message' => 'hodiday not found'], 404);
        }
        $hodiday->update(
            [
                "is_delete" => 1
            ]
        );
        return response()->json(['status' => 'success', 'message' => 'hodiday deleted successfully'], 200);
    }
    public function letterindex()
    {
        $letters = $this->letterModel::where("is_delete", 0)->get();
        $totalcount = $this->letterModel::where("is_delete", 0)->get()->count();
        if ($letters->isEmpty()) {
            return DataTables::of($letters)
                ->with([
                    'status' => 404,
                    'message' => 'No Data Found',
                    'recordsTotal' => $totalcount, // Total records count
                ])
                ->make(true);
        }
        return DataTables::of($letters)
            ->with([
                'status' => 200,
                'recordsTotal' => $totalcount, // Total records count
            ])
            ->make(true);
    }
    public function letterstore(Request $request)
    {
        $validatedData = $request->validate([
            'letter_name'     => 'required|string|max:255',
            'header_image'    => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'header_align'    => 'required|string',
            'header_width'    => 'required|integer|min:1|max:100',
            'header_content'  => 'required|string',
            'body_content'    => 'required|string',
            'footer_image'    => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'footer_align'    => 'required|string',
            'footer_width'    => 'required|integer|min:1|max:100',
            'footer_content'  => 'required|string',
        ]);

        $validatedData['created_by'] = $request->user_id;
        $validatedData['updated_by'] = $request->user_id;

        // Create letter first
        $letter = $this->letterModel::create($validatedData);

        // Make folder using the letter ID
        $uploadPath = public_path("uploads/hr/letter/{$letter->id}");
        if (!file_exists($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        // Upload header image
        if ($request->hasFile('header_image')) {
            $headerFile = $request->file('header_image');
            $headerFileName = 'header_image.' . $headerFile->getClientOriginalExtension();
            $headerFile->move($uploadPath, $headerFileName);

            $headerPath = "uploads/hr/letter/{$letter->id}/{$headerFileName}";
            $letter->update(['header_image' => $headerPath]);
        }

        // Upload footer image
        if ($request->hasFile('footer_image')) {
            $footerFile = $request->file('footer_image');
            $footerFileName = 'footer_image.' . $footerFile->getClientOriginalExtension();
            $footerFile->move($uploadPath, $footerFileName);

            $footerPath = "uploads/hr/letter/{$letter->id}/{$footerFileName}";
            $letter->update(['footer_image' => $footerPath]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Letter created successfully!',
            'letter_id' => $letter->id
        ]);
    }
    public function letteredit($id)
    {
        $letter = $this->letterModel::find($id);

        if (!$letter) {
            return response()->json(['status' => 'error', 'message' => 'letter not found'], 404);
        }

        return response()->json(['status' => 'success', 'data' => $letter], 200);
    }
    public function letterupdate(Request $request, $id)
    {
        // Find the letter
        $letter = $this->letterModel::find($id);

        if (!$letter) {
            return response()->json([
                'status' => 'error',
                'message' => 'Letter not found'
            ], 404);
        }

        // Validate input
        $validatedData = $request->validate([
            'letter_name'     => 'required|string|max:255',
            'header_image'    => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'header_align'    => 'required|string',
            'header_width'    => 'required|integer|min:1|max:100',
            'header_content'  => 'required|string',
            'body_content'    => 'required|string',
            'footer_image'    => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'footer_align'    => 'required|string',
            'footer_width'    => 'required|integer|min:1|max:100',
            'footer_content'  => 'required|string',
        ]);

        $validatedData['updated_by'] = $request->user_id;

        $uploadPath = public_path("uploads/hr/letter/{$letter->id}");
        if (!file_exists($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        if ($request->hasFile('header_image')) {
            $headerFile = $request->file('header_image');
            $headerFileName = 'header_image.' . $headerFile->getClientOriginalExtension();
            $headerFile->move($uploadPath, $headerFileName);

            $headerPath = "uploads/hr/letter/{$letter->id}/{$headerFileName}";
            $validatedData['header_image'] = $headerPath;
        }

        if ($request->hasFile('footer_image')) {
            $footerFile = $request->file('footer_image');
            $footerFileName = 'footer_image.' . $footerFile->getClientOriginalExtension();
            $footerFile->move($uploadPath, $footerFileName);

            $footerPath = "uploads/hr/letter/{$letter->id}/{$footerFileName}";
            $validatedData['footer_image'] = $footerPath;
        }
// dd($validatedData);
        $letter->update($validatedData);

        return response()->json([
            'status' => 'success',
            'message' => 'Letter updated successfully!',
            'letter_id' => $letter->id
        ]);
    }

    public function letterdestroy($id)
    {
        $letter = $this->letterModel::find($id);
        if (!$letter) {
            return response()->json(['status' => 'error', 'message' => 'letter not found'], 404);
        }
        $letter->update(
            [
                "is_delete" => 1
            ]
        );
        return response()->json(['status' => 'success', 'message' => 'letter deleted successfully'], 200);
    }
}
