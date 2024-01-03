<?php

namespace App\Policies;

use App\Models\bank_detail;
use App\Models\User;

class Bank_DetailPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function view(User $authenticatedUser, bank_detail $requestedbank)
    {  

        if ($authenticatedUser->role === 1) {
            return true; // Allow access
        }
        if ($authenticatedUser->role === 3) {
            return false; // denny access
        }
        return $authenticatedUser->company_id === $requestedbank->created_by;
        
    }
}
