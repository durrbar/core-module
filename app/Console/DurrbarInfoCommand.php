<?php

namespace Modules\Core\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Modules\Ecommerce\Traits\ENVSetupTrait;

use function Laravel\Prompts\info;
use function Laravel\Prompts\table;

class DurrbarInfoCommand extends Command
{
    use ENVSetupTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'durrbar:help';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Durrbar command information';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Check if the .env file exists
        $this->CheckENVExistOrNot();
        try {
            // Read the current .env content
            $envFilePath = base_path('.env');
            $envContent = File::get($envFilePath);
            $targetKeys = ['APP_NAME', 'APP_ENV', 'APP_DEBUG', 'APP_URL', 'APP_VERSION', 'APP_SERVICE', 'APP_NOTICE_DOMAIN', 'DUMMY_DATA_PATH']; // Add the keys you want to display
            info('Basic application information.');
            $this->existingKeyValueInENV($targetKeys, $envContent);

            info('Available Durrbar Command');

            table(['Command', 'Details'], [
                ['durrbar:install', 'Installing Durrbar application'],
                ['durrbar:env-setup', 'Setup necessary config in .env file'],
                ['durrbar:database-setup', 'Setup MySQL database in .env file'],
                ['durrbar:mail-setup', 'Mail server setup (mailtrap, mailgun, gmail)'],
                ['durrbar:mailchimp-newsletter', 'Mailchimp newsletter setup in .env file'],
                ['durrbar:frontend-setup', 'Frontend URL setup (admin & shop)'],
                ['durrbar:aws-setup', 'AWS (bucket) setup'],
                ['durrbar:create-admin', 'Create an admin user'],
                ['durrbar:default-language-setup', 'Setup default language in .env file'],
                ['durrbar:open-ai-setup', 'Setup OpenAI in .env file'],
                ['durrbar:otp-gateway-setup', 'OTP SMS gateway (Twilio or MessageBird) setup in .env file'],
                ['durrbar:queue-setup', 'Setup queue connection in .env file. (e.g. database or sync)'],
                ['durrbar:seed', 'Import Demo Data'],
                ['durrbar:settings-seed', 'Import Settings Data'],
                ['durrbar:translation-enable', 'Enable translation settings in .env file (true/false)'],
                ['durrbar:test-mail-send', 'Send an email for credentials check'],
            ]);

            $this->info("'durrbar:env-setup' command has some Quick Access Key");

            table(['Quick Access Key', 'Details'], [
                ['mail', 'Mail server setup (mailtrap, mailgun, gmail)'],
                ['database', 'Setup MySQL database in .env file'],
                ['newsletter', 'Mailchimp newsletter setup in .env file'],
                ['frontend-connection', 'Frontend URL setup (admin & shop)'],
                ['aws', 'AWS (bucket) setup'],
                ['default-language', 'Setup default language in .env file'],
                ['open-ai', 'Setup OpenAI in .env file'],
                ['otp', 'OTP SMS gateway (Twilio or MessageBird) setup in .env file'],
                ['queue-connection', 'Setup queue connection in .env file. (e.g. database or sync)'],
                ['translation-enable', 'Enable translation settings in .env file (true/false)'],
                ['test-mail', 'Send an email for credentials check'],
            ]);

            table(['The command looks like:'], [
                ['durrbar:env-setup mail'],
            ]);

        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }
}
