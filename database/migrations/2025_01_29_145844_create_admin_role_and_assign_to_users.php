<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\User;
use Spatie\Permission\Models\Role;

class CreateAdminRoleAndAssignToUsers extends Migration
{
    public function up()
    {
        // Create admin role if it doesn't exist
        $adminRole = Role::where('name', 'Admin')->first();

        if (!$adminRole) {
            $adminRole = Role::create([
                'name' => 'Admin',
                'guard_name' => 'web'
            ]);
        }

        // Assign admin role to all users
        User::chunk(100, function ($users) use ($adminRole) {
            foreach ($users as $user) {
                if (!$user->hasRole('Admin')) {
                    $user->assignRole('Admin');
                }
            }
        });
    }

    public function down()
    {
        // Remove admin role from all users
        User::chunk(100, function ($users) {
            foreach ($users as $user) {
                $user->removeRole('Admin');
            }
        });

        // Delete the Admin role
        Role::where('name', 'Admin')->delete();
    }
}
