<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewLicenseInvitation extends Mailable
{
    use Queueable, SerializesModels;

    protected User $user;
    protected string $temporaryPassword;

    /**
     * Create a new message instance.
     *
     * @param User $user
     * @param string $temporaryPassword
     */
    public function __construct(User $user, string $temporaryPassword)
    {
        $this->user = $user;
        $this->temporaryPassword = $temporaryPassword;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Bienvenue sur Orchestra - Votre licence a été activée')
            ->markdown('emails.new-license-invitation', [
                'user' => $this->user,
                'temporaryPassword' => $this->temporaryPassword,
                'enterprise' => $this->user->enterprise
            ]);
    }
}