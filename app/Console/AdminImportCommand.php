<?php

declare(strict_types=1);

namespace Modules\Core\Console;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Modules\User\Models\User;

use function Laravel\Prompts\info;

#[Signature('durrbar:import-admin')]
#[Description('Create an admin user.')]
class AdminImportCommand extends Command
{
    public function handle(): int
    {
        $user = User::firstOrCreate(
            [
                'email' => 'kidmax285@gmail.com',
            ],
            [
                'first_name' => 'Kid',
                'last_name' => 'Max',
                'email_verified_at' => now(),
                'password' => Hash::make('demo1234'),
            ]
        );

        if ($user->wasRecentlyCreated) {
            info('Admin user created successfully.');
        } else {
            info('Admin user already exists.');
        }

        return self::SUCCESS;
    }
}
