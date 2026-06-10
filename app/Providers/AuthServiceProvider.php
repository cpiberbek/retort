<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use App\Models\Plant;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [];

    public function boot()
    {
        Gate::define('can access cikande', function ($user) {
            $plantName = strtolower(
                Plant::where('uuid', $user->plant)->value('plant') ?? ''
            );

            return $plantName === 'cikande 2';
        });
    }
}