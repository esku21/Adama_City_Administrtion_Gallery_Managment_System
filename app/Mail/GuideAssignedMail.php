<?php

namespace App\Mail;

use App\Models\Guide;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class GuideAssignedMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Public properties are automatically available in the Blade view.
     */
    public $guide;
    public $password;

    /**
     * Create a new message instance.
     * * @param Guide $guide
     * @param string $password
     */
    public function __construct(Guide $guide, $password)
    {
        $this->guide = $guide;
        $this->password = $password;
    }

    /**
     * Get the message envelope (Subject line).
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your ACAGMS Guide Credentials',
        );
    }

    /**
     * Get the message content definition.
     * Points to: resources/views/emails/guides/guide_assigned.blade.php
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.guides.guide_assigned', 
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