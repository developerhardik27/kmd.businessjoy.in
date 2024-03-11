<?php

namespace App\Policies;

use App\Models\company;
use App\Models\User;

class CompanyPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }
    public function view(User $authenticatedUser, company $requestedCompany)
    {  

        if ($authenticatedUser->role === 1) {
            return true; // Allow access
        }
        if ($authenticatedUser->role === 3) {
            return false; // denny access
        }
        return $authenticatedUser->company_id === $requestedCompany->id || $authenticatedUser->id === $requestedCompany->created_by ;
    }
}
