<?php

namespace App\Policies;

use App\Models\invoice;
use App\Models\User;

class invoicePolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function view(User $authenticatedUser, invoice $requestedinvoice)
    {  

        if ($authenticatedUser->role === 1) {
            return true; // Allow access
        }
        return $authenticatedUser->id === $requestedinvoice->created_by
        || $authenticatedUser->company_id === $requestedinvoice->company_id;
    }
}
