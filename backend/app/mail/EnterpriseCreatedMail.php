<?php

namespace App\Mail;

use App\Models\Enterprise;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EnterpriseCreatedMail extends Mailable implements ShouldQueue
{
    use SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public Enterprise $enterprise,
        public User $owner,
        public string $recoveryKey
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Confirmation de création de votre entreprise sur Orchestra',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.enterprise.created',
            with: [
                'enterpriseName' => $this->enterprise->name,
                'ownerName' => $this->owner->firstname . ' ' . $this->owner->lastname,
                'loginUrl' => config('app.url') . '/login',
                'recoveryKey' => $this->recoveryKey,
                'supportEmail' => config('mail.support_email', 'support@orchestra.com'),
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}