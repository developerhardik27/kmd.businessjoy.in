<?php

namespace App\Http\Controllers\v1_0_0\admin;

use App\Http\Controllers\Controller;
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
        $this->version = $_SESSION['folder_name'];
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return view($this->version.'.admin.user');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view($this->version.'.admin.userform', ['user_id' => Session::get('user_id'), 'company_id' => Session::get('company_id')]);
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


        return view($this->version.'.admin.userupdateform', ['user_id' => Session::get('user_id'), 'edit_id' => $id]);



    }
    public function edituser(string $id)
    {

        $user = User::findOrFail($id);
        $this->authorize('view', $user);

        return view($this->version.'.admin.edituser', ['user_id' => Session::get('user_id'), 'edit_id' => $id]);
    }
    public function profile(string $id)
    {

        $user = User::findOrFail($id);
        $this->authorize('view', $user);

        return view($this->version.'.admin.profile', ['user_id' => Session::get('user_id'), 'id' => $id]);
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
