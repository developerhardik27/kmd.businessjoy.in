<?php

namespace App\Http\Controllers\admin;

use App\Models\User;
use GuzzleHttp\Client;
use App\Models\company;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Jenssegers\Agent\Agent;
use Illuminate\Http\Request;
use App\Models\user_activity;
use App\Mail\ForgotPasswordMail;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class AdminLoginController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.login');
    }

    // check user permission function
    public function hasPermission($json, $module)
    {
        if (isset($json[$module]) && !empty($module)) {
            foreach ($json[$module] as $key => $value) {
                foreach ($value as $key2 => $value2) {
                    if ($value == 'loginhistory')
                        continue;
                    if ($key2 === "show" && $value2 == 1) {
                        return true;
                    }
                }
            }
        }
    }

    // check dashboard permission function
    public function hasDashboardPermission($json, $module)
    {
        if (isset($json[$module]) && !empty($module)) {
            foreach ($json[$module] as $key => $value) {
                if (is_string($key) && stripos($key, 'dashboard') !== false) {
                    foreach ($value as $key2 => $value2) {
                        if ($key2 === "show" && $value2 == 1) {
                            return true;
                        }
                    }
                }
            }
        }
    }

    /**
     * Summary of authenticate
     * - check user credential and check permissions
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\RedirectResponse
     */
    public function authenticate(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);


        if ($validator->fails()) {
            return redirect()->route('admin.login')->withErrors($validator)->withInput($request->only('email'));
        }

        $checkEmail = User::where('email', $request->email)->exists();

        if (!$checkEmail) {
            $this->save_user_login_history($request, 'direct', 'Email not registered.');
            return redirect()->route('admin.login')->with('error', 'You are not Registered !')->withInput($request->only('email'));
        }

        $verifyCredentials = Auth::guard('admin')->attempt(['email' => $request->email, 'password' => $request->password, 'is_deleted' => 0]);

        if (!$verifyCredentials) {
            $this->save_user_login_history($request, 'direct', 'Credential invalid.');
            return redirect()->route('admin.login')->with('error', 'credential invalid')->withInput($request->only('email'));
        }

        $admin = Auth::guard('admin')->user(); // user

        do {
            $api_token = Str::random(60);
            $exists = DB::table('users')->where('api_token', $api_token)->exists();
        } while ($exists);

        DB::table('users')->where('id', $admin->id)->update(['api_token' => $api_token]); // store api token into user table for further activity

        if (!(in_array($admin->role, [1, 2, 3])) || ($admin->is_active != 1)) {
            Auth::guard('admin')->logout();
            return redirect()->route('admin.login')->with('error', 'You are unauthorized to access admin panel')->withInput($request->only('email'));
        }

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
            $allmenus = [];

            /*
             * $menus (using in dashboard for showing menus) 
             */


            if ($this->hasPermission($rp, "invoicemodule")) {
                session(['invoice' => "yes"]);
                session(['menu' => 'invoice']);
                $allmenus[] = 'invoice';
                if ($this->hasDashboardPermission($rp, 'invoicemodule')) {
                    $menus[] = 'invoice';
                }
            }

            if ($this->hasPermission($rp, "quotationmodule")) {
                session(['quotation' => "yes"]);
                $allmenus[] = 'quotation';
                if (!(Session::has('menu') && (in_array(Session::get('menu'), ['invoice'])))) {
                    session(['menu' => 'quotation']);
                }
                if ($this->hasDashboardPermission($rp, 'quotationmodule')) {
                    $menus[] = 'quotation';
                }
            }

            if ($this->hasPermission($rp, "leadmodule")) {
                session(['lead' => "yes"]);
                $allmenus[] = 'lead';
                if (!(Session::has('menu') && (in_array(Session::get('menu'), ['invoice', 'quotation'])))) {
                    session(['menu' => 'lead']);
                }
                if ($this->hasDashboardPermission($rp, 'leadmodule')) {
                    $menus[] = 'lead';
                }
            }

            if ($this->hasPermission($rp, "customersupportmodule")) {
                session(['customersupport' => "yes"]);
                $allmenus[] = 'customersupport';
                if (!(Session::has('menu') && (in_array(Session::get('menu'), ['invoice', 'lead', 'quotation'])))) {
                    session(['menu' => 'Customer support']);
                }
                // $menus[] = 'customersupport';
            }

            if ($this->hasPermission($rp, "adminmodule")) {
                session(['admin' => "yes"]);
                $allmenus[] = 'admin';
                if (!(Session::has('menu') && (in_array(Session::get('menu'), ['invoice', 'lead', 'quotation', 'customersupport'])))) {
                    session(['menu' => 'admin']);
                }
                // $menus[] = 'admin';
            }

            // if ($this->hasPermission($rp, "accountmodule")) {
            //     session(['account' => "yes"]);
            //     if (!(Session::has('menu') && (in_array(Session::get('menu'), ['invoice', 'customersupport', 'admin', 'lead', 'inventory', 'reminder', 'blog'])))) {
            //         session(['menu' => 'account']);
            //     }
            //     // $menus[] = 'account';
            // }

            if ($this->hasPermission($rp, "inventorymodule")) {
                session(['inventory' => "yes"]);
                $allmenus[] = 'inventory';
                if (!(Session::has('menu') && (in_array(Session::get('menu'), ['invoice', 'lead', 'quotation', 'customersupport', 'admin'])))) {
                    session(['menu' => 'inventory']);
                }
                // $menus[] = 'inventory';
            }

            if ($this->hasPermission($rp, "remindermodule")) {
                session(['reminder' => "yes"]);
                $allmenus[] = 'reminder';
                if (!(Session::has('menu') && (in_array(Session::get('menu'), ['invoice', 'lead', 'quotation', 'customersupport', 'admin', 'inventory'])))) {
                    session(['menu' => 'reminder']);
                }
                if ($this->hasDashboardPermission($rp, 'remindermodule')) {
                    $menus[] = 'reminder';
                }
            }

            if ($this->hasPermission($rp, "reportmodule")) { // its invoice report
                session(['invoice' => "yes"]);
                session(['menu' => 'invoice']);
                session(['report' => "yes"]);
                $allmenus[] = 'report';
                // if (!(Session::has('menu') && (in_array(Session::get('menu'), ['invoice', 'customersupport', 'admin', 'account', 'lead', 'inventory'])))) {
                // session(['menu' => 'invoice']);
                // }
                // $menus[] = 'invoice';
            }

            if ($this->hasPermission($rp, "blogmodule")) {
                session(['blog' => "yes"]);
                $allmenus[] = 'blog';
                if (!(Session::has('menu') && (in_array(Session::get('menu'), ['invoice', 'lead', 'quotation', 'customersupport', 'admin', 'inventory', 'reminder'])))) {
                    session(['menu' => 'blog']);
                }
                // $menus[] = 'blog';
            }

            if ($this->hasPermission($rp, "logisticmodule")) {
                session(['logistic' => "yes"]);
                $allmenus[] = 'logistic';
                if (!(Session::has('menu') && (in_array(Session::get('menu'), ['invoice', 'lead', 'quotation', 'customersupport', 'admin', 'inventory', 'reminder', 'blog'])))) {
                    session(['menu' => 'logistic']);
                }
                $menus[] = 'logistic';
            }

            if ($this->hasPermission($rp, "developermodule")) {
                session(['developer' => "yes"]);
                $allmenus[] = 'developer';
                if (!(Session::has('menu') && (in_array(Session::get('menu'), ['invoice', 'lead', 'quotation', 'customersupport', 'admin', 'inventory', 'reminder', 'blog', 'logistic'])))) {
                    session(['menu' => 'developer']);
                }
                $menus[] = 'developer';
            }

            $request->session()->put([
                'allmenu' => $menus,
                'navmanu' => $allmenus // showing navbar base on this > 1
            ]);

        }

        $request->session()->put([
            'user' => $admin,
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

        if (Session::get('menu') == null) {
            DB::table('users')
                ->where('id', $admin->id)
                ->update(['api_token' => null]);

            $request->session()->flush();

            if (session_status() !== PHP_SESSION_ACTIVE)
                session_start();
            session_destroy();
            Auth::guard('admin')->logout();
            $this->save_user_login_history($request, 'direct', 'Due to no permissions.');
            return redirect()->back()->with('error', 'You have not any permission')->withInput($request->only('email'));
        }

        $this->save_user_login_history();//create login history

        if (isset($admin->default_module) && isset($admin->default_page)) {
            session(['menu' => $admin->default_module]);
            return redirect()->route('admin.' . $admin->default_page);
        }

        return redirect()->route('admin.welcome');

    }


    public function save_user_login_history($request = null, $via = 'direct', $message = null)
    {

        try {

            $user = Auth::guard('admin')->user();

            // Get the current IP address
            $ip = request()->header('X-Forwarded-For') ?? request()->server('REMOTE_ADDR');

            // Get the country based on IP using ip-api
            $client = new Client();
            $response = $client->get("http://ip-api.com/json/{$ip}");

            // Decode the response JSON
            $data = json_decode($response->getBody(), true);

            // If the status is 'fail' or any other issue, set 'Unknown'
            $country = $data['status'] === 'fail' ? 'Unknown' : $data['country'];

            // Get device information (Mobile/Desktop/Tablet/Etc...)
            $agent = new Agent();
            $device = $agent->isDesktop() ? 'Desktop' : ($agent->isMobile() ? 'Mobile' : 'Tablet');

            // Get the browser name (e.g., Chrome, Firefox)
            $browser = $agent->browser();

            if ($user) {
                // Create user login entry
                user_activity::create([
                    'user_id' => $user->id,
                    'username' => $user->email,  // Add username if needed
                    'ip' => $ip,  // Capture IP address
                    'country' => $country,
                    'device' => $device,
                    'browser' => $browser,
                    'status' => 'success',  // Mark the login status as success
                    'via' => $via,
                    'company_id' => $user->company_id,
                ]);
            } else {
                $user = User::where('email', $request->email)->where('is_deleted', 0)->first();

                if ($user) {
                    // Create user login entry
                    user_activity::create([
                        'user_id' => $user->id,
                        'username' => $user->email,  // Add username if needed
                        'ip' => $ip,  // Capture IP address
                        'country' => $country,
                        'device' => $device,
                        'browser' => $browser,
                        'status' => 'fail',  // Mark the login status as success
                        'via' => $via,
                        'company_id' => $user->company_id,
                        'message' => $message
                    ]);
                } else {
                    // Create user login entry
                    user_activity::create([
                        'username' => $request->email,  // Add username if needed
                        'ip' => $ip,  // Capture IP address
                        'country' => $country,
                        'device' => $device,
                        'browser' => $browser,
                        'status' => 'fail',  // Mark the login status as success
                        'via' => $via,
                        'message' => $message
                    ]);
                }

            }

            if ($user) {

                // Delete all user activity records older than 90 days
                user_activity::where('user_id', $user->id)
                    ->where('created_at', '<', now()->subDays(config('app.recent_activity_retention_days.login_activity') ?? 90))
                    ->delete();
            }

        } catch (\Exception $e) {
            Log::info($e->getMessage());
        }

    }

    /**
     * Summary of forgot
     * forgot page view
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function forgot()
    {
        return view('admin.forgot');
    }

    /**
     * Summary of forgot_password
     * varify  and return reset password link on email
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
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

    /**
     * Summary of reset_password
     * reset password page view
     * @param mixed $token
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function reset_password($token)
    {

        $user = User::where('pass_token', '=', $token)->first();

        if (!empty($user)) {
            return view('admin.resetpassword', ['token' => $token]);
        } else {
            abort(404);
        }
    }

    /**
     * reset password
     */

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

    /**
     * Summary of set_password
     * set new password view page
     * @param mixed $token
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function set_password($token)
    {

        $user = User::where('pass_token', '=', $token)->first();

        if (!empty($user)) {
            return view('admin.setpassword', ['token' => $token]);
        } else {
            abort(404);
        }
    }

    /**
     * Summary of post_set_password
     * set new password 
     * @param mixed $token
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
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

    /**
     * Summary of setmenusession
     * store menu in session base on user permission
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
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
     * Summary of superAdminLoginFromAnyUser
     * super admin login from any user 
     * @param \Illuminate\Http\Request $request
     * @param string $userId
     * @return mixed|\Illuminate\Http\RedirectResponse
     */

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
                    $allmenus = [];

                    /*
                     * $menus (using in dashboard for showing menus) 
                     */



                    if ($this->hasPermission($rp, "invoicemodule")) {
                        session(['invoice' => "yes"]);
                        session(['menu' => 'invoice']);
                        $allmenus[] = 'invoice';
                        if ($this->hasDashboardPermission($rp, 'invoicemodule')) {
                            $menus[] = 'invoice';
                        }
                    }

                    if ($this->hasPermission($rp, "quotationmodule")) {
                        session(['quotation' => "yes"]);
                        $allmenus[] = 'quotation';
                        if (!(Session::has('menu') && (in_array(Session::get('menu'), ['invoice'])))) {
                            session(['menu' => 'quotation']);
                        }
                        if ($this->hasDashboardPermission($rp, 'quotationmodule')) {
                            $menus[] = 'quotation';
                        }
                    }

                    if ($this->hasPermission($rp, "leadmodule")) {
                        session(['lead' => "yes"]);
                        $allmenus[] = 'lead';
                        if (!(Session::has('menu') && (in_array(Session::get('menu'), ['invoice', 'quotation'])))) {
                            session(['menu' => 'lead']);
                        }
                        if ($this->hasDashboardPermission($rp, 'leadmodule')) {
                            $menus[] = 'lead';
                        }
                    }

                    if ($this->hasPermission($rp, "customersupportmodule")) {
                        session(['customersupport' => "yes"]);
                        $allmenus[] = 'customersupport';
                        if (!(Session::has('menu') && (in_array(Session::get('menu'), ['invoice', 'lead', 'quotation'])))) {
                            session(['menu' => 'Customer support']);
                        }
                        // $menus[] = 'customersupport';
                    }

                    if ($this->hasPermission($rp, "adminmodule")) {
                        session(['admin' => "yes"]);
                        $allmenus[] = 'admin';
                        if (!(Session::has('menu') && (in_array(Session::get('menu'), ['invoice', 'lead', 'quotation', 'customersupport'])))) {
                            session(['menu' => 'admin']);
                        }
                        // $menus[] = 'admin';
                    }

                    // if ($this->hasPermission($rp, "accountmodule")) {
                    //     session(['account' => "yes"]);
                    //     if (!(Session::has('menu') && (in_array(Session::get('menu'), ['invoice', 'customersupport', 'admin', 'lead', 'inventory', 'reminder', 'blog'])))) {
                    //         session(['menu' => 'account']);
                    //     }
                    //     // $menus[] = 'account';
                    // }

                    if ($this->hasPermission($rp, "inventorymodule")) {
                        session(['inventory' => "yes"]);
                        $allmenus[] = 'inventory';
                        if (!(Session::has('menu') && (in_array(Session::get('menu'), ['invoice', 'lead', 'quotation', 'customersupport', 'admin'])))) {
                            session(['menu' => 'inventory']);
                        }
                        // $menus[] = 'inventory';
                    }

                    if ($this->hasPermission($rp, "remindermodule")) {
                        session(['reminder' => "yes"]);
                        $allmenus[] = 'reminder';
                        if (!(Session::has('menu') && (in_array(Session::get('menu'), ['invoice', 'lead', 'quotation', 'customersupport', 'admin', 'inventory'])))) {
                            session(['menu' => 'reminder']);
                        }
                        if ($this->hasDashboardPermission($rp, 'remindermodule')) {
                            $menus[] = 'reminder';
                        }
                    }

                    if ($this->hasPermission($rp, "reportmodule")) { // its invoice report
                        session(['invoice' => "yes"]);
                        session(['menu' => 'invoice']);
                        session(['report' => "yes"]);
                        $allmenus[] = 'report';
                        // if (!(Session::has('menu') && (in_array(Session::get('menu'), ['invoice', 'customersupport', 'admin', 'account', 'lead', 'inventory'])))) {
                        // session(['menu' => 'invoice']);
                        // }
                        // $menus[] = 'invoice';
                    }

                    if ($this->hasPermission($rp, "blogmodule")) {
                        session(['blog' => "yes"]);
                        $allmenus[] = 'blog';
                        if (!(Session::has('menu') && (in_array(Session::get('menu'), ['invoice', 'lead', 'quotation', 'customersupport', 'admin', 'inventory', 'reminder'])))) {
                            session(['menu' => 'blog']);
                        }
                        // $menus[] = 'blog';
                    }

                    if ($this->hasPermission($rp, "logisticmodule")) {
                        session(['logistic' => "yes"]);
                        $allmenus[] = 'logistic';
                        if (!(Session::has('menu') && (in_array(Session::get('menu'), ['invoice', 'lead', 'quotation', 'customersupport', 'admin', 'inventory', 'reminder', 'blog'])))) {
                            session(['menu' => 'logistic']);
                        }
                        $menus[] = 'logistic';
                    }

                    if ($this->hasPermission($rp, "developermodule")) {
                        session(['developer' => "yes"]);
                        $allmenus[] = 'developer';
                        if (!(Session::has('menu') && (in_array(Session::get('menu'), ['invoice', 'lead', 'quotation', 'customersupport', 'admin', 'inventory', 'reminder', 'blog', 'logistic'])))) {
                            session(['menu' => 'developer']);
                        }
                        $menus[] = 'developer';
                    }

                    $request->session()->put([
                        'allmenu' => $menus,
                        'navmanu' => $allmenus // showing navbar base on this > 1
                    ]);

                }

                $request->session()->put([
                    'user' => $admin,
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



                if (Session::get('menu') == null) {
                    DB::table('users')
                        ->where('id', $admin->id)
                        ->update(['super_api_token' => null]);

                    $request->session()->flush();

                    if (session_status() !== PHP_SESSION_ACTIVE)
                        session_start();
                    session_destroy();
                    Auth::guard('admin')->logout();
                    $this->save_user_login_history($user, 'superadmin', 'Due to no permissions.');
                    return redirect()->route('admin.login')->with('error', 'User has not any permission');
                }


                $this->save_user_login_history($request, 'superadmin');

                if (isset($admin->default_module) && isset($admin->default_page)) {
                    session(['menu' => $admin->default_module]);
                    return redirect()->route('admin.' . $admin->default_page);
                }

                return redirect()->route('admin.welcome');
            } else {
                Auth::guard('admin')->logout();
                return redirect()->route('admin.login')->with('error', 'User is unauthorized to access admin panel');
            }

        } else {
            return redirect()->route('admin.login')->with('error', 'User is not Registered !');
        }

    }

}
