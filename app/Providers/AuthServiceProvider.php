<?php

namespace Modules\Core\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Nwidart\Modules\Facades\Module;

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
        if (Module::has('blog') && Module::isEnabled('ModuleName')) {
            $this->policies[\Modules\Comment\Models\Comment::class] = \Modules\Comment\Policies\CommentPolicy::class;
        }

        // Grant all permissions to "Super Admin" role
        Gate::before(fn($user) => $user->hasRole('super-admin') ? true : null);
    }
}
