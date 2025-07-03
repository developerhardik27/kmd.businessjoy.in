<?php

namespace App\Http\Middleware;

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

        if (!isset($sessionToken)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Check if the token is present in the database
        $query = User::where(function ($q) use ($request, $sessionToken) {
            $q->where('id', $request->user_id)
                ->where('company_id', $request->company_id)
                ->whereRaw('BINARY `api_token` = ?', [$sessionToken]);

            if (Schema::hasColumn('users', 'super_api_token')) {
                $q->orWhereRaw('BINARY `super_api_token` = ?', [$sessionToken]);
            }
        });

        $dbToken = $query->first();

        if (!$dbToken) {
            return response()->json(['error' => 'You are Unauthorized'], 401);
        }

        return $next($request);
    }
}
