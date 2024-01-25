<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Mail\ForgotPasswordMail;
use App\Models\User;
use App\Models\user_permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AdminLoginController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.login');
    }

    public function authenticate(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);


        if ($validator->passes()) {

            if (User::where('email', '=', $request->email)->first()) {
                if (Auth::guard('admin')->attempt(['email' => $request->email, 'password' => $request->password])) {
                    $admin = Auth::guard('admin')->user();
                    $api_token = Str::random(60);




                    DB::table('users')->where('id', $admin->id)->update(['api_token' => $api_token]);

                    if ((($admin->role == 1) or ($admin->role == 2)) && ($admin->is_active == 1)) {

                        $rpdetailsjson = DB::table('user_permissions')->select('rp')->where('user_id', $admin->id)->get();

                        if ($rpdetailsjson->count() > 0) {
                            // Decode the JSON data
                            $rp = json_decode($rpdetailsjson[0]->rp, true);

                            // Store the decoded data in the session
                            session(['user_permissions' => $rp]);


                            function hasPermission($json, $module)
                            {
                                if (isset($json[$module]) && !empty($module)) {
                                    foreach ($json[$module] as $key => $value) {
                                        foreach ($value as $key2 => $value2) {
                                            if ($key2 === "show") {
                                                if($value2 === "1"){
                                                    return true;
                                                }
                                            }
                                        }
                                    }
                                }
                            }

                            if(hasPermission($rp, "invoicemodule")){
                                session(['invoice' => "yes"]);
                                session([ 'menu' => 'invoice']);

                            }
                            if(hasPermission($rp, "leadmodule")){
                                session(['lead' => "yes"]);
                                if(!(Session::has('menu') && (Session::get('menu') == 'invoice' || Session::get('menu') == 'customersupport'))){
                                    session([ 'menu' => 'lead']);
                                }
                            }
                            if(hasPermission($rp,"customersupportmodule")){
                                session(['customersupport' => "yes"]);
                                if(!(Session::has('menu') && (Session::get('menu') == 'invoice' || Session::get('menu') == 'lead'))){
                                    session([ 'menu' => 'customersupport']);
                                }
                            }
                            
                        }
                        $request->session()->put([
                            'admin_role' => $admin->role,
                            'company_id' => $admin->company_id,
                            'user_id' => $admin->id,
                            'name' => $admin->firstname . ' ' . $admin->lastname,
                            'api_token' => $api_token,
                        ]);

                        return redirect()->route('admin.welcome');
                    } else {
                        Auth::guard('admin')->logout();
                        return redirect()->route('admin.login')->with('error', 'You are not authorized to access admin panel');
                    }
                } else {
                    return redirect()->route('admin.login')->with('error', 'credential invalid')->withInput($request->only('email'));
                }
            } else {
                return redirect()->route('admin.login')->with('error', 'You are not Registered !')->withInput($request->only('email'));
            }
        } else {
            return redirect()->route('admin.login')->withErrors($validator)->withInput($request->only('email'));
        }
    }


    public function forgot()
    {
        return view('admin.forgot');
    }
    public function reset_password($token)
    {

        $user = User::where('pass_token', '=', $token)->first();

        if (!empty($user)) {
            return view('admin.resetpassword', ['token' => $token]);
        } else {
            abort(404);
        }
    }
    public function post_reset_password($token, Request $request)
    {

        $user = User::where('pass_token', '=', $token)->first();

        if (!empty($user)) {

            if ($request->password == $request->cpassword) {

                $user->password = Hash::make($request->password);
                $user->pass_token = Str::random(40);
                $user->save();

                return redirect()->route('admin.login')->with('success', 'Password Successfully Reset');
            } else {
                return redirect()->back()->with('error', 'Password and Confirm Password does not match');
            }
        } else {
            abort(404);
        }
    }

    public function forgot_password(Request $request)
    {

        $user = User::where('email', '=', $request->email)->first();

        if (!empty($user)) {
            $user->pass_token = str::random(40);
            $user->save();

            Mail::to($user->email)->send(new ForgotPasswordMail($user));

            return redirect()->back()->with('success', 'plz check your mailbox and reset your password');
        } else {
            return redirect()->back()->with('error', 'sorry ! you are not registered ');
        }
    }


    public function setmenusession(Request $request)
    {

        $value = $request->input('value');
        // Set the session value
        $request->session()->forget('menu');
        $request->session()->put('menu', $value);
        $request->session()->save();
        return response()->json(['status' => $value]);
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
    }
}
