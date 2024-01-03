<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next , $module, $submodule, $action): Response
    {
        $menu = '';

        $user = Auth::user(); 

        $user_rp = DB::table('user_permissions')->select('rp')->where('user_id',$user->id)->get();
        $permissions = json_decode($user_rp,true);
        $rp = json_decode($permissions[0]['rp'],true);

       if($module == 'invoicemodule'){
          $menu = 'invoice';
       }
       if($module == 'leadmodule'){
        $menu = 'lead';
       }
     
        // Check if the user has permission for the given module and action
        if (isset($rp[$module][$submodule][$action]) && $rp[$module][$submodule][$action] === '1' && Session::get('menu') == $menu ) {
            return $next($request);
        }
    
        // Handle unauthorized access
        abort(404, 'You are not Authorized.');
    }
}
