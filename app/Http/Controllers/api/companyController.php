<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Mail\sendmail;
use App\Models\company;
use App\Models\company_detail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class companyController extends Controller
{
  
    public function companydetailspdf($id){
        
        $companydetails =  DB::table('company_details')
        ->join('country','company_details.country_id','=','country.id')
        ->join('state','company_details.state_id','=','state.id')
        ->join('city','company_details.city_id','=','city.id')
        ->select('company_details.name','company_details.email','company_details.contact_no','company_details.address','company_details.gst_no','company_details.pincode','company_details.img','country.country_name','state.state_name','city.city_name')
                         ->where('company_details.id',$id)->get() ;

        if($companydetails->count() > 0){
            return response()->json([
                'status' => 200,
                'companydetails' => $companydetails
            ], 200);
        }else{
            return response()->json([
                'status' => 404,
                'companydetails' => 'No Records Found'
            ], 404);
        }
    }

    public function companyprofile(Request $request)
    {

        $companyId = $request->input('company_id');

        $company = DB::table('company')
            ->join('company_details', 'company.company_details_id', '=', 'company_details.id')
            ->join('country', 'company_details.country_id', '=', 'country.id')
            ->join('state', 'company_details.state_id', '=', 'state.id')
            ->join('city', 'company_details.city_id', '=', 'city.id')
            ->select('company_details.name', 'company_details.email', 'company_details.contact_no', 'company_details.address', 'company_details.gst_no', 'company_details.pincode','company_details.img', 'country.country_name', 'state.state_name', 'city.city_name')
            ->where('company.id', $companyId)   
            ->get();

        if ($company->count() > 0) {
            return response()->json([
                'status' => 200,
                'company' => $company
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'company' => 'No Records Found'
            ], 404);
        }
    }

    /**
     * Display a listing of the resource.
     */

    public function index(Request $request)
    {

        $userId = $request->input('user_id');

        if ($userId == 1) {

            $company = DB::table('company')
                ->join('company_details', 'company.company_details_id', '=', 'company_details.id')
                ->join('country', 'company_details.country_id', '=', 'country.id')
                ->join('state', 'company_details.state_id', '=', 'state.id')
                ->join('city', 'company_details.city_id', '=', 'city.id')
                ->select('company.id', 'company_details.name', 'company_details.email', 'company_details.contact_no', 'company_details.address', 'company_details.gst_no', 'country.country_name', 'state.state_name', 'city.city_name', 'company.created_by', 'company.updated_by', 'company.created_at', 'company.updated_at', 'company.is_active', 'company.is_deleted')
                ->where('company.is_deleted', 0)->where('company.is_active', 1)
                ->get();
        } else {
            $company = DB::table('company')
                ->join('company_details', 'company.company_details_id', '=', 'company_details.id')
                ->join('country', 'company_details.country_id', '=', 'country.id')
                ->join('state', 'company_details.state_id', '=', 'state.id')
                ->join('city', 'company_details.city_id', '=', 'city.id')
                ->select('company.id', 'company_details.name', 'company_details.email', 'company_details.contact_no', 'company_details.address', 'company_details.gst_no', 'country.country_name', 'state.state_name', 'city.city_name', 'company.created_by', 'company.updated_by', 'company.created_at', 'company.updated_at', 'company.is_active', 'company.is_deleted')
                ->where('company.is_deleted', 0)->where('company.is_active', 1)->where('company.id', $userId)
                ->get();
        }



        if ($company->count() > 0) {
            return response()->json([
                'status' => 200,
                'company' => $company
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'company' => 'No Records Found'
            ], 404);
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

            'name' => 'required|string|max:50',
            'email' => 'required|string|max:50',
            'contact_number' => 'required|numeric|digits:10',
            'address' => 'required|string|max:255',
            'gst_number' => 'required|string|max:50',
            'country' => 'required|numeric',
            'state' => 'required|numeric',
            'city' => 'required|numeric',
            'pincode' => 'required|numeric|digits:6',
            'img' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'created_by' => 'required|numeric',
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
            ], 422);
        } else {

            if ($request->hasFile('img') && $request->file('img') != '') {
                $image = $request->file('img');
                $imageName = $request->name . time() . '.' . $image->getClientOriginalExtension();
                if (!file_exists(public_path('uploads'))) {
                    mkdir(public_path('uploads'), 0755, true);
                }
                // Save the image to the uploads directory
                if ($image->move(public_path('uploads'), $imageName)) {

                    $company_details = DB::table('company_details')->insertGetId([
                        'name' => $request->name,
                        'email' => $request->email,
                        'contact_no' => $request->contact_number,
                        'address' => $request->address,
                        'country_id' => $request->country,
                        'state_id' => $request->state,
                        'city_id' => $request->city,
                        'pincode' => $request->pincode,
                        'gst_no' => $request->gst_number,
                        'img' => $imageName,
                        'created_by' => $request->created_by
                    ]);

                    if ($company_details) {
                        $company_details_id = $company_details;
                        $company = DB::table('company')->insertGetId([
                            'company_detail_id' => $company_details_id,
                            'created_by' => $request->created_by,
                        ]);

                        if ($company) {
                            $password = Str::random(8);
                            $hashpassword =  Hash::make($password);
                            $company_id = $company;
                            $user = DB::table('users')->insert([
                                'firstname' => $request->name,
                                'email' => $request->email,
                                'password' => $hashpassword,
                                'role' => 2,
                                'contact_no' => $request->contact_number,
                                'country_id' => $request->country,
                                'state_id' => $request->state,
                                'city_id' => $request->city,
                                'pincode' => $request->pincode,
                                'company_id' => $company_id,
                                'created_by' => $request->created_by,
                            ]);

                            if ($user) {

                                Mail::to($request->email)->send(new sendmail($request->email, $password));

                                return response()->json([
                                    'status' => 200,
                                    'message' => 'succesfully added'
                                ], 200);
                            }
                        } else {
                            $id = $company;
                            $record = company::find($id);
                            $companydetails = company_detail::find($company_details_id);
                            // Check if the record exists
                            if ($record && $companydetails) {
                                // Delete the record
                                $record->delete();
                                $companydetails->delete();
                            }
                            return response()->json([
                                'status' => 500,
                                'message' => 'Oops ! Something Went wrong'
                            ], 500);
                        }
                    } else {
                        return response()->json([
                            'status' => 500,
                            'message' => 'Oops ! Something Went wrong'
                        ], 500);
                    }
                } else {
                    return response()->json([
                        'status' => 500,
                        'message' => 'image not succesfully upload'
                    ]);
                }
            } else {
                $company_details = DB::table('company_details')->insertGetId([
                    'name' => $request->name,
                    'email' => $request->email,
                    'contact_no' => $request->contact_number,
                    'address' => $request->address,
                    'country_id' => $request->country,
                    'state_id' => $request->state,
                    'city_id' => $request->city,
                    'pincode' => $request->pincode,
                    'gst_no' => $request->gst_number,
                ]);

                if ($company_details) {
                    $company_details_id = $company_details;
                    $company = DB::table('company')->insertGetId([
                        'company_details_id' => $company_details_id,
                        'created_by' => $request->created_by,
                    ]);

                    if ($company) {
                        $password = Str::random(8);
                        $hashpassword =  Hash::make($password);
                        $company_id = $company;
                        $user = DB::table('users')->insert([
                            'firstname' => $request->name,
                            'email' => $request->email,
                            'password' => $hashpassword,
                            'role' => 2,
                            'contact_no' => $request->contact_number,
                            'country_id' => $request->country,
                            'state_id' => $request->state,
                            'city_id' => $request->city,
                            'pincode' => $request->pincode,
                            'company_id' => $company_id,
                            'created_by' => $request->created_by,
                        ]);

                        if ($user) {

                            Mail::to($request->email)->send(new sendmail($request->email, $password));

                            return response()->json([
                                'status' => 200,
                                'message' => 'succesfully added'
                            ], 200);
                        }
                    } else {
                        $id = $company;
                        $record = company::find($id);
                        $companydetails = company_detail::find($company_details_id);
                        // Check if the record exists
                        if ($record && $companydetails) {
                            // Delete the record
                            $record->delete();
                            $companydetails->delete();
                        }
                        return response()->json([
                            'status' => 500,
                            'message' => 'Oops ! Something Went wrong'
                        ], 500);
                    }
                } else {
                    return response()->json([
                        'status' => 500,
                        'message' => 'Oops ! Something Went wrong'
                    ], 500);
                }
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {   
        $company = DB::table('company')
            ->join('company_details', 'company.company_details_id', '=', 'company_details.id')
            ->where('company.id', $id)
            ->get();

        if ($company) {
            return response()->json([
                'status' => 200,
                'company' => $company
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'company' => $company,
                'message' => "No Such company Found!"
            ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {  

        $company = DB::table('company')->join('company_details', 'company.company_details_id', '=', 'company_details.id')->where('company.id', $id)->get();
        if ($company) {
            return response()->json([
                'status' => 200,
                'company' => $company
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'company' => $company,
                'message' => "No Such company Found!"
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:50',
            'email' => 'required|string|max:50',
            'contact_number' => 'required|numeric|digits:10',
            'address' => 'required|string|max:255',
            'gst_number' => 'required|string|max:50',
            'country' => 'required|numeric',
            'state' => 'required|numeric',
            'city' => 'required|numeric',
            'pincode' => 'required|numeric|digits:6',
            'img' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'updated_by' => 'numeric',
            'updated_at',
            'is_active',
            'is_deleted'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->messages()
            ], 422);
        } else {


            if ($request->hasFile('img') && $request->hasFile('img') != '') {
                $image = $request->file('img');
                $imageName = $request->name . time() . '.' . $image->getClientOriginalExtension();
               
                // $company = company::join('company_details','company.company_details_id','=','company_details.id')
                //            ->select('company_details.img')->where('company.id',$id)
                //            ->get();
                // if ($company) {
                //     $imagePath = public_path('uploads/' . $company[0]->img); // old img remove
                //     if (is_file($imagePath)) {
                //         unlink($imagePath);
                //     }
                // }

                if ($image->move(public_path('uploads'), $imageName)) {
                    $company_details = DB::table('company_details')->insertGetId([
                        'name' => $request->name,
                        'email' => $request->email,
                        'contact_no' => $request->contact_number,
                        'address' => $request->address,
                        'country_id' => $request->country,
                        'state_id' => $request->state,
                        'city_id' => $request->city,
                        'pincode' => $request->pincode,
                        'gst_no' => $request->gst_number,
                        'img'=>$imageName
                    ]);
                    if ($company_details) {
                        $company_details_id = $company_details;
                        $company = company::find($id);
                        if ($company) {
    
                            $companyupdate =  $company->update([
                                'company_details_id' => $company_details_id,
                                'updated_by' => $request->updated_by,
    
                            ]);
    
                            if ($companyupdate) {
                                return response()->json([
                                    'status' => 200,
                                    'message' => 'company succesfully updated'
                                ]);
                            } else {
                                $company_details = company_detail::find($company_details_id);
                                $company_details->delete();
                                return response()->json([
                                    'status' => 200,
                                    'message' => 'company not succesfully updated'
                                ]);
                            }
                        } else {
                            return response()->json([
                                'status' => 404,
                                'message' => 'No Such company Found!'
                            ]);
                        }
                    } else {
                        return response()->json([
                            'status' => 500,
                            'message' => 'Oops ! Something Went wrong'
                        ], 500);
                    }
                } else {
                    return response()->json([
                        'status' => 500,
                        'message' => 'image not succesfully upload'
                    ]);
                }
            } else {
                $company = company::join('company_details','company.company_details_id','=','company_details.id')
                ->select('company_details.img')->where('company.id',$id)
                ->get();
                $company_details = DB::table('company_details')->insertGetId([
                    'name' => $request->name,
                    'email' => $request->email,
                    'contact_no' => $request->contact_number,
                    'address' => $request->address,
                    'country_id' => $request->country,
                    'state_id' => $request->state,
                    'city_id' => $request->city,
                    'pincode' => $request->pincode,
                    'gst_no' => $request->gst_number,
                    'img'=> $company[0]->img
                ]);
                if ($company_details) {
                    $company_details_id = $company_details;
                    $company = company::find($id);
                    if ($company) {

                        $companyupdate =  $company->update([
                            'company_details_id' => $company_details_id,
                            'updated_by' => $request->updated_by,

                        ]);

                        if ($companyupdate) {
                            return response()->json([
                                'status' => 200,
                                'message' => 'company succesfully updated'
                            ]);
                        } else {
                            $company_details = company_detail::find($company_details_id);
                            $company_details->delete();
                            return response()->json([
                                'status' => 200,
                                'message' => 'company not succesfully updated'
                            ]);
                        }
                    } else {
                        return response()->json([
                            'status' => 404,
                            'message' => 'No Such company Found!'
                        ]);
                    }
                } else {
                    return response()->json([
                        'status' => 500,
                        'message' => 'Oops ! Something Went wrong'
                    ], 500);
                }
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $company = company::find($id);

        if ($company) {
            $company->update([
                'is_deleted' => 1
            ]);
            return response()->json([
                'status' => 200,
                'message' => 'company succesfully deleted'
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'No Such company Found!'
            ], 404);
        }
    }

    // for join tables company to another table
    public function joincompany()
    {
        $company = DB::table('company')
            ->join('company_details', 'company.company_details_id', '=', 'company_details.id')
            ->select('company.id', 'company_details.name')->where('company.is_deleted', 0)->where('company.is_active', 1)
            ->get();

        if ($company->count() > 0) {
            return response()->json([
                'status' => 200,
                'company' => $company
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'company' => 'No Records Found'
            ], 404);
        }
    }
}
