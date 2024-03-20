<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Http\JsonResponse;


class UserPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function view(User $authenticatedUser, User $requestedUser)
    {

        if ($authenticatedUser->role === 1) {
            return true; // Allow access
        }
        return $authenticatedUser->id === $requestedUser->created_by
                 || $authenticatedUser->id === $requestedUser->id 
                 || $authenticatedUser->company_id === $requestedUser->company_id;
    }

    /**
     * Handle unauthorized access.
     *
     * @return JsonResponse
     */
    public function authorizeFailed()
    {
        return response()->json(['error' => 'Unauthorized'], 403);
    }

}
