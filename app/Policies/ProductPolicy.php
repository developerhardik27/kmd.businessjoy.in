<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Support\Facades\Log;

class ProductPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function view(User $authenticatedUser, $requestedProduct)
    {  

        if ($authenticatedUser->role === 1) {
            return true; // Allow access
        }
        return $authenticatedUser->id === $requestedProduct->created_by
        || $authenticatedUser->company_id === $requestedProduct->company_id;
    }
}
