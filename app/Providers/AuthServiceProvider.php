<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use App\Models\company;
use App\Policies\CompanyPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;



class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // \App\Models\product::class => \App\Policies\ProductPolicy::class,
        // User::class => UserPolicy::class,
        company::class => CompanyPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
       
      
       
    }
}
