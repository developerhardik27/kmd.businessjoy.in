<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use App\Models\company;
use Illuminate\Http\Request;
use App\Models\api_authorization;
use Illuminate\Support\Facades\Log;

class DynamicVersionMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $version = null;

        // Middleware logic to dynamically decide the version
        $middlewareNamespace = 'App\\Http\\Middleware\\';

        try {
            // Check if the user exists
            if ($request->has('user_id')) {
                // Retrieve the user if the user_id exists in the request
                $user = User::find($request->user_id);

                // If the user exists, retrieve the company's version
                if ($user) {
                    $version = Company::find($user->company_id);
                }
            } elseif ($request->has('site_key') && $request->has('server_key')) {
                $company_id = api_authorization::where('site_key', $request->site_key)
                    ->where('server_key', $request->server_key)
                    ->where('is_active', 1)
                    ->where('is_deleted', 0)
                    ->select('company_id')
                    ->first();

                // If the user exists, retrieve the company's version
                if ($company_id) {
                    $version = Company::find($company_id->company_id);
                }

            }
            $versionexplode = $version ? $version->app_version : "v1_0_0"; // Default version
        } catch (\Exception $e) {
            Log::error("Error determining version: " . $e->getMessage());
            // Handle error gracefully
            $versionexplode = "v1_0_0"; // Default version in case of error
        }

        // Dynamically build the middleware class name
        $middlewareClass = $middlewareNamespace . $versionexplode . '\\CheckToken';

        // Check if the class exists
        if (!class_exists($middlewareClass)) {
            abort(500, "Middleware class does not exist: " . $middlewareClass);
        }
 
        // Continue processing the request
        return $next($request);
    }
}

