<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookingSuccessMail extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    public function build()
    {
        return $this->subject('ACAGMS - Booking Confirmation & QR Pass')
                    ->view('emails.booking_success')
                    ->with([
                        'visitorName' => $this->booking->visitor_name,
                        'qrToken' => $this->booking->qr_token,
                        'date' => $this->booking->booking_date,
                    ]);
    }
}