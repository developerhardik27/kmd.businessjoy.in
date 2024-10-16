<?php

namespace App\Http\Middleware;

use App\Models\api_authorization;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
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
        if (!$sessionToken) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }elseif(!isset($request->site_key) && !isset($request->server_key)){
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        if($sessionToken){
            // Check if the token is present in the database
            $dbToken = User::where('api_token', $sessionToken)->orWhere('super_api_token',$sessionToken)->first();

            if (!$dbToken) {
                return response()->json(['error' => 'Invalid token'], 401);
            }
        }elseif(isset($request->site_key) && isset($request->server_key)){
            $domainName = $request->getHost();
            $authorize = api_authorization::where('site_key', $request->site_key)
                       ->where('server_key', $request->server_key)
                       ->where('domain_name', 'LIKE', '%' . $domainName . '%')
                       ->first();

            if (!$authorize) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
        }
       
        return $next($request);
    }
}
