<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class LogPageLoadTime
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Record the start time
        $startTime = microtime(true);

        // Process the request
        $response = $next($request);

        // Record the end time
        $endTime = microtime(true);

        // Calculate the duration in milliseconds
        $duration = ($endTime - $startTime) * 1000;
        $threshold = config('app.page_load_threshold_ms');
        
        
        // Log page info if duration exceeds the threshold
        if ($duration > $threshold) { 
            $logInfo = [] ;
             
            $logInfo = [
                'page_url' => $request->fullUrl(),
                'controller' => class_basename($request->route()->controller ?? 'Unknown'),
                'method' => $request->route()->getActionMethod() ?? 'Unknown', 
                'start_time' => date('Y-m-d H:i:s', $startTime),
                'end_time' => date('Y-m-d H:i:s', $endTime),
                'load_time' => $duration,
                'date_time' => now(),
                'created_at' => now()
            ];

            if(Auth::guard('admin')->user()){ 
                $dbname = company::find(Auth::guard('admin')->user()->company_id); 
                $logInfo['username'] = Auth::guard('admin')->user()->firstname . Auth::guard('admin')->user()->lastname ;
                $logInfo['db_name'] =  $dbname->dbname ; 
            } 
            // Save to database
            $this->saveLog($logInfo);
        } 

        return $response;
    }

    protected function saveLog(array $logInfo)
    {
        DB::connection('mysql')->table('page_load_logs')->insert($logInfo);
    }
}
