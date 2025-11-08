<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        Gate::define('admin', function ($user) {
            return $user->role === 'admin';
        });

        Gate::define('teacher', function ($user) {
            return $user->role === 'teacher';
        });

        Gate::define('student', function ($user) {
            return $user->role === 'student';
        });

        Gate::define('guardian', function ($user) {
            return $user->role === 'guardian';
        });
    }
}
