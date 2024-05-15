<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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

                $routename =['admin.login', 'admin.authenticate','admin.forgot','admin.forgotpassword','admin.resetpassword','admin.post_resetpassword','admin.setpassword','admin.post_setpassword'];
                if (!in_array($request->route()->getName(), $routename)) {
                    // Redirect to the 'admin.login' route
                    Auth::guard('admin')->logout();
                    return redirect()->route('admin.login')->with('error', 'Session Expired');
                }

            }


        }

        // else {
        //     // Log that the route could not be determined
        //     Log::warning('Unable to determine current route name');
        // }
        return $next($request);
    }
}
