<?php

namespace App\Policies;

use App\Models\customer;
use App\Models\User;

class CustomerPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }
    public function view(User $authenticatedUser, $requestedCustomer)
    {  

        if ($authenticatedUser->role === 1) {
            return true; // Allow access
        }
        return $authenticatedUser->id === $requestedCustomer->created_by
        || $authenticatedUser->company_id === $requestedCustomer->company_id;
    }
}
