<?php

namespace App\Http\Middleware\v1_0_0;

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
        }

        // Check if the token is present in the database
        $dbToken = User::where('api_token', $sessionToken)->first();

        if (!$dbToken) {
            return response()->json(['error' => 'Invalid token'], 401);
        }

         if (!$request->ajax()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
         }
       
        return $next($request);
    }
}
