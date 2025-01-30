<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;

class AssignAdminRoleToUsers extends Command
{
    protected $signature = 'users:make-admin';
    protected $description = 'Assign admin role to all users';

    public function handle()
    {
        // Create admin role if it doesn't exist
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);

        // Get all users and assign admin role
        $users = User::all();
        $count = 0;

        foreach ($users as $user) {
            if (!$user->hasRole('Admin')) {
                $user->assignRole('Admin');
                $count++;
            }
        }

        $this->info("{$count} users have been assigned the Admin role");
        return Command::SUCCESS;
    }
}
