<?php

namespace App\Http\Controllers\v1_0_0\api;


use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\company;


class userController extends commonController
{

    public $db, $companyId, $userId, $rp, $masterdbname, $user_permissionModel;
    public function __construct(Request $request)
    {

        if (isset ($request->company_id)) {
            $dbname = company::find($request->company_id);
        } else {
            $dbname = company::find(1);
        }

        $this->db = $dbname->dbname;

        config(['database.connections.dynamic_connection.database' => $dbname->dbname]);

        // Establish connection to the dynamic database
        DB::purge('dynamic_connection');
        DB::reconnect('dynamic_connection');

        $this->companyId = $request->company_id;
        $this->userId = $request->user_id;
        $this->masterdbname = DB::connection()->getDatabaseName();

        $user_rp = DB::connection('dynamic_connection')->table('user_permissions')->select('rp')->where('user_id', $this->userId)->get();
        $permissions = json_decode($user_rp, true);
        $this->rp = json_decode($permissions[0]['rp'], true);

        $this->user_permissionModel = $this->getmodel('user_permission');

    }

    public function username(Request $request)
    {

        $user = DB::table('users')
            ->join('company', 'users.company_id', '=', 'company.id')
            ->join('company_details', 'company.company_details_id', '=', 'company_details.id')
            ->select('users.firstname', 'users.lastname', 'users.img', 'company_details.name')->where('users.id', $this->userId)->get();
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

        $users = DB::table('users')
            ->join('country', 'users.country_id', '=', 'country.id')
            ->join('state', 'users.state_id', '=', 'state.id')
            ->join('city', 'users.city_id', '=', 'city.id')
            ->join('company', 'users.company_id', '=', 'company.id')
            ->join('company_details', 'company.company_details_id', '=', 'company_details.id')
            ->select('users.id', 'users.firstname', 'users.lastname', 'users.email', 'users.contact_no', 'country.country_name', 'state.state_name', 'city.city_name', 'users.pincode', 'company_details.name', 'users.img', 'users.created_by')
            ->where('users.is_active', '1')->where('users.is_deleted', '0')->where('users.id', $request->id)
            ->get();


        if ($this->rp['invoicemodule']['user']['alldata'] != 1) {
            if ($users[0]->created_by != $this->userId && $users[0]->id != $this->userId) {
                return response()->json([
                    'status' => 500,
                    'message' => "You are Unauthorized!"
                ]);
            }
        }
        if ($this->rp['invoicemodule']['user']['view'] != 1) {
            return response()->json([
                'status' => 500,
                'message' => 'You are Unauthorized!'
            ]);
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
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        if ($this->companyId == 1) {
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
            $usersres = DB::table('users')
                ->join('country', 'users.country_id', '=', 'country.id')
                ->join('state', 'users.state_id', '=', 'state.id')
                ->join('city', 'users.city_id', '=', 'city.id')
                ->join('company', 'users.company_id', '=', 'company.id')
                ->join('company_details', 'company.company_details_id', '=', 'company_details.id')
                ->join('user_role', 'users.role', '=', 'user_role.id')
                ->select('users.id', 'users.firstname', 'users.lastname', 'users.email', 'users.password', 'users.contact_no', 'country.country_name', 'state.state_name', 'city.city_name', 'users.pincode', 'company_details.name as company_name', 'user_role.role as user_role', 'users.img', 'users.created_by', 'users.updated_by', 'users.is_active')
                ->where('users.is_deleted', 0)->where('users.company_id', $this->companyId);

            if ($this->rp['invoicemodule']['user']['alldata'] != 1) {
                $usersres->where('users.created_by', $this->userId);
            }

            $users = $usersres->get();
        }

        if ($this->rp['invoicemodule']['user']['view'] != 1) {
            return response()->json([
                'status' => 500,
                'message' => 'You are Unauthorized'
            ]);
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
            'user_id' => 'required|numeric',
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
            if ($this->rp['invoicemodule']['user']['add'] != 1) {
                return response()->json([
                    'status' => 500,
                    'message' => 'You are Unauthorized'
                ]);
            }
            $rp = [
                "invoicemodule" => [
                    "invoice" => ["show" => $request->showinvoicemenu, "add" => $request->addinvoice, "view" => $request->viewinvoice, "edit" => $request->editinvoice, "delete" => $request->deleteinvoice, "alldata" => $request->alldatainvoice],
                    "mngcol" => ["show" => $request->showmngcolmenu, "add" => $request->addmngcol, "view" => $request->viewmngcol, "edit" => $request->editmngcol, "delete" => $request->deletemngcol, "alldata" => $request->alldatamngcol],
                    "formula" => ["show" => $request->showformulamenu, "add" => $request->addformula, "view" => $request->viewformula, "edit" => $request->editformula, "delete" => $request->deleteformula, "alldata" => $request->alldataformula],
                    "invoicesetting" => ["show" => $request->showinvoicesettingmenu, "add" => $request->addinvoicesetting, "view" => $request->viewinvoicesetting, "edit" => $request->editinvoicesetting, "delete" => $request->deleteinvoicesetting, "alldata" => $request->alldatainvoicesetting],
                    "company" => ["show" => $request->showcompanymenu, "add" => $request->addcompany, "view" => $request->viewcompany, "edit" => $request->editcompany, "delete" => $request->deletecompany, "alldata" => $request->alldatacompany],
                    "bank" => ["show" => $request->showbankmenu, "add" => $request->addbank, "view" => $request->viewbank, "edit" => $request->editbank, "delete" => $request->deletebank, "alldata" => $request->alldatabank],
                    "user" => ["show" => $request->showusermenu, "add" => $request->adduser, "view" => $request->viewuser, "edit" => $request->edituser, "delete" => $request->deleteuser, "alldata" => $request->alldatauser],
                    "customer" => ["show" => $request->showcustomermenu, "add" => $request->addcustomer, "view" => $request->viewcustomer, "edit" => $request->editcustomer, "delete" => $request->deletecustomer, "alldata" => $request->alldatacustomer],
                    "product" => ["show" => $request->showproductmenu, "add" => $request->addproduct, "view" => $request->viewproduct, "edit" => $request->editproduct, "delete" => $request->deleteproduct, "alldata" => $request->alldataproduct],
                    "purchase" => ["show" => $request->showpurchasemenu, "add" => $request->addpurchase, "view" => $request->viewpurchase, "edit" => $request->editpurchase, "delete" => $request->deletepurchase, "alldata" => $request->alldatapurchase]
                ],
                "leadmodule" => [
                    "lead" => ["show" => $request->showleadmenu, "add" => $request->addlead, "view" => $request->viewlead, "edit" => $request->editlead, "delete" => $request->deletelead, "alldata" => $request->alldatalead]
                ],
                "customersupportmodule" => [
                    "customersupport" => ["show" => $request->showcustomersupportmenu, "add" => $request->addcustomersupport, "view" => $request->viewcustomersupport, "edit" => $request->editcustomersupport, "delete" => $request->deletecustomersupport, "alldata" => $request->alldatacustomersupport]
                ]
            ];

            $rpjson = json_encode($rp);


            $userdata = [];
            if ($request->hasFile('img') && $request->hasFile('img') != '') {
                $image = $request->file('img');
                $imageName = $request->firstname . time() . '.' . $image->getClientOriginalExtension();

                if (!file_exists('uploads/')) {
                    mkdir('uploads/', 0755, true);
                }
                // Save the image to the uploads directory
                if ($image->move('uploads/', $imageName)) {
                    $userdata['img'] = $imageName;
                }

            }

            $user = array_merge($userdata, [
                'firstname' => $request->firstname,
                'lastname' => $request->lastname,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'contact_no' => $request->contact_number,
                'country_id' => $request->country,
                'state_id' => $request->state,
                'city_id' => $request->city,
                'pincode' => $request->pincode,
                'company_id' => $this->companyId,
                'created_by' => $this->userId
            ]);

            $users = User::insertgetId($user);

            if ($users) {
                $userrp = $this->user_permissionModel::create([
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

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {

        $user = User::find($id);
        $dbname = company::find($user->company_id);

        $users = DB::table('users')
            ->join($dbname->dbname . '.user_permissions', 'users.id', '=', $dbname->dbname . '.user_permissions.user_id')
            ->select('users.*', 'user_permissions.rp')
            ->where('users.id', $id)->get();


        if (($this->rp['invoicemodule']['user']['alldata'] != 1) || ($users[0]->company_id != $this->companyId)) {
            if ($users[0]->created_by != $this->userId && $users[0]->id != $this->userId && $this->userId != 1) {
                return response()->json([
                    'status' => 500,
                    'message' => "You are Unauthorized!"
                ]);
            }
        }
        if ($this->rp['invoicemodule']['user']['view'] != 1) {
            return response()->json([
                'status' => 500,
                'message' => 'You are Unauthorized'
            ]);
        }
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
        if ($this->rp['invoicemodule']['user']['alldata'] != 1) {
            if ($users->created_by != $this->userId) {
                return response()->json([
                    'status' => 500,
                    'message' => "You are Unauthorized!"
                ]);
            }
        }
        if ($this->rp['invoicemodule']['user']['edit'] != 1) {
            return response()->json([
                'status' => 500,
                'message' => 'You are Unauthorized'
            ]);
        }
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
            'user_id' => 'required|numeric',
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

            if ($this->rp['invoicemodule']['user']['edit'] != 1) {
                return response()->json([
                    'status' => 500,
                    'message' => 'You are Unauthorized'
                ]);
            }

            $user = User::find($id);
            $dbname = company::find($user->company_id);
            config(['database.connections.dynamic_connection.database' => $dbname->dbname]);

            // Establish connection to the dynamic database
            DB::purge('dynamic_connection');
            DB::reconnect('dynamic_connection');

            $rp = [
                "invoicemodule" => [
                    "invoice" => ["show" => $request->showinvoicemenu, "add" => $request->addinvoice, "view" => $request->viewinvoice, "edit" => $request->editinvoice, "delete" => $request->deleteinvoice, "alldata" => $request->alldatainvoice],
                    "mngcol" => ["show" => $request->showmngcolmenu, "add" => $request->addmngcol, "view" => $request->viewmngcol, "edit" => $request->editmngcol, "delete" => $request->deletemngcol, "alldata" => $request->alldatamngcol],
                    "formula" => ["show" => $request->showformulamenu, "add" => $request->addformula, "view" => $request->viewformula, "edit" => $request->editformula, "delete" => $request->deleteformula, "alldata" => $request->alldataformula],
                    "invoicesetting" => ["show" => $request->showinvoicesettingmenu, "add" => $request->addinvoicesetting, "view" => $request->viewinvoicesetting, "edit" => $request->editinvoicesetting, "delete" => $request->deleteinvoicesetting, "alldata" => $request->alldatainvoicesetting],
                    "company" => ["show" => $request->showcompanymenu, "add" => $request->addcompany, "view" => $request->viewcompany, "edit" => $request->editcompany, "delete" => $request->deletecompany, "alldata" => $request->alldatacompany],
                    "bank" => ["show" => $request->showbankmenu, "add" => $request->addbank, "view" => $request->viewbank, "edit" => $request->editbank, "delete" => $request->deletebank, "alldata" => $request->alldatabank],
                    "user" => ["show" => $request->showusermenu, "add" => $request->adduser, "view" => $request->viewuser, "edit" => $request->edituser, "delete" => $request->deleteuser, "alldata" => $request->alldatauser],
                    "customer" => ["show" => $request->showcustomermenu, "add" => $request->addcustomer, "view" => $request->viewcustomer, "edit" => $request->editcustomer, "delete" => $request->deletecustomer, "alldata" => $request->alldatacustomer],
                    "product" => ["show" => $request->showproductmenu, "add" => $request->addproduct, "view" => $request->viewproduct, "edit" => $request->editproduct, "delete" => $request->deleteproduct, "alldata" => $request->alldataproduct],
                    "purchase" => ["show" => $request->showpurchasemenu, "add" => $request->addpurchase, "view" => $request->viewpurchase, "edit" => $request->editpurchase, "delete" => $request->deletepurchase, "alldata" => $request->alldatapurchase]
                ],
                "leadmodule" => [
                    "lead" => ["show" => $request->showleadmenu, "add" => $request->addlead, "view" => $request->viewlead, "edit" => $request->editlead, "delete" => $request->deletelead, "alldata" => $request->alldatalead]
                ],
                "customersupportmodule" => [
                    "customersupport" => ["show" => $request->showcustomersupportmenu, "add" => $request->addcustomersupport, "view" => $request->viewcustomersupport, "edit" => $request->editcustomersupport, "delete" => $request->deletecustomersupport, "alldata" => $request->alldatacustomersupport]
                ]
            ];


            $rpjson = json_encode($rp);
            $users = User::find($id);
            $userupdatedata = [];
            if ($request->hasFile('img') && $request->hasFile('img') != '') {
                $image = $request->file('img');
                $imageName = $request->firstname . time() . '.' . $image->getClientOriginalExtension();

                if ($image->move('uploads/', $imageName)) {
                    $imagePath = 'uploads/' . $users->img;
                    if (is_file($imagePath)) {
                        unlink($imagePath);  // old img remove
                    }
                    $userupdatedata['img'] = $imageName;
                }
            }

            if ($users) {
                $userupdatedata = array_merge($userupdatedata, [
                    'firstname' => $request->firstname,
                    'lastname' => $request->lastname,
                    'email' => $request->email,
                    'contact_no' => $request->contact_number,
                    'country_id' => $request->country,
                    'state_id' => $request->state,
                    'city_id' => $request->city,
                    'pincode' => $request->pincode,
                    'updated_by' => $this->userId,
                    'updated_at' => date('Y-m-d')
                ]);

                if ($request->password != '') {
                    $userupdatedata['password'] = Hash::make($request->password);
                }
                $user = $users->update($userupdatedata);
                if ($user) {
                    if ($request->editrole == 1) {
                        return response()->json([
                            'status' => 200,
                            'message' => 'user succesfully updated'
                        ]);
                    } else {
                        $searchuserrp = $this->user_permissionModel::where('user_id', $id)->first();
                        if ($searchuserrp) {
                            $rpupdate = $searchuserrp->update([
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
                    }
                } else {
                    return response()->json([
                        'status' => 404,
                        'message' => 'user not succesfully updated!'
                    ]);
                }

            } else {
                return response()->json([
                    'status' => 404,
                    'message' => 'No Such user Found!'
                ]);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $users = User::find($id);
        if ($this->rp['invoicemodule']['user']['alldata'] != 1) {
            if ($users->created_by != $this->userId) {
                return response()->json([
                    'status' => 500,
                    'message' => "You are Unauthorized!"
                ]);
            }
        }
        if ($this->rp['invoicemodule']['user']['delete'] != 1) {
            return response()->json([
                'status' => 500,
                'message' => 'You are Unauthorized'
            ]);
        }
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
        if ($this->rp['invoicemodule']['user']['alldata'] != 1) {
            if ($user->created_by != $this->userId) {
                return response()->json([
                    'status' => 500,
                    'message' => "You are Unauthorized!"
                ]);
            }
        }
        if ($this->rp['invoicemodule']['user']['edit'] != 1) {
            return response()->json([
                'status' => 500,
                'message' => 'You are Unauthorized'
            ]);
        }
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
