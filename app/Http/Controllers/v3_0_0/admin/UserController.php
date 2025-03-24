<?php

namespace App\Http\Controllers\v3_0_0\admin;

use App\Http\Controllers\Controller;
use App\Models\company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class UserController extends Controller
{

    public $version;
    public function __construct()
    {
        if (session_status() !== PHP_SESSION_ACTIVE)
        session_start();
        if(isset($_SESSION['folder_name'])){
            $this->version =  $_SESSION['folder_name'];
        }
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if(isset($request->search)){
            $search = $request->search;
        } else{
            $search = '';
        }

        return view($this->version.'.admin.User.user',['search' => $search]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $company = company::find(Session::get('company_id'));
        $user = User::where('company_id','=',$company->id)->where('is_deleted',0)->get();

        $companymaxuser = $company->max_users ;
 
       
        if($user->count() >= $companymaxuser){
            $allow = "no" ;
        }else{
            $allow = "yes" ;
        }

        return view($this->version.'.admin.User.userform', ['user_id' => Session::get('user_id'), 'company_id' => Session::get('company_id'),'allow' => $allow]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $this->authorize('view', $user);

        return view($this->version.'.admin.User.userupdateform', ['user_id' => Session::get('user_id'), 'edit_id' => $id]);
    }

    public function edituser(string $id)
    {
        $user = User::findOrFail($id);
        $this->authorize('view', $user);

        return view($this->version.'.admin.User.edituser', ['user_id' => Session::get('user_id'), 'edit_id' => $id]);
    }

    public function profile(string $id)
    {
        $user = User::findOrFail($id);
        $this->authorize('view', $user);

        return view($this->version.'.admin.User.profile', ['user_id' => Session::get('user_id'),'company_id' => Session::get('company_id'), 'id' => $id ]);
    }

}
