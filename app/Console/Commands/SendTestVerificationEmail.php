<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerificationCodeMail;

class SendTestVerificationEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:send-test-verification {email} {--code=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a test verification (OTP) email to the given address';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $email = $this->argument('email');
        $code = $this->option('code') ?: rand(100000, 999999);

        try {
            Mail::to($email)->send(new VerificationCodeMail($code));
            $this->info("Verification code {$code} sent to {$email}");
            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Failed to send email: ' . $e->getMessage());
            return self::FAILURE;
        }
    }
}