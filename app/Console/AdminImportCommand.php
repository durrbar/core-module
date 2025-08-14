<?php

namespace Modules\Core\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Modules\Role\Enums\Permission as UserPermission;
use Modules\Role\Enums\Role as UserRole;
use Modules\User\Models\User;
use Illuminate\Support\Str;

use function Laravel\Prompts\error;
use function Laravel\Prompts\info;

class AdminImportCommand extends Command
{
    protected $signature = 'durrbar:import-admin';

    protected $description = 'Create an admin user.';

    public function handle()
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
                'shop_id' => null
            ]);

            // $user->givePermissionTo(
            //     [
            //         UserPermission::SUPER_ADMIN,
            //         UserPermission::STORE_OWNER,
            //         UserPermission::CUSTOMER,
            //     ]
            // );

            // $user->assignRole(UserRole::SUPER_ADMIN);

            info('User Creation Successful!');
        }
    }
}
