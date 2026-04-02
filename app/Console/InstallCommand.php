<?php

declare(strict_types=1);

namespace Modules\Core\Console;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Modules\Ecommerce\Database\Seeders\DurrbarSeeder;
use Modules\Role\Enums\Permission as UserPermission;
use Modules\Role\Enums\Role as UserRole;
use Modules\Settings\Database\Seeders\SettingsSeeder;
use Modules\Settings\Models\Settings;
use PDO;
use PDOException;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\error;
use function Laravel\Prompts\info;
use function Laravel\Prompts\table;
use function Laravel\Prompts\text;

#[Signature('durrbar:install')]
#[Description('Installing Durrbar Dependencies')]
class InstallCommand extends Command
{
    protected DurrbarVerification $verification;

    private array $appData;

    public function handle(): int
    {
        // $this->verification = new DurrbarVerification();
        // $shouldGetLicenseKeyFromUser = $this->shouldGetLicenseKey();
        // if ($shouldGetLicenseKeyFromUser) {
        //     $this->getLicenseKey();
        //     $description = $this->appData['description'] ?? '';
        //     $this->components->info("Thank you for using " . APP_NOTICE_DOMAIN . ". $description");
        // } else {
        //     $this->appData = $this->verification->jsonSerialize();
        // }

        info('Installing Durrbar Dependencies...');
        info('Do you want to migrate Tables?');
        info('If you have already run this command or migrated tables then be aware.');
        info('Tt will erase all of your data.');

        info('Please use arrow key for navigation.');
        if (confirm('Are you sure!')) {

            info('Migrating Tables Now....');

            $this->call('migrate:fresh');

            info('Tables Migration completed.');

            if (confirm('Do you want to seed dummy data?')) {
                $this->call('durrbar:seed');
                $this->call('db:seed', [
                    '--class' => DurrbarSeeder::class,
                ]);
            }

            info('Importing required settings...');

            $this->call(
                'db:seed',
                [
                    '--class' => SettingsSeeder::class,
                ]
            );

            info('Settings import is completed.');
        } else {
            info('Do you want to seed dummy Settings data?');
            info('If "yes", then please follow next steps carefully.');
            if (confirm('Are you sure!')) {
                $this->call('durrbar:settings-seed');
            }
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        Permission::firstOrCreate(['name' => UserPermission::SuperAdmin->value]);
        Permission::firstOrCreate(['name' => UserPermission::Customer->value]);
        Permission::firstOrCreate(['name' => UserPermission::StoreOwner->value]);
        Permission::firstOrCreate(['name' => UserPermission::Staff->value]);

        $superAdminPermissions = [UserPermission::SuperAdmin->value, UserPermission::StoreOwner->value, UserPermission::Customer->value];
        $storeOwnerPermissions = [UserPermission::StoreOwner->value, UserPermission::Customer->value];
        $staffPermissions = [UserPermission::Staff->value, UserPermission::Customer->value];
        $customerPermissions = [UserPermission::Customer->value];

        Role::firstOrCreate(['name' => UserRole::SuperAdmin->value])->syncPermissions($superAdminPermissions);
        Role::firstOrCreate(['name' => UserRole::StoreOwner->value])->syncPermissions($storeOwnerPermissions);
        Role::firstOrCreate(['name' => UserRole::Staff->value])->syncPermissions($staffPermissions);
        Role::firstOrCreate(['name' => UserRole::Customer->value])->syncPermissions($customerPermissions);

        $this->call('durrbar:create-admin'); // creating Admin

        $this->call('durrbar:copy-files');

        $this->modifySettingsData();

        info('You need to configure your mail server for proper application performance.');
        info('Do you want to configure mail server.');
        $confirmed = confirm(
            label: 'Are you sure!',
            default: true,
            yes: 'Yes, I accept',
            no: 'No, I decline',
        );
        if ($confirmed) {
            $this->call('durrbar:mail-setup');
        } else {
            info('You can configuration by below command or manual process.');
            table(['Command', 'Details'], [['durrbar:mail-setup', 'Mail setup (mailtrap, mailgun, gmail)']]);
        }

        info('Everything is successful. Now clearing all cached...');
        $this->call('optimize:clear');
        info('Thank You.');

        return self::SUCCESS;
    }

    private function createDatabase(): void
    {
        $databaseName = config('database.connections.mysql.database');
        $servername = config('database.connections.mysql.host');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');

        try {
            $conn = new PDO("mysql:host=$servername", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Check if the database exists
            $query = "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$databaseName'";
            $stmt = $conn->query($query);
            $databaseExists = $stmt->fetch(PDO::FETCH_ASSOC);

            if (! $databaseExists) {
                // Create the database
                $createDatabaseQuery = "CREATE DATABASE $databaseName";
                $conn->exec($createDatabaseQuery);
                info("Database $databaseName created successfully.");
            }
            // else {
            //     $this->info("Database $databaseName already exists.");
            // }
        } catch (PDOException $e) {
            info('Connection failed: '.$e->getMessage());
        }
    }

    private function getLicenseKey(int $count = 0): bool
    {
        $message = 'Kindly enter a valid License Key or visit https://redq.io/pickbazar-laravel-ecommerce for a legitimate license key';
        if ($count < 1) {
            $message = 'Please Enter Your License Key.';
        }
        $licenseKey = text($message);
        $isValid = $this->licenseKeyValidator($licenseKey);
        if (! $isValid) {
            $count++;
            error('Invalid Licensing Key');
            $this->getLicenseKey($count);
        }

        return $isValid;
    }

    private function licenseKeyValidator(string $licenseKey): bool
    {
        $verification = $this->verification->verify($licenseKey);
        $this->appData = $verification->jsonSerialize();

        return $verification->getTrust();
    }

    private function shouldGetLicenseKey(): bool
    {
        $trust = empty($this->verification->getTrust());
        $env = config('app.env');
        if ($env === 'production') {
            return true;
        }
        if ($env === 'local' && $trust) {
            return true;
        }
        if ($env === 'development' && $trust) {
            return true;
        }

        return false;
    }

    private function modifySettingsData(): void
    {
        $language = request()['language'] ?? DEFAULT_LANGUAGE;
        Cache::flush();
        $settings = Settings::getData($language);
        $settings->update([
            'options' => [
                ...$settings->options,
                'app_settings' => [
                    'last_checking_time' => $this->appData['last_checking_time'],
                    'trust' => $this->appData['trust'],
                ],
            ],
        ]);
    }
}
