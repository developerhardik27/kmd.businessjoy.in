<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\user_permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class userController extends Controller
{

    public function username(Request $request)
    {
        $userId = $request->input('user_id');
        $user = DB::table('users')
            ->join('company', 'users.company_id', '=', 'company.id')
            ->join('company_details', 'company.company_details_id', '=', 'company_details.id')
            ->select('users.firstname', 'users.lastname', 'users.img', 'company_details.name')->where('users.id', $userId)->get();
        if ($user->count() > 0) {
            return response()->json([
                'status' => 200,
                'user' => $user
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'user' => 'No Records Found'
            ]);
        }
    }

    public function userprofile(Request $request)
    {

        $userId = $request->input('user_id');
        $users = DB::table('users')
            ->join('country', 'users.country_id', '=', 'country.id')
            ->join('state', 'users.state_id', '=', 'state.id')
            ->join('city', 'users.city_id', '=', 'city.id')
            ->join('company', 'users.company_id', '=', 'company.id')
            ->join('company_details', 'company.company_details_id', '=', 'company_details.id')
            ->select('users.firstname', 'users.lastname', 'users.email', 'users.contact_no', 'country.country_name', 'state.state_name', 'city.city_name', 'users.pincode', 'company_details.name', 'users.img')
            ->where('users.is_active', '1')->where('users.is_deleted', '0')->where('users.id', $userId)
            ->get();



        if ($users->count() > 0) {
            return response()->json([
                'status' => 200,
                'user' => $users
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'user' => 'No Records Found'
            ]);
        }
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $userId = $request->input('user_id');
        if ($userId == 1) {
            $users = DB::table('users')
                ->join('country', 'users.country_id', '=', 'country.id')
                ->join('state', 'users.state_id', '=', 'state.id')
                ->join('city', 'users.city_id', '=', 'city.id')
                ->join('company', 'users.company_id', '=', 'company.id')
                ->join('company_details', 'company.company_details_id', '=', 'company_details.id')
                ->join('user_role', 'users.role', '=', 'user_role.id')
                ->select('users.id', 'users.firstname', 'users.lastname', 'users.email', 'users.password', 'users.contact_no', 'country.country_name', 'state.state_name', 'city.city_name', 'users.pincode', 'company_details.name as company_name', 'user_role.role as user_role', 'users.img', 'users.created_by', 'users.updated_by', 'users.is_active')
                ->where('users.is_deleted', 0)->get();
        } else {
            $users = DB::table('users')
                ->join('country', 'users.country_id', '=', 'country.id')
                ->join('state', 'users.state_id', '=', 'state.id')
                ->join('city', 'users.city_id', '=', 'city.id')
                ->join('company', 'users.company_id', '=', 'company.id')
                ->join('company_details', 'company.company_details_id', '=', 'company_details.id')
                ->join('user_role', 'users.role', '=', 'user_role.id')
                ->select('users.id', 'users.firstname', 'users.lastname', 'users.email', 'users.password', 'users.contact_no', 'country.country_name', 'state.state_name', 'city.city_name', 'users.pincode', 'company_details.name as company_name', 'user_role.role as user_role', 'users.img', 'users.created_by', 'users.updated_by', 'users.is_active')
                ->where('users.is_deleted', 0)->where('users.company_id', $userId)->get();
        }
        if ($users->count() > 0) {
            return response()->json([
                'status' => 200,
                'user' => $users
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'user' => 'No Records Found'
            ]);
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
            'firstname' => 'required|string|max:50',
            'lastname' => 'required|string|max:50',
            'email' => 'required|email|max:50',
            'password' => 'required|string|max:70',
            'contact_number' => 'required|numeric|digits:10',
            'country' => 'required|numeric',
            'state' => 'required|numeric',
            'city' => 'required|numeric',
            'pincode' => 'required|numeric|digits:6',
            'company_id' => 'required|numeric',
            'created_by' => 'required|numeric',
            'img' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
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
            ]);
        } else {

            $rp = [
                "invoicemodule" => [
                    "invoice" => ["show" => $request->showinvoicemenu, "add" => $request->addinvoice, "view" => $request->viewinvoice, "edit" => $request->editinvoice, "delete" => $request->deleteinvoice],
                    "company" => ["show" => $request->showcompanymenu, "add" => $request->addcompany, "view" => $request->viewcompany, "edit" => $request->editcompany, "delete" => $request->deletecompany],
                    "bank" => ["show" => $request->showbankmenu, "add" => $request->addbank, "view" => $request->viewbank, "edit" => $request->editbank, "delete" => $request->deletebank],
                    "user" => ["show" => $request->showusermenu, "add" => $request->adduser, "view" => $request->viewuser, "edit" => $request->edituser, "delete" => $request->deleteuser],
                    "customer" => ["show" => $request->showcustomermenu, "add" => $request->addcustomer, "view" => $request->viewcustomer, "edit" => $request->editcustomer, "delete" => $request->deletecustomer],
                    "product" => ["show" => $request->showproductmenu, "add" => $request->addproduct, "view" => $request->viewproduct, "edit" => $request->editproduct, "delete" => $request->deleteproduct],
                    "purchase" => ["show" => $request->showpurchasemenu, "add" => $request->addpurchase, "view" => $request->viewpurchase, "edit" => $request->editpurchase, "delete" => $request->deletepurchase]
                ],
                "leadmodule" => [
                    "lead" => ["show" => $request->showleadmenu, "add" => $request->addlead, "view" => $request->viewlead, "edit" => $request->editlead, "delete" => $request->deletelead]
                ]
            ];

            $rpjson = json_encode($rp);

            if ($request->hasFile('img') && $request->file('img') != '') {
                $image = $request->file('img');
                $imageName = $request->firstname . time() . '.' . $image->getClientOriginalExtension();
                if (!file_exists(public_path('uploads'))) {
                    mkdir(public_path('uploads'), 0755, true);
                }
                // Save the image to the uploads directory
                if ($image->move(public_path('uploads'), $imageName)) {

                    $users = User::insertGetId([
                        'firstname' => $request->firstname,
                        'lastname' => $request->lastname,
                        'email' => $request->email,
                        'password' => Hash::make($request->password),
                        'contact_no' => $request->contact_number,
                        'country_id' => $request->country,
                        'state_id' => $request->state,
                        'city_id' => $request->city,
                        'pincode' => $request->pincode,
                        'img' => $imageName,
                        'company_id' => $request->company_id,
                        'created_by' => $request->created_by,
                    ]);


                    if ($users) {

                        $userrp = user_permission::create([
                            'user_id' => $users,
                            'rp' => $rpjson
                        ]);

                        if ($userrp) {
                            return response()->json([
                                'status' => 200,
                                'message' => 'user succesfully created'
                            ]);
                        } else {
                            User::where('id', $users)->delete();
                            return response()->json([
                                'status' => 500,
                                'message' => 'User Roles & Permissions not succesfully create'
                            ]);
                        }
                    } else {
                        return response()->json([
                            'status' => 500,
                            'message' => 'user not succesfully create'
                        ]);
                    }
                } else {
                    return response()->json([
                        'status' => 500,
                        'message' => 'image not succesfully upload'
                    ]);
                }
            } else {
                $users = User::insertGetId([
                    'firstname' => $request->firstname,
                    'lastname' => $request->lastname,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'contact_no' => $request->contact_number,
                    'country_id' => $request->country,
                    'state_id' => $request->state,
                    'city_id' => $request->city,
                    'pincode' => $request->pincode,
                    'company_id' => $request->company_id,
                    'created_by' => $request->created_by,
                ]);


                if ($users) {
                    $userrp = user_permission::create([
                        'user_id' => $users,
                        'rp' => $rpjson
                    ]);

                    if ($userrp) {
                        return response()->json([
                            'status' => 200,
                            'message' => 'user succesfully created'
                        ]);
                    } else {
                        User::where('id', $users)->delete();
                        return response()->json([
                            'status' => 500,
                            'message' => 'User Roles & Permissions not succesfully create'
                        ]);
                    }
                } else {
                    return response()->json([
                        'status' => 500,
                        'message' => 'user not succesfully create'
                    ]);
                }
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $users = DB::table('users')
            ->join('user_permissions', 'users.id', '=', 'user_permissions.user_id')
            ->select('users.*', 'user_permissions.rp')
            ->where('users.id', $id)->get();

        if ($users) {
            return response()->json([
                'status' => 200,
                'user' => $users
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => "No Such user Found!"
            ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $users = User::find($id);
        if ($users) {
            return response()->json([
                'status' => 200,
                'user' => $users
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => "No Such user Found!"
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
          

        $validator = Validator::make($request->all(), [
            'firstname' => 'required|string|max:50',
            'lastname' => 'required|string|max:50',
            'email' => 'required|email|max:50',
            'password' => 'nullable|string|max:70',
            'contact_number' => 'required|numeric|digits:10',
            'country' => 'required|numeric',
            'state' => 'required|numeric',
            'city' => 'required|numeric',
            'pincode' => 'required|numeric|digits:6',
            'img' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'created_by',
            'updated_by' => 'required|numeric',
            'created_at',
            'updated_at',
            'is_active',
            'is_deleted'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->messages()
            ]);
        } else {

            $rp = [
                "invoicemodule" => [
                    "invoice" => ["show" => $request->showinvoicemenu, "add" => $request->addinvoice, "view" => $request->viewinvoice, "edit" => $request->editinvoice, "delete" => $request->deleteinvoice],
                    "company" => ["show" => $request->showcompanymenu, "add" => $request->addcompany, "view" => $request->viewcompany, "edit" => $request->editcompany, "delete" => $request->deletecompany],
                    "bank" => ["show" => $request->showbankmenu, "add" => $request->addbank, "view" => $request->viewbank, "edit" => $request->editbank, "delete" => $request->deletebank],
                    "user" => ["show" => $request->showusermenu, "add" => $request->adduser, "view" => $request->viewuser, "edit" => $request->edituser, "delete" => $request->deleteuser],
                    "customer" => ["show" => $request->showcustomermenu, "add" => $request->addcustomer, "view" => $request->viewcustomer, "edit" => $request->editcustomer, "delete" => $request->deletecustomer],
                    "product" => ["show" => $request->showproductmenu, "add" => $request->addproduct, "view" => $request->viewproduct, "edit" => $request->editproduct, "delete" => $request->deleteproduct],
                    "purchase" => ["show" => $request->showpurchasemenu, "add" => $request->addpurchase, "view" => $request->viewpurchase, "edit" => $request->editpurchase, "delete" => $request->deletepurchase]
                ],
                "leadmodule" => [
                    "lead" => ["show" => $request->showleadmenu, "add" => $request->addlead, "view" => $request->viewlead, "edit" => $request->editlead, "delete" => $request->deletelead]
                ]
            ];

            $rpjson = json_encode($rp);
            if ($request->hasFile('img') && $request->hasFile('img') != '') {
                $image = $request->file('img');
                $imageName = $request->firstname . time() . '.' . $image->getClientOriginalExtension();

                // Save the image to the uploads directory
                if ($image->move(public_path('uploads'), $imageName)) {
                    $users = User::find($id);
                    if ($users) {
                        $imagePath = public_path('uploads/' . $users->img);
                        if (is_file($imagePath)) {
                            unlink($imagePath);  // old img remove
                        }
                        if ($request->password == '') {
                            $user =   $users->update([
                                'firstname' => $request->firstname,
                                'lastname' => $request->lastname,
                                'email' => $request->email,
                                'contact_no' => $request->contact_number,
                                'country_id' => $request->country,
                                'state_id' => $request->state,
                                'city_id' => $request->city,
                                'pincode' => $request->pincode,
                                'img' => $imageName,
                                'updated_by' => $request->updated_by,
                                'updated_at' => date('Y-m-d')

                            ]);
                            if ($user) {
                                $searchuserrp = user_permission::where('user_id', $id)->first();
                                if ($searchuserrp) {
                                    $rpupdate =  $searchuserrp->update([
                                        "rp" => $rpjson
                                    ]);

                                    if ($rpupdate) {
                                        return response()->json([
                                            'status' => 200,
                                            'message' => 'user succesfully updated'
                                        ]);
                                    } else {
                                        return response()->json([
                                            'status' => 404,
                                            'message' => 'user role & permissions not succesfully updated!'
                                        ]);
                                    }
                                } else {
                                    return response()->json([
                                        'status' => 404,
                                        'message' => 'No Such roles & permissions Found!'
                                    ]);
                                }
                            } else {
                                return response()->json([
                                    'status' => 404,
                                    'message' => 'user not succesfully updated!'
                                ]);
                            }
                        } else {
                            $user =  $users->update([
                                'firstname' => $request->firstname,
                                'lastname' => $request->lastname,
                                'email' => $request->email,
                                'password' =>  Hash::make($request->password),
                                'contact_no' => $request->contact_number,
                                'country_id' => $request->country,
                                'state_id' => $request->state,
                                'city_id' => $request->city,
                                'pincode' => $request->pincode,
                                'img' => $imageName,
                                'updated_by' => $request->updated_by,
                                'updated_at' => date('Y-m-d')
                            ]);
                            if ($user) {
                                $searchuserrp = user_permission::where('user_id', $id)->first();
                                if ($searchuserrp) {
                                    $rpupdate =  $searchuserrp->update([
                                        "rp" => $rpjson
                                    ]);

                                    if ($rpupdate) {
                                        return response()->json([
                                            'status' => 200,
                                            'message' => 'user succesfully updated'
                                        ]);
                                    } else {
                                        return response()->json([
                                            'status' => 404,
                                            'message' => 'user role & permissions not succesfully updated!'
                                        ]);
                                    }
                                } else {
                                    return response()->json([
                                        'status' => 404,
                                        'message' => 'No Such roles & permissions Found!'
                                    ]);
                                }
                            } else {
                                return response()->json([
                                    'status' => 404,
                                    'message' => 'user not succesfully updated!'
                                ]);
                            }
                        }
                    } else {
                        return response()->json([
                            'status' => 404,
                            'message' => 'No Such user Found!'
                        ]);
                    }
                } else {
                    return response()->json([
                        'status' => 500,
                        'message' => 'image not succesfully upload'
                    ]);
                }
            } else {
                $users = User::find($id);
                if ($users) {
                    if ($request->password == '') {
                        $user =  $users->update([
                            'firstname' => $request->firstname,
                            'lastname' => $request->lastname,
                            'email' => $request->email,
                            'contact_no' => $request->contact_number,
                            'country_id' => $request->country,
                            'state_id' => $request->state,
                            'city_id' => $request->city,
                            'pincode' => $request->pincode,
                            'updated_by' => $request->updated_by,
                            'updated_at' => date('Y-m-d')

                        ]);

                        if ($user) {
                            $searchuserrp = user_permission::where('user_id', $id)->first();
                            if ($searchuserrp) {
                                $rpupdate =  $searchuserrp->update([
                                    "rp" => $rpjson
                                ]);

                                if ($rpupdate) {
                                    return response()->json([
                                        'status' => 200,
                                        'message' => 'user succesfully updated'
                                    ]);
                                } else {
                                    return response()->json([
                                        'status' => 404,
                                        'message' => 'user role & permissions not succesfully updated!'
                                    ]);
                                }
                            } else {
                                return response()->json([
                                    'status' => 404,
                                    'message' => 'No Such roles & permissions  Found!'
                                ]);
                            }
                        } else {
                            return response()->json([
                                'status' => 404,
                                'message' => 'user not succesfully updated!'
                            ]);
                        }
                    } else {
                        $user =  $users->update([
                            'firstname' => $request->firstname,
                            'lastname' => $request->lastname,
                            'email' => $request->email,
                            'password' =>  Hash::make($request->password),
                            'contact_no' => $request->contact_number,
                            'country_id' => $request->country,
                            'state_id' => $request->state,
                            'city_id' => $request->city,
                            'pincode' => $request->pincode,
                            'updated_by' => $request->updated_by,
                            'updated_at' => date('Y-m-d')
                        ]);

                        if ($user) {
                            $searchuserrp = user_permission::where('user_id', $id)->first();
                            if ($searchuserrp) {
                                $rpupdate =  $searchuserrp->update([
                                    "rp" => $rpjson
                                ]);

                                if ($rpupdate) {
                                    return response()->json([
                                        'status' => 200,
                                        'message' => 'user succesfully updated'
                                    ]);
                                } else {
                                    return response()->json([
                                        'status' => 404,
                                        'message' => 'user role & permissions not succesfully updated!'
                                    ]);
                                }
                            } else {
                                return response()->json([
                                    'status' => 404,
                                    'message' => 'No Such roles & permissions  Found!'
                                ]);
                            }
                        } else {
                            return response()->json([
                                'status' => 404,
                                'message' => 'user not succesfully updated!'
                            ]);
                        }
                    }
                } else {
                    return response()->json([
                        'status' => 404,
                        'message' => 'No Such user Found!'
                    ]);
                }
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $users = User::find($id);

        if ($users) {
            $users->update([
                'is_deleted' => 1

            ]);
            return response()->json([
                'status' => 200,
                'message' => 'user succesfully deleted'
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'No Such user Found!'
            ]);
        }
    }

    // status update
    public function statusupdate(Request $request, string $id)
    {
        $user = User::find($id);
        if ($user) {
            $user->update([
                'is_active' => $request->status
            ]);
            return response()->json([
                'status' => 200,
                'message' => 'user status succesfully updated'
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'No Such user Found!'
            ]);
        }
    }
}
