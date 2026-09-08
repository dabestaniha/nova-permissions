# Laravel Nova - Roles & Permissions

[![Latest Version on Packagist](https://img.shields.io/packagist/v/sereny/nova-permissions?style=flat-square)](https://packagist.org/packages/sereny/nova-permissions)
[![Total Downloads](https://poser.pugx.org/sereny/nova-permissions/downloads?format=flat-square)](https://packagist.org/packages/sereny/nova-permissions)

A Laravel Nova tool for grouping permissions and assigning roles and permissions to users. It uses Spatie's `laravel-permission` package.

This release supports PHP 8.1+, Laravel Nova 5.9+, and `spatie/laravel-permission` 6.25+.

We have a Migration, Seed, Policy and Resource ready for a good Authorization Experience.

1. [Installation](#Installation)
2. [Permissions with Groups](#permissions-with-groups)
   - [Index view](#index-view)
   - [Detail View](#detail-view)
   - [Edit View](#edit-view)
   - [Database Seeding](#database-seeding)
   - [Create a Model Policy](#create-a-model-policy)
   - [Super Admin](#super-admin)
3. [Customization](#customization)
4. [Credits](#credits)

## Installation

You can install the package in to a Laravel app that uses [Nova](https://nova.laravel.com) via composer:

```bash
composer require sereny/nova-permissions
```

If your application does not already use `spatie/laravel-permission`, publish its configuration and migrations first:

```bash
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
```

Then publish this package's additive migration. It adds the `group` column used by the Nova resources without replacing Spatie's schema:

```bash
php artisan vendor:publish --provider="Sereny\NovaPermissions\ToolServiceProvider" --tag="migrations"
```

Migrate the Database:

```bash
php artisan migrate
```

Next up, you must register the tool with Nova. This is typically done in the `tools` method of the `NovaServiceProvider`.

```php
// in app/Providers/NovaServiceProvider.php

// ...

public function tools(): array
{
    return [
        // ...
        new \Sereny\NovaPermissions\NovaPermissions(),
    ];
}
```

If you want to hide the tool from certain users, you can write your custom logic for the ability to see the tool:

```php
// in app/Providers/NovaServiceProvider.php

// ...

public function tools(): array
{
    return [
        // ...
        (new \Sereny\NovaPermissions\NovaPermissions())->canSee(function ($request) {
            return $request->user()->isSuperAdmin();
        }),
    ];
}


```

Finally, add `MorphToMany` fields to your `app/Nova/User` resource:

```php
// ...
use Laravel\Nova\Fields\MorphToMany;

public function fields(\Laravel\Nova\Http\Requests\NovaRequest $request): array
{
    return [
        // ...
        MorphToMany::make('Roles', 'roles', \Sereny\NovaPermissions\Nova\Role::class),
        MorphToMany::make('Permissions', 'permissions', \Sereny\NovaPermissions\Nova\Permission::class),
    ];
}
```

Add the `Spatie\Permission\Traits\HasRoles` trait to your User model(s):

```php
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasRoles;

    // ...
}
```

A new menu item called **Roles & Permissions** will appear in your Nova app after installing this package.

## Permissions with Groups

### Index View

![image](/.github/images/role-index.png)

### Detail View

![image](/.github/images/role-detail.png)

### Edit View

![image](/.github/images/role-edit.png)

### Database Seeding

Publish our Seeder with the following command:

```bash
php artisan vendor:publish --provider="Sereny\NovaPermissions\ToolServiceProvider" --tag="seeders"
```

This is just an example on how you could seed your Database with Roles and Permissions. Modify `RolesAndPermissionsSeeder.php` in `database/seeders`. List all your Models you want to have Permissions for in the `$collection` Array and change the email for the Super-Admin:

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $collection = collect([
            'Invoice',
            'Client',
            'Contact',
            'Payment',
            'Team',
            'User',
            'Role',
            'Permission'
            // ... your own models/permission you want to crate
        ]);

        $collection->each(function ($item) {
            collect(['viewAny', 'view', 'update', 'create', 'delete', 'restore', 'forceDelete'])
                ->each(function ($ability) use ($item) {
                    Permission::findOrCreate($ability.$item)->update(['group' => $item]);
                });
        });

        // Create a Super-Admin Role and assign all permissions to it
        $role = Role::findOrCreate('super-admin');
        $role->syncPermissions(Permission::all());

        // Give User Super-Admin Role
        $user = \App\Models\User::where('email', 'your@email.com')->first();
        $user?->assignRole('super-admin');
    }
}
```

Now you can seed the Database. Add `$this->call(RolesAndPermissionsSeeder::class);` to the `DatabaseSeeder`.

> **Note**: If this doesn't work, run `composer dumpautoload` to autoload the Seeder.

### Create a Model Policy

You can extend `Sereny\NovaPermissions\Policies\BasePolicy` and have a very clean Model Policy that works with Nova.

For Example: Create a new Contact Policy with `php artisan make:policy ContactPolicy` with the following code:

```php
<?php

namespace App\Policies;

use Sereny\NovaPermissions\Policies\BasePolicy;

class ContactPolicy extends BasePolicy
{
    /**
     * The Permission key the Policy corresponds to.
     *
     * @var string
     */
    public $key = 'contact';
}
```

It should now work as exptected. Just create a Role, modify its Permissions and the Policy should take care of the rest.

Laravel automatically discovers policies that follow its standard model and policy naming conventions. Register non-standard policy locations manually in your application's service provider.

> **Note**: Only extend the policy if your permission names follow this package's convention. For a `Contact` resource, those names are `viewAnyContact`, `viewContact`, `createContact`, `updateContact`, `deleteContact`, `restoreContact`, and `forceDeleteContact`.

### Super Admin

A Super Admin can do everything. If you extend our Policy, make sure to add a `isSuperAdmin()` Function to your `App\User` Model:

```php
<?php

namespace App\Models;

class User
{

    /**
     * Determines if the User is a Super admin
     * @return bool
    */
    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super-admin');
    }
}
```

## Customizations

```php
// in app/Providers/NovaServiceProvider.php

use App\Nova\Permission;
use App\Nova\Role;
use App\Policies\PermissionPolicy;
use App\Policies\RolePolicy;

// ...

public function tools(): array
{
    return [
        // ...
        \Sereny\NovaPermissions\NovaPermissions::make()
            ->roleResource(Role::class)
            ->permissionResource(Permission::class)
            ->rolePolicy(RolePolicy::class)
            ->permissionPolicy(PermissionPolicy::class)
            ->disablePermissions()
            ->disableMenu()
            ->hideFieldsFromRole([
                'id',
                'guard_name'
            ])
            ->hideFieldsFromPermission([
                'id',
                'guard_name',
                'users',
                'roles'
            ])
            ->resolveGuardsUsing(function ($request) {
                return ['web'];
            })
            ->resolveModelForGuardUsing(function () {
                /** @var App\Auth\CustomGuard $guard */
                $guard = auth()->guard();
                return $guard->getProvider()->getModel();
            })
    ];
}
```

### Important

To customize the `Role` model you need to use `Sereny\NovaPermissions\Traits\SupportsRole` trait:

```php

// Role using UUID primary key

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Str;
use Sereny\NovaPermissions\Traits\SupportsRole;
use Spatie\Permission\Models\Role as BaseRole;

class Role extends BaseRole
{
    use HasUuids,
        SupportsRole; // REQUIRED TRAIT
}

```

## Credits

This Package is inspired by [eminiarts/nova-permissions](https://github.com/eminiarts/nova-permissions).

A huge thanks goes to Spatie [spatie/laravel-permission](https://github.com/spatie/laravel-permission) for their amazing work!
