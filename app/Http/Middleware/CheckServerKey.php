<?php

namespace App\Http\Middleware;


use App\Models\uuid_company;
use Closure;
use App\Models\company;
use Illuminate\Http\Request; 
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class CheckServerKey
{
    /**
     * Handle an incoming request.
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        // Check if the site key and server key is present in the session
        $companyuuid = $request->company_id;
        $serverKey = $request->header('X-Server-Key');

        if (!isset($companyuuid) && !isset($serverKey)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $authorize = uuid_company::where('uuid', $companyuuid)
            ->first();

        if (!$authorize) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Set dynamic DB
        $company = company::find($authorize->company_id);

        if (!$company) {
            return response()->json(['error' => 'No Record found'], 404);
        }

        config(['database.connections.dynamic_connection.database' => $company->dbname]);
        DB::purge('dynamic_connection');
        DB::reconnect('dynamic_connection');

        // check server key and get user id
        $userid = DB::connection('dynamic_connection')
        ->table('api_server_keys')
        ->where('server_key',$serverKey)
        ->where('is_deleted',0)
        ->get();

        if(!$userid){ // server key not match
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Store info for later
        $request->merge([
            'otherapi' => 'yes',
            'user_id' => $userid,
            'company_id' => $company->id,
            'app_version' => $company->app_version
        ]);

        return $next($request);
    }
}
