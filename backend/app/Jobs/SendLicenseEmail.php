<?php

namespace App\Jobs;

use App\Mail\NewLicenseInvitation;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendLicenseEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $temporaryPassword;

    public function __construct(User $user, string $temporaryPassword)
    {
        $this->user = $user;
        $this->temporaryPassword = $temporaryPassword;
        $this->onQueue('emails');
    }

    public function handle()
    {
        try {
            Mail::to($this->user->email)
                ->send(new NewLicenseInvitation($this->user, $this->temporaryPassword));
            
            logger()->info('License welcome email sent', [
                'user_email' => $this->user->email,
                'enterprise_name' => $this->user->enterprise->name
            ]);
        } catch (\Exception $e) {
            logger()->error('Failed to send license welcome email', [
                'user_email' => $this->user->email,
                'enterprise_name' => $this->user->enterprise->name,
                'error' => $e->getMessage()
            ]);
        }
    }
    
    public function failed(\Throwable $exception)
    {
        logger()->critical('Failed to send license email after multiple attempts', [
            'user_email' => $this->user->email,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);
    }
}