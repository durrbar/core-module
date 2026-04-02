<?php

declare(strict_types=1);

namespace Modules\Core\Console;

use Exception;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

use function Laravel\Prompts\error;
use function Laravel\Prompts\info;

#[Signature('durrbar:copy-files')]
#[Description('Copy necessary files')]
class CopyFilesCommand extends Command
{
    public function handle(): int
    {
        try {
            (new Filesystem())->ensureDirectoryExists(resource_path('views/emails'));

            info('Copying resources files...');

            (new Filesystem())->copyDirectory(__DIR__.'/../../stubs/resources/views/emails', resource_path('views/emails'));
            (new Filesystem())->copyDirectory(__DIR__.'/../../stubs/resources/views/pdf', resource_path('views/pdf'));
            (new Filesystem())->copyDirectory(__DIR__.'/../../stubs/resources/lang', resource_path('lang'));

            info('Installation Complete');
        } catch (Exception $e) {
            error($e->getMessage());

            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
