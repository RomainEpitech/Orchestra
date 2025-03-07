<?php

namespace App\Jobs;

use App\Mail\PasswordChangedMail;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ProcessPasswordChange implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $shouldRevokeTokens;
    protected $ipAddress;
    protected $userAgent;

    /**
     * Create a new job instance.
     *
     * @param User $user
     * @param bool $shouldRevokeTokens
     * @param string|null $ipAddress
     * @param string|null $userAgent
     */
    public function __construct(User $user, bool $shouldRevokeTokens = true, string $ipAddress, string $userAgent)
    {
        $this->user = $user;
        $this->shouldRevokeTokens = $shouldRevokeTokens;
        $this->ipAddress = $ipAddress;
        $this->userAgent = $userAgent;
        $this->onQueue('security');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('User password changed', [
            'user_uuid' => $this->user->uuid,
            'user_email' => $this->user->email,
            'enterprise_uuid' => $this->user->enterprise_uuid,
            'ip_address' => $this->ipAddress,
            'user_agent' => $this->userAgent,
            'timestamp' => now()->toIso8601String()
        ]);

        if ($this->shouldRevokeTokens) {
            $currentToken = request()->bearerToken();
            if ($currentToken) {
                $this->user->tokens()->where('token', '!=', hash('sha256', $currentToken))->delete();
            } else {
                $this->user->tokens()->delete();
            }
        }

        try {
            Mail::to($this->user->email)
                ->send(new PasswordChangedMail($this->user));
            
            Log::info('Password change email sent', [
                'user_email' => $this->user->email
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send password change email', [
                'user_email' => $this->user->email,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Failed to process password change notification', [
            'user_uuid' => $this->user->uuid,
            'user_email' => $this->user->email,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);
    }
}