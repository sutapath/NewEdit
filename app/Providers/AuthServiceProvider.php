<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // // 'App\Models\Model' => 'App\Policies\ModelPolicy',
        // ApplyScholar::class => ApplyScholarPolicy::class,
        // ScholarInform::class => ScholarInformPolicy::class,
        // Scholarship::class => ScholarshipPolicy::class,
        // ActivityInformation::class => ActivityInformationPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}
