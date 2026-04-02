<?php

declare(strict_types=1);

namespace Modules\Core\Console;

use Exception;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Modules\Ecommerce\Traits\ENVSetupTrait;
use Symfony\Component\HttpKernel\Exception\HttpException;

use function Laravel\Prompts\info;

#[Signature('durrbar:test-mail-send')]
#[Description('Send a mail for credentials check')]
class TestMailSendCommand extends Command
{
    use ENVSetupTrait;

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        // Check if the .env file exists
        $this->CheckENVExistOrNot();
        try {
            $this->sendEmail();
        } catch (Exception $e) {
            throw new HttpException(400, 'Opsss! Something is wrong in your mail configuration. Please check again.');
        }

        return self::SUCCESS;
    }

    protected function sendEmail(): void
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
