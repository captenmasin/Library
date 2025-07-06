<?php

namespace App\Console\Commands;

use App\Enums\UserRole;
use Illuminate\Console\Command;

use function Laravel\Prompts\info;
use function Laravel\Prompts\multiselect;

class AssignRole extends Command
{
    protected $signature = 'users:role:assign {role?}';

    public function handle(): void
    {
        $selectedRole = $this->argument('role');

        $roles = multiselect(
            label: 'What role should the user have?',
            options: collect(UserRole::cases())
                ->mapWithKeys(fn ($role) => [$role->value => $role->name])
                ->toArray(),
            default: [$selectedRole],
            required: true
        );

        if (in_array(UserRole::Admin->value, $roles)) {
            $roles[] = UserRole::User->value;
        }

        $users = multiselect(
            label: 'Select users to assign the role to',
            options: collect(\App\Models\User::all())
                ->mapWithKeys(fn ($user) => [$user->id => $user->name])
                ->toArray(),
            required: true
        );

        $users = \App\Models\User::whereIn('id', $users)->get();
        foreach ($users as $user) {
            foreach ($roles as $role) {
                $user->assignRole($role);
            }

            info("Assigned roles to user: {$user->name}");
        }
    }
}
