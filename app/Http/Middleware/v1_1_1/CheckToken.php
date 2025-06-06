<?php

namespace App\Http\Middleware\v1_1_1;

use Closure;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\api_authorization;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

class CheckToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
         
        // Check if the token is present in the session
        $sessionToken = $request->token;
        
        if(!$sessionToken && !isset($request->site_key) && !isset($request->server_key)){
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        if($sessionToken){
            // Check if the token is present in the database
            if (Schema::hasColumn('users', 'super_api_token')) {
                // If the column exists, include it in the query
                $dbToken = User::where('api_token', $sessionToken)
                    ->orWhere('super_api_token', $sessionToken)
                    ->first();
            } else {
                // If the column does not exist, only check api_token
                $dbToken = User::where('api_token', $sessionToken)->first();
            }

            if (!$dbToken) {
                return response()->json(['error' => 'Invalid token'], 401);
            }
        }
        return $next($request);
    }
}
