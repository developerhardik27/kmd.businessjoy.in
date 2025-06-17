<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\api_authorization;
use Illuminate\Support\Facades\Auth;
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
        if ((!$sessionToken) && !isset($request->site_key) && !isset($request->server_key)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        if ($sessionToken) {
            // Check if the token is present in the database
            $query = User::where('id', $request->user_id)
                ->where('company_id', $request->company_id)
                ->where('api_token', $sessionToken);

            if (Schema::hasColumn('users', 'super_api_token')) {
                $query->orWhere('super_api_token', $sessionToken);
            }

            $dbToken = $query->first();
            if (!$dbToken) {
                return response()->json(['error' => 'You are Unauthorized'], 401);
            }

        } elseif (isset($request->site_key) && isset($request->server_key)) {
            // $domainName = basename($request->header('Origin'));
            $origin = $request->header('Origin');
             \Log::info('origin' . $origin);
            $domainName = $origin ? parse_url($origin, PHP_URL_HOST) : null;

            \Log::info('domainName' . $domainName);
            
            if ($domainName) {
                // Normalize by removing "www."
                $domainName = preg_replace('/^www\./i', '', $domainName);
            }
            
            \Log::info('Domain name' . $domainName);
            $authorize = api_authorization::where('site_key', $request->site_key)
                ->where('server_key', $request->server_key)
                ->whereRaw('FIND_IN_SET(?, domain_name)', [$domainName])
                ->first();

            if (!$authorize) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
        }

        return $next($request);
    }
}
