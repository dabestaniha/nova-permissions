<?php

namespace Workbench\Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
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
            // ... // List all your Models you want to have Permissions for.
        ]);

        $collection->each(function ($item) {
            collect(['viewAny', 'view', 'update', 'create', 'delete', 'restore', 'forceDelete'])
                ->each(function ($ability) use ($item) {
                    Permission::findOrCreate($ability.$item)->update(['group' => $item]);
                });
        });

        // Create a Super-Admin Role and assign all Permissions
        $role = Role::findOrCreate('super-admin');
        $role->syncPermissions(Permission::all());

        // Give User Super-Admin Role
        // $user = \App\Models\User::where('email', 'your@email.com')->first(); // Change this to your email.
        // $user->assignRole('super-admin');
    }
}
