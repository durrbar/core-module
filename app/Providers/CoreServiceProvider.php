<?php

namespace Modules\Core\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;
use Modules\Core\Console\AdminCreateCommand;
use Modules\Core\Console\AdminImportCommand;
use Modules\Core\Console\AWSSetupCommand;
use Modules\Core\Console\CopyFilesCommand;
use Modules\Core\Console\DatabaseSetupCommand;
use Modules\Core\Console\DefaultLanguageSetupCommand;
use Modules\Core\Console\DurrbarInfoCommand;
use Modules\Core\Console\DurrbarVerification;
use Modules\Core\Console\ENVSetupCommand;
use Modules\Core\Console\FrontendSetupCommand;
use Modules\Core\Console\ImportDemoData;
use Modules\Core\Console\InstallCommand;
use Modules\Core\Console\MailchimpNewsletterSetupCommand;
use Modules\Core\Console\MailSetupCommand;
use Modules\Core\Console\OTPGatewaySetupCommand;
use Modules\Core\Console\QueueConnectionSetupCommand;
use Modules\Core\Console\SettingsDataImporter;
use Modules\Core\Console\SettingsImportCommand;
use Modules\Core\Console\TestMailSendCommand;
use Modules\Core\Console\TranslationEnabledCommand;
use Modules\Core\Http\Resources\Resource;
use Nwidart\Modules\Traits\PathNamespace;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class CoreServiceProvider extends ServiceProvider
{
    use PathNamespace;

    protected string $name = 'Core';

    protected string $nameLower = 'core';

    /**
     * Boot the application events.
     */
    public function boot(): void
    {
        $this->registerCommands();
        $this->registerCommandSchedules();
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->loadMigrationsFrom(module_path($this->name, 'database/migrations'));

        Resource::withoutWrapping();

        if (File::exists(__DIR__.'/../Helpers/helpers.php')) {
            require __DIR__.'/../Helpers/helpers.php';
        }
        if (File::exists(__DIR__.'/../Helpers/ResourceHelpers.php')) {
            require __DIR__.'/../Helpers/ResourceHelpers.php';
        }
    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->app->register(EventServiceProvider::class);
        $this->app->register(RouteServiceProvider::class);

        $this->app->singleton(DurrbarVerification::class, function ($app) {
            return new DurrbarVerification();
        });

        $this->mergeConfigFrom(__DIR__.'/../Config/shop.php', 'shop');

        require_once __DIR__.'/../Config/constants.php';

        config([
            'auth' => File::getRequire(__DIR__.'/../Config/auth.php'),
            'broadcasting' => File::getRequire(__DIR__.'/../Config/broadcasting.php'),
            'cache' => File::getRequire(__DIR__.'/../Config/cache.php'),
            'cors' => File::getRequire(__DIR__.'/../Config/cors.php'),
            'fortify' => File::getRequire(__DIR__.'/../Config/fortify.php'),
            'graphiql' => File::getRequire(__DIR__.'/../Config/graphiql.php'),
            'graphql-playground' => File::getRequire(__DIR__.'/../Config/graphql-playground.php'),
            'laravel-omnipay' => File::getRequire(__DIR__.'/../Config/laravel-omnipay.php'),
            'media-library' => File::getRequire(__DIR__.'/../Config/media-library.php'),
            'newsletter' => File::getRequire(__DIR__.'/../Config/newsletter.php'),
            'paymongo' => File::getRequire(__DIR__.'/../Config/paymongo.php'),
            'paystack' => File::getRequire(__DIR__.'/../Config/paystack.php'),
            'permission' => File::getRequire(__DIR__.'/../Config/permission.php'),
            'reverb' => File::getRequire(__DIR__.'/../Config/reverb.php'),
            'sanctum' => File::getRequire(__DIR__.'/../Config/sanctum.php'),
            'scout' => File::getRequire(__DIR__.'/../Config/scout.php'),
            'services' => File::getRequire(__DIR__.'/../Config/services.php'),
            'sluggable' => File::getRequire(__DIR__.'/../Config/sluggable.php'),
            'sslcommerz' => File::getRequire(__DIR__.'/../Config/sslcommerz.php'),
        ]);
    }

    /**
     * Register commands in the format of Command::class
     */
    protected function registerCommands(): void
    {
        $this->commands([
            InstallCommand::class,
            AdminCreateCommand::class,
            AdminImportCommand::class,
            ImportDemoData::class,
            CopyFilesCommand::class,
            SettingsDataImporter::class,
            SettingsImportCommand::class,
            MailSetupCommand::class,
            AWSSetupCommand::class,
            FrontendSetupCommand::class,
            TranslationEnabledCommand::class,
            DefaultLanguageSetupCommand::class,
            QueueConnectionSetupCommand::class,
            OTPGatewaySetupCommand::class,
            MailchimpNewsletterSetupCommand::class,
            ENVSetupCommand::class,
            DatabaseSetupCommand::class,
            DurrbarInfoCommand::class,
            TestMailSendCommand::class,
        ]);
    }

    /**
     * Register command Schedules.
     */
    protected function registerCommandSchedules(): void
    {
        // $this->app->booted(function () {
        //     $schedule = $this->app->make(Schedule::class);
        //     $schedule->command('inspire')->hourly();
        // });
    }

    /**
     * Register translations.
     */
    public function registerTranslations(): void
    {
        $langPath = resource_path('lang/modules/'.$this->nameLower);

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, $this->nameLower);
            $this->loadJsonTranslationsFrom($langPath);
        } else {
            $this->loadTranslationsFrom(module_path($this->name, 'lang'), $this->nameLower);
            $this->loadJsonTranslationsFrom(module_path($this->name, 'lang'));
        }
    }

    /**
     * Register config.
     */
    protected function registerConfig(): void
    {
        $relativeConfigPath = config('modules.paths.generator.config.path');
        $configPath = module_path($this->name, $relativeConfigPath);

        if (is_dir($configPath)) {
            $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($configPath));

            foreach ($iterator as $file) {
                if ($file->isFile() && $file->getExtension() === 'php') {
                    $relativePath = str_replace($configPath.DIRECTORY_SEPARATOR, '', $file->getPathname());
                    $configKey = $this->nameLower.'.'.str_replace([DIRECTORY_SEPARATOR, '.php'], ['.', ''], $relativePath);
                    $key = ($relativePath === 'config.php') ? $this->nameLower : $configKey;

                    $this->publishes([$file->getPathname() => config_path($relativePath)], 'config');
                    $this->mergeConfigFrom($file->getPathname(), $key);
                }
            }
        }
    }

    /**
     * Register views.
     */
    public function registerViews(): void
    {
        $viewPath = resource_path('views/modules/'.$this->nameLower);
        $sourcePath = module_path($this->name, 'resources/views');

        $this->publishes([$sourcePath => $viewPath], ['views', $this->nameLower.'-module-views']);

        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), $this->nameLower);

        $componentNamespace = $this->module_namespace($this->name, $this->app_path(config('modules.paths.generator.component-class.path')));
        Blade::componentNamespace($componentNamespace, $this->nameLower);
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [];
    }

    private function getPublishableViewPaths(): array
    {
        $paths = [];
        foreach (config('view.paths') as $path) {
            if (is_dir($path.'/modules/'.$this->nameLower)) {
                $paths[] = $path.'/modules/'.$this->nameLower;
            }
        }

        return $paths;
    }
}
