<?php

namespace Sereny\NovaPermissions;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;
use Sereny\NovaPermissions\Nova\Permission;
use Sereny\NovaPermissions\Nova\Role;

class ToolServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(Filesystem $filesystem): void
    {
        $this->publishes([
            __DIR__.'/../database/migrations/add_group_to_permissions_table.php.stub' => $this->getMigrationFileName($filesystem),
        ], 'migrations');

        $this->publishes([
            __DIR__.'/../database/seeders/RolesAndPermissionsSeeder.php.stub' => $this->app->databasePath().'/seeders/RolesAndPermissionsSeeder.php',
        ], 'seeders');

        Role::$model = config('permission.models.role');
        Permission::$model = config('permission.models.permission');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
    }

    /**
     * Returns existing migration file if found, else uses the current timestamp.
     *
     * @param  Filesystem $filesystem
     * @return string
     */
    protected function getMigrationFileName(Filesystem $filesystem): string
    {
        $timestamp = date('Y_m_d_His');

        return Collection::make($this->app->databasePath().DIRECTORY_SEPARATOR.'migrations'.DIRECTORY_SEPARATOR)
            ->flatMap(function ($path) use ($filesystem) {
                return $filesystem->glob($path.'*_add_group_to_permissions_table.php');
            })->push($this->app->databasePath()."/migrations/{$timestamp}_add_group_to_permissions_table.php")
            ->first();
    }
}
