<?php

namespace Modules\Core\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Modules\Role\Enums\Permission as UserPermission;
use Modules\Role\Enums\Role as UserRole;
use Modules\User\Models\User;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\error;
use function Laravel\Prompts\info;
use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

class AdminCreateCommand extends Command
{
    protected $signature = 'durrbar:create-admin';

    protected $description = 'Create an admin user.';

    public function handle()
    {
        try {

            if (confirm('Do you want to create an admin?')) {

                info('Provide admin credentials info to create an admin user for you.');
                $name = text(label: 'Enter admin name', required: 'Admin Name is required');

                // Manually validate the email input
                do {
                    $email = text(label: 'Enter admin email', required: 'Admin Email is required');
                    if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        info('Invalid email address format. Please enter a valid email.');
                    } else {
                        // Break out of the loop if the email is valid
                        break;
                    }
                } while (true);

                do {
                    $password = password(label: 'Enter your admin password', required: 'Password is required');
                    $confirmPassword = password(label: 'Enter your password again', required: 'Confirm Password is required');
                    if ($password !== $confirmPassword) {
                        info('Passwords do not match. Please try again.');
                    }
                } while ($password !== $confirmPassword);

                info('Please wait, Creating an admin profile for you...');
                $validator = Validator::make(
                    [
                        'name' => $name,
                        'email' => $email,
                        'password' => $password,
                        'confirmPassword' => $confirmPassword,
                    ],
                    [
                        'name' => 'required|string',
                        'email' => 'required|email|unique:users,email',
                        'password' => 'required',
                        'confirmPassword' => 'required|same:password',
                    ]
                );
                if ($validator->fails()) {
                    info('User not created. See error messages below:');
                    foreach ($validator->errors()->all() as $error) {
                        error($error);
                    }

                    return;
                }
                $user = User::create([
                    'name' => $name,
                    'email' => $email,
                    'password' => Hash::make($password),
                ]);
                $user->email_verified_at = now()->timestamp;
                $user->save();
                $user->givePermissionTo(
                    [
                        UserPermission::SUPER_ADMIN,
                        UserPermission::STORE_OWNER,
                        UserPermission::CUSTOMER,
                    ]
                );
                $user->assignRole(UserRole::SUPER_ADMIN);

                info('User Creation Successful!');
            }
        } catch (\Exception $e) {
            error($e->getMessage());
        }
    }
}
