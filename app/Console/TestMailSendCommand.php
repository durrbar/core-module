<?php

namespace Modules\Core\Console;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Modules\Ecommerce\Traits\ENVSetupTrait;
use Symfony\Component\HttpKernel\Exception\HttpException;

use function Laravel\Prompts\info;

class TestMailSendCommand extends Command
{
    use ENVSetupTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'durrbar:test-mail-send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a mail for credentials check';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Check if the .env file exists
        $this->CheckENVExistOrNot();
        $envFilePath = base_path('.env');
        $envContent = File::get($envFilePath);
        try {
            $this->sendEmail();
        } catch (Exception $e) {
            throw new HttpException(400, 'Opsss! Something is wrong in your mail configuration. Please check again.');
        }
    }

    protected function sendEmail()
    {
        $to = env('ADMIN_EMAIL');
        if ($to !== null && $to !== '') {
            $subject = 'Mail Configuration Completed';
            $message = 'Your mail configuration has been successfully completed.';

            Mail::raw($message, function ($mail) use ($to, $subject): void {
                $mail->to($to)->subject($subject);
            });

            info('An email has sent to your mail. Please check your email');
        } else {
            info('admin email missing in your env file!');

            return;
        }
    }
}
