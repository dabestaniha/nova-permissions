<?php

namespace Sereny\NovaPermissions\Traits;

use Sereny\NovaPermissions\ModelForGuardState;
use Spatie\Permission\Guard;

trait ModelForGuardResolver {

    /**
     * Determines the guard model class
     *
     * @return class-string
     */
    public function modelForGuard(): ?string
    {
        return ModelForGuardState::$resolveModelForGuardCallback
            ? call_user_func(ModelForGuardState::$resolveModelForGuardCallback)
            : Guard::getModelForGuard($this->guard_name ?? config('auth.defaults.guard'));
    }
}
