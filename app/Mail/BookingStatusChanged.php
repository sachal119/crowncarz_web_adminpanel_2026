<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookingStatusChanged extends Mailable
{
    use SerializesModels;

    public $booking;
    public $oldStatus;
    public $newStatus;

    public function __construct($booking, $oldStatus,$newStatus)
    {
        $this->booking   = $booking;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
        
    }

    public function build()
    {
        return $this->subject('Your Booking Status Has Been Updated')
                    ->view('emails.booking-status-changed');
    }
}
