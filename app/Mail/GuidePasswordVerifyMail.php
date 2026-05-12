<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class GuidePasswordVerifyMail extends Mailable
{
    use Queueable, SerializesModels;

    public $guide;
    public $url;

    public function __construct($guide, $url)
    {
        $this->guide = $guide;
        $this->url = $url;
    }

    public function build()
    {
        return $this->subject('Security Verification - ' . config('app.name'))
                    ->markdown('emails.guides.verify-password');
    }
}