<?php

namespace App\Http\Controllers\admin;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class HomeController extends Controller
{

    public $version;
    public function __construct()
    {
        if (session_status() !== PHP_SESSION_ACTIVE)
            session_start();

        if (isset($_SESSION['folder_name'])) {
            $this->version = $_SESSION['folder_name'];
        } else {
            $this->version = "v1_0_0";
        }
    }

    public function index()
    {
        return view($this->version . '.admin.index');
    }

    public function logout(Request $request)
    {
        $apiToken = session('api_token');
        $request->merge([
            'api_token' => $apiToken
        ]);

        $response =  $this->apiLogout($request);

        $response = $response->getData(true);

        if ($response['status'] == '200') {
            $request->session()->flush();
            if (session_status() !== PHP_SESSION_ACTIVE)
                session_start();
            session_destroy();
            return redirect()->route('admin.login');
        }
    }

    // when user logged in new device then old session destroy and old user logout from old device automatic
    public function singlelogout(Request $request)
    {

        $request->session()->flush();

        if (session_status() !== PHP_SESSION_ACTIVE)
            session_start();
        session_destroy();
        $user = Auth::guard('admin')->user();
        //user login that user_login field update to 1
        User::where('id', $user->id)
            ->update(['user_login' => 0]);
        Auth::guard('admin')->logout();

        return redirect()->route('admin.login')->with('unauthorized', 'You are already logged in on a different device');
    }

    public function apiLogout(Request $request)
    {
        $token = $request->api_token;

        if (!$token) {
            return response()->json([
                'status' => 401,
                'message' => 'Authorization token not provided'
            ], 200);
        }

        // Check if it's a normal user or a superadmin impersonation
        $user = DB::table('users')
            ->where('api_token', $token)
            ->orWhere('super_api_token', $token)
            ->first();
        //user login that user_login field update to 1
        User::where('id', $user->id)
            ->update(['user_login' => 0]);
        if (!$user) {
            return response()->json([
                'status' => 401,
                'message' => 'Invalid or expired token'
            ], 200);
        }

        // Revoke the appropriate token
        $updateField = $user->super_api_token === $token ? 'super_api_token' : 'api_token';

        DB::table('users')->where('id', $user->id)->update([
            $updateField => null
        ]);

        Auth::guard('admin')->logout(); // optional if still authenticated

        return response()->json([
            'status' => 200,
            'message' => 'Logged out successfully'
        ], 200);
    }
}
