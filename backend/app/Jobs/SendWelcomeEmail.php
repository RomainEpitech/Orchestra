<?php

namespace App\Jobs;

use App\Mail\EnterpriseCreatedMail;
use App\Models\Enterprise;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendWelcomeEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $enterprise;
    protected $owner;
    protected $recoveryKey;

    public function __construct(Enterprise $enterprise, User $owner, string $recoveryKey)
    {
        $this->enterprise = $enterprise;
        $this->owner = $owner;
        $this->recoveryKey = $recoveryKey;
        $this->onQueue('emails');
    }

    public function handle()
    {
        Mail::to($this->owner->email)
            ->send(new EnterpriseCreatedMail(
                enterprise: $this->enterprise,
                owner: $this->owner,
                recoveryKey: $this->recoveryKey
            ));
    }
}