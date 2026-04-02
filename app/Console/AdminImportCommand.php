<?php

declare(strict_types=1);

namespace Modules\Core\Console;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use function Laravel\Prompts\info;

#[Signature('durrbar:import-admin')]
#[Description('Create an admin user.')]
class AdminImportCommand extends Command
{
    public function handle(): int
    {
        if (DB::table('users')->first()) {
            info('Previous users was kept. Thanks!');
        } else {
            info('Importing dummy settings...');
            DB::table('users')->insert([
                'id' => Str::uuid(),
                'first_name' => 'Kid',
                'last_name' => 'Max',
                'email' => 'kidmax285@gmail.com',
                'email_verified_at' => now(),
                'password' => '$2y$10$UVs.WftC2iIdLQsHz9Tbdu7OmUXG3P7wyjHvJqCunyJ7JE8ekyXr.',
                'is_active' => true,
                'shop_id' => null,
            ]);

            info('User Creation Successful!');
        }

        return self::SUCCESS;
    }
}
