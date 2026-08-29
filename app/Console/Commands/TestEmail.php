<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:test {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a test email to the specified address';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $email = $this->argument('email');
        $this->info("Sending test email to {$email}...");

        try {
            \Illuminate\Support\Facades\Mail::raw('This is a test email from ChamaHub to verify email integration is working.', function ($message) use ($email) {
                $message->to($email)
                        ->subject('Test Email from ChamaHub');
            });
            $this->info('Email sent successfully!');
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Failed to send email. Error: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
