<?php

namespace Modules\Core\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Modules\Settings\Database\Seeders\SettingsSeeder;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\info;
use function Laravel\Prompts\text;

class SettingsImportCommand extends Command
{
    protected $signature = 'durrbar:settings-import';

    protected $description = 'Import Settings Data';

    public function handle()
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
    }
}
