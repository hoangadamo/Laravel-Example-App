<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendOtpEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $email;
    protected $otp;
    protected $expirationTime;

    /**
     * Create a new job instance.
     */
    public function __construct($email, $otp, $expirationTime)
    {
        $this->email = $email;
        $this->otp = $otp;
        $this->expirationTime = $expirationTime;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Mail::send('mail.template', ['otp' => $this->otp, 'expirationTime' => $this->expirationTime], function ($message) {
                $message->to($this->email)
                    ->subject('Your OTP Code');
            });
        } catch (\Exception $e) {
            Log::error('Mail Sending Failed: ' . $e->getMessage());
        }
    }
}
