<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckSession
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        $route = $request->route();

        if ($route && $route->getName()) {

            // Log the route name
            // Log::debug('Current route name: ' . $routeName);


            if (session_status() !== PHP_SESSION_ACTIVE) {
                session_start();
            }
            if (!isset($_SESSION['folder_name'])) {

                // $routename =['admin.login', 'admin.authenticate','admin.forgot','admin.forgotpassword','admin.resetpassword','admin.post_resetpassword','admin.setpassword','admin.post_setpassword','admin.new'];
                // if (!in_array($request->route()->getName(), $routename)) {
                //     // Redirect to the 'admin.login' route
                // }
                $user = Auth::guard('admin')->user();
                //user login that user_login field update to 1
                User::where('id', $user->id)
                    ->update(['user_login' => 0]);
                Auth::guard('admin')->logout();
                return redirect()->route('admin.login')->with('error', 'Session Expired');
            }
        }

        // else {
        //     // Log that the route could not be determined
        //     Log::warning('Unable to determine current route name');
        // }
        return $next($request);
    }
}
