<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Mail\ForgotPasswordMail;
use App\Models\company;
use App\Models\User;
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

            if (User::where('email', $request->email)->first()) { // check if request email is registered or not
                if (Auth::guard('admin')->attempt(['email' => $request->email, 'password' => $request->password, 'is_deleted' => 0])) { //verify credentials
                    $admin = Auth::guard('admin')->user(); // user
                    $api_token = Str::random(60); // generate api token

                    DB::table('users')->where('id', $admin->id)->update(['api_token' => $api_token]); // store api token into user table for further activity

                    if ((($admin->role == 1) or ($admin->role == 2) or ($admin->role == 3)) && ($admin->is_active == 1)) {

                        $dbname = company::find($admin->company_id);
                        config(['database.connections.dynamic_connection.database' => $dbname->dbname]);

                        // Establish connection to the dynamic database
                        DB::purge('dynamic_connection');
                        DB::reconnect('dynamic_connection');

                        // fetch user permissions
                        $rpdetailsjson = DB::connection('dynamic_connection')->table('user_permissions')->select('rp')->where('user_id', $admin->id)->get();

                        if ($rpdetailsjson->count() > 0) {
                            // Decode the JSON data
                            $rp = json_decode($rpdetailsjson[0]->rp, true);

                            // Store the decoded data in the session
                            session(['user_permissions' => $rp]);

                            $menus = [];

                            // check user permission function
                            function hasPermission($json, $module)
                            {
                                if (isset($json[$module]) && !empty($module)) {
                                    foreach ($json[$module] as $key => $value) {
                                        foreach ($value as $key2 => $value2) {
                                            if ($key2 === "show" && $value2 == 1) {
                                                return true;
                                            }
                                        }
                                    }
                                }
                            }


                            /*
                             * $menus (using in dashboard for showing menus) 
                             */



                            if (hasPermission($rp, "invoicemodule")) {
                                session(['invoice' => "yes"]);
                                session(['menu' => 'invoice']);
                                session(['showinvoicesettings' => "yes"]);
                                $menus[] = 'invoice';
                            }

                            if (hasPermission($rp, "leadmodule")) {
                                session(['lead' => "yes"]);
                                if (!(Session::has('menu') && (in_array(Session::get('menu'), ['invoice', 'customersupport', 'admin', 'account', 'inventory', 'reminder', 'blog'])))) {
                                    session(['menu' => 'lead']);
                                }
                                // $menus[] = 'lead';
                            }

                            if (hasPermission($rp, "customersupportmodule")) {
                                session(['customersupport' => "yes"]);
                                if (!(Session::has('menu') && (in_array(Session::get('menu'), ['invoice', 'lead', 'admin', 'account', 'inventory', 'reminder', 'blog'])))) {
                                    session(['menu' => 'customersupport']);
                                }
                                // $menus[] = 'customersupport';
                            }
                            if (hasPermission($rp, "adminmodule")) {
                                session(['admin' => "yes"]);
                                if (!(Session::has('menu') && (in_array(Session::get('menu'), ['invoice', 'customersupport', 'lead', 'account', 'inventory', 'reminder', 'blog'])))) {
                                    session(['menu' => 'admin']);
                                }
                                // $menus[] = 'admin';
                            }
                            if (hasPermission($rp, "accountmodule")) {
                                session(['account' => "yes"]);
                                if (!(Session::has('menu') && (in_array(Session::get('menu'), ['invoice', 'customersupport', 'admin', 'lead', 'inventory', 'reminder', 'blog'])))) {
                                    session(['menu' => 'account']);
                                }
                                // $menus[] = 'account';
                            }
                            if (hasPermission($rp, "inventorymodule")) {
                                session(['inventory' => "yes"]);
                                if (!(Session::has('menu') && (in_array(Session::get('menu'), ['invoice', 'customersupport', 'admin', 'account', 'lead', 'reminder', 'blog'])))) {
                                    session(['menu' => 'inventory']);
                                }
                                // $menus[] = 'inventory';
                            }
                            if (hasPermission($rp, "remindermodule")) {
                                session(['reminder' => "yes"]);
                                if (!(Session::has('menu') && (in_array(Session::get('menu'), ['invoice', 'customersupport', 'admin', 'account', 'lead', 'inventory', 'blog'])))) {
                                    session(['menu' => 'reminder']);
                                }
                                $menus[] = 'reminder';
                            }
                            if (hasPermission($rp, "reportmodule")) {
                                session(['invoice' => "yes"]);
                                session(['menu' => 'invoice']);
                                session(['report' => "yes"]);
                                // if (!(Session::has('menu') && (in_array(Session::get('menu'), ['invoice', 'customersupport', 'admin', 'account', 'lead', 'inventory'])))) {
                                // session(['menu' => 'invoice']);
                                // }
                                // $menus[] = 'report';
                            }
                            if (hasPermission($rp, "blogmodule")) {
                                session(['blog' => "yes"]);
                                if (!(Session::has('menu') && (in_array(Session::get('menu'), ['invoice', 'customersupport', 'admin', 'account', 'lead', 'inventory', 'reminder'])))) {
                                    session(['menu' => 'blog']);
                                }
                                // $menus[] = 'blog';
                            }
                            $request->session()->put([
                                'allmenu' => $menus
                            ]);

                        }

                        $request->session()->put([
                            'admin_role' => $admin->role,
                            'company_id' => $admin->company_id,
                            'user_id' => $admin->id,
                            'name' => $admin->firstname . ' ' . $admin->lastname,
                            'api_token' => $api_token,
                            'folder_name' => $dbname->app_version,
                            'loggedby' => 'user'
                        ]);


                        if (session_status() !== PHP_SESSION_ACTIVE)
                            session_start();
                        $_SESSION['folder_name'] = session('folder_name');

                        if (isset($admin->default_module) && isset($admin->default_page)) {
                            session(['menu' => $admin->default_module]);
                            dd(session('menu'));
                            return redirect()->route('admin.' . $admin->default_page);
                        }
                        if (Session::get('menu') == null) {
                            DB::table('users')
                                ->where('id', $admin->id)
                                ->update(['api_token' => null]);

                            $request->session()->flush();

                            if (session_status() !== PHP_SESSION_ACTIVE)
                                session_start();
                            session_destroy();
                            Auth::guard('admin')->logout();
                            return redirect()->back()->with('error', 'You have not any permission')->withInput($request->only('email'));
                        }
                        return redirect()->route('admin.welcome');
                    } else {
                        Auth::guard('admin')->logout();
                        return redirect()->route('admin.login')->with('error', 'You are unauthorized to access admin panel')->withInput($request->only('email'));
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

            Mail::to($user->email)->bcc('parthdeveloper9@gmail.com')->send(new ForgotPasswordMail($user));

            return redirect()->back()->with('success', 'plz check your mailbox and reset your password');
        } else {
            return redirect()->back()->with('error', 'sorry ! you are not registered ');
        }
    }


    public function set_password($token)
    {

        $user = User::where('pass_token', '=', $token)->first();

        if (!empty($user)) {
            return view('admin.setpassword', ['token' => $token]);
        } else {
            abort(404);
        }
    }

    public function post_set_password($token, Request $request)
    {
        $user = User::where('pass_token', '=', $token)->first();

        if (!empty($user)) {

            if ($request->password == $request->cpassword) {

                $user->password = Hash::make($request->password);
                $user->pass_token = Str::random(40);
                $user->save();


                session()->flash('email', $user->email);

                return redirect()->route('admin.login')->with('success', 'Password Successfully Established');
            } else {
                return redirect()->back()->with('error', 'Password and Confirm Password does not match');
            }
        } else {
            abort(404);
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

    public function superAdminLoginFromAnyUser(Request $request, string $userId)
    {

        if (session('user_id') != 1) {
            return redirect()->route('admin.login')->with('error', 'You are unauthorized');
        }

        session()->flush();

        if (session_status() !== PHP_SESSION_ACTIVE)
            session_start();
        session_destroy();
        Auth::guard('admin')->logout();

        $user = User::find($userId);

        if ($user) { // check if request email is registered or not

            $admin = Auth::guard('admin')->loginUsingId($user->id); // user

            $api_token = Str::random(60); // generate api token

            DB::table('users')->where('id', $admin->id)->update(['super_api_token' => $api_token]); // store api token into user table for further activity

            if (in_array($admin->role, [1, 2, 3]) && $admin->is_active == 1) {

                $dbname = company::find($admin->company_id);
                config(['database.connections.dynamic_connection.database' => $dbname->dbname]);

                // Establish connection to the dynamic database
                DB::purge('dynamic_connection');
                DB::reconnect('dynamic_connection');

                // fetch user permissions
                $rpdetailsjson = DB::connection('dynamic_connection')->table('user_permissions')->select('rp')->where('user_id', $admin->id)->get();

                if ($rpdetailsjson->count() > 0) {
                    // Decode the JSON data
                    $rp = json_decode($rpdetailsjson[0]->rp, true);

                    // Store the decoded data in the session
                    session(['user_permissions' => $rp]);

                    $menus = [];

                    // check user permission function
                    function hasPermission($json, $module)
                    {
                        if (isset($json[$module]) && !empty($module)) {
                            foreach ($json[$module] as $key => $value) {
                                foreach ($value as $key2 => $value2) {
                                    if ($key2 === "show" && $value2 == 1) {
                                        return true;
                                    }
                                }
                            }
                        }
                    }


                    /*
                     * $menus (using in dashboard for showing menus) 
                     */



                    if (hasPermission($rp, "invoicemodule")) {
                        session(['invoice' => "yes"]);
                        session(['menu' => 'invoice']);
                        session(['showinvoicesettings' => "yes"]);
                        $menus[] = 'invoice';
                    }

                    if (hasPermission($rp, "leadmodule")) {
                        session(['lead' => "yes"]);
                        if (!(Session::has('menu') && (in_array(Session::get('menu'), ['invoice', 'customersupport', 'admin', 'account', 'inventory', 'reminder', 'blog'])))) {
                            session(['menu' => 'lead']);
                        }
                        // $menus[] = 'lead';
                    }

                    if (hasPermission($rp, "customersupportmodule")) {
                        session(['customersupport' => "yes"]);
                        if (!(Session::has('menu') && (in_array(Session::get('menu'), ['invoice', 'lead', 'admin', 'account', 'inventory', 'reminder', 'blog'])))) {
                            session(['menu' => 'customersupport']);
                        }
                        // $menus[] = 'customersupport';
                    }
                    if (hasPermission($rp, "adminmodule")) {
                        session(['admin' => "yes"]);
                        if (!(Session::has('menu') && (in_array(Session::get('menu'), ['invoice', 'customersupport', 'lead', 'account', 'inventory', 'reminder', 'blog'])))) {
                            session(['menu' => 'admin']);
                        }
                        // $menus[] = 'admin';
                    }
                    if (hasPermission($rp, "accountmodule")) {
                        session(['account' => "yes"]);
                        if (!(Session::has('menu') && (in_array(Session::get('menu'), ['invoice', 'customersupport', 'admin', 'lead', 'inventory', 'reminder', 'blog'])))) {
                            session(['menu' => 'account']);
                        }
                        // $menus[] = 'account';
                    }
                    if (hasPermission($rp, "inventorymodule")) {
                        session(['inventory' => "yes"]);
                        if (!(Session::has('menu') && (in_array(Session::get('menu'), ['invoice', 'customersupport', 'admin', 'account', 'lead', 'reminder', 'blog'])))) {
                            session(['menu' => 'inventory']);
                        }
                        // $menus[] = 'inventory';
                    }
                    if (hasPermission($rp, "remindermodule")) {
                        session(['reminder' => "yes"]);
                        if (!(Session::has('menu') && (in_array(Session::get('menu'), ['invoice', 'customersupport', 'admin', 'account', 'lead', 'inventory', 'blog'])))) {
                            session(['menu' => 'reminder']);
                        }
                        $menus[] = 'reminder';
                    }
                    if (hasPermission($rp, "reportmodule")) {
                        session(['invoice' => "yes"]);
                        session(['menu' => 'invoice']);
                        session(['report' => "yes"]);
                        // if (!(Session::has('menu') && (in_array(Session::get('menu'), ['invoice', 'customersupport', 'admin', 'account', 'lead', 'inventory'])))) {
                        // session(['menu' => 'invoice']);
                        // }
                        // $menus[] = 'report';
                    }
                    if (hasPermission($rp, "blogmodule")) {
                        session(['blog' => "yes"]);
                        if (!(Session::has('menu') && (in_array(Session::get('menu'), ['invoice', 'customersupport', 'admin', 'account', 'lead', 'inventory', 'reminder'])))) {
                            session(['menu' => 'blog']);
                        }
                        // $menus[] = 'blog';
                    }
                    $request->session()->put([
                        'allmenu' => $menus
                    ]);

                }

                $request->session()->put([
                    'admin_role' => $admin->role,
                    'company_id' => $admin->company_id,
                    'user_id' => $admin->id,
                    'name' => $admin->firstname . ' ' . $admin->lastname,
                    'api_token' => $api_token,
                    'folder_name' => $dbname->app_version,
                    'loggedby' => 'admin'
                ]);


                if (session_status() !== PHP_SESSION_ACTIVE)
                    session_start();
                $_SESSION['folder_name'] = session('folder_name');

                if (isset($admin->default_module) && isset($admin->default_page)) {
                    session('menu', $admin->default_module);
                    return redirect()->route('admin.' . $admin->default_page);
                }
                if (Session::get('menu') == null) {
                    DB::table('users')
                        ->where('id', $admin->id)
                        ->update(['super_api_token' => null]);

                    $request->session()->flush();

                    if (session_status() !== PHP_SESSION_ACTIVE)
                        session_start();
                    session_destroy();
                    Auth::guard('admin')->logout();
                    return redirect()->back()->with('error', 'You have not any permission');
                }
                return redirect()->route('admin.welcome');
            } else {
                Auth::guard('admin')->logout();
                return redirect()->route('admin.login')->with('error', 'You are unauthorized to access admin panel');
            }

        } else {
            return redirect()->route('admin.login')->with('error', 'You are not Registered !');
        }

    }

}
