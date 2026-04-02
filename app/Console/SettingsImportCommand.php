<?php

declare(strict_types=1);

namespace Modules\Core\Console;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Modules\Settings\Database\Seeders\SettingsSeeder;

use function Laravel\Prompts\info;

#[Signature('durrbar:settings-import')]
#[Description('Import Settings Data')]
class SettingsImportCommand extends Command
{
    public function handle(): int
    {
        if (DB::table('settings')->first()) {
            info('Previous settings was kept. Thanks!');
        } else {
            info('Importing dummy settings...');

            $this->call('db:seed', [
                '--class' => SettingsSeeder::class,
            ]);

            info('Settings were imported successfully');
        }

        return self::SUCCESS;
    }
}
