<?php

namespace App\Policies;

use App\Models\Purchase;
use App\Models\User;

class PurchasePolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function view(User $authenticatedUser, $requestedpurchase)
    {

        if ($authenticatedUser->role === 1) {
            return true; // Allow access
        }
        return $authenticatedUser->id === $requestedpurchase->created_by
            || $authenticatedUser->company_id === $requestedpurchase->company_id;
    }
}
