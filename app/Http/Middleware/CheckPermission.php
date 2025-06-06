<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;
use App\Models\company;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $module, $submodule, $action): Response
    {
        $menu = '';

        $user = Auth::user();

        $dbname = company::find($user->company_id);
        config(['database.connections.dynamic_connection.database' => $dbname->dbname]);

        // Establish connection to the dynamic database
        DB::purge('dynamic_connection');
        DB::reconnect('dynamic_connection');

        $user_rp = DB::connection('dynamic_connection')->table('user_permissions')->select('rp')->where('user_id', $user->id)->get();
        $permissions = json_decode($user_rp, true);
        $rp = json_decode($permissions[0]['rp'], true);

        if ($module == 'invoicemodule') {
            $menu = 'invoice';
        }
        if ($module == 'leadmodule') {
            $menu = 'lead';
        }
        if ($module == 'customersupportmodule') {
            $menu = 'Customer support';
        }
        if ($module == 'adminmodule') {
            $menu = 'admin';
        }
        if ($module == 'accountmodule') {
            $menu = 'account';
        }
        if ($module == 'inventorymodule') {
            $menu = 'inventory';
        }
        if ($module == 'remindermodule') {
            $menu = 'reminder';
        }
        if ($module == 'blogmodule') {
            $menu = 'blog';
        }
        if ($module == 'quotationmodule') {
            $menu = 'quotation';
        }

        // Check if the user has permission for the given module and action
        // if (isset($rp[$module][$submodule][$action]) && $rp[$module][$submodule][$action] == '1' && Session::get('menu') == $menu) {
        if (isset($rp[$module][$submodule][$action]) && $rp[$module][$submodule][$action] == '1') {
            return $next($request);
        }

        // Handle unauthorized access
        abort(404, 'You are Unauthorized.');
    }
}
