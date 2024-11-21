<?php

namespace App\Mail;

use App\Models\SaudiApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Address;

class SaudiApplicationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public SaudiApplication $application,
        public ?string $fromAddress = null // Make fromAddress optional
    ) {
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        $fromAddress = $this->fromAddress ?? env('MAIL_FROM_ADDRESS'); // Use default if not provided

        return new Envelope(
            from: new Address($fromAddress, $this->application->cPerson), // Use contact person's name
            subject: 'New Application Submission',
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            view: 'mail.saudi-application-email', // Corresponding view file
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}
