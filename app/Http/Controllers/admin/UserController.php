<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Session;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {    
        return view('admin.user');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.userform',['user_id'=> Session::get('user_id') , 'company_id'=>Session::get('company_id') ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {   
        $user = User::findOrFail($id);
        $this->authorize('view', $user);
        

        return view('admin.userupdateform',['user_id'=> Session::get('user_id') ,'edit_id' => $id ]);
        // if (Gate::allows('view', [$user->id, (int)$id ])) {
        // } else {
        //     // If not authorized, handle unauthorized access
        //     return response()->json(['error' => 'Unauthorized'], 401);
        // }

       
    }
    public function edituser(string $id)
    {     
    
        $user = User::findOrFail($id);
        $this->authorize('view', $user);

        return view('admin.edituser',['user_id'=> Session::get('user_id') ,'edit_id' =>  $id  ]);
    }
    public function profile(string $id)
    {     

           $user = User::findOrFail($id);
        $this->authorize('view', $user);

        return view('admin.profile',['user_id'=> Session::get('user_id'),'id'=>$id]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
