<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookingConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;
    public $messageContent;
    public $refNo;
    public $formattedPickupDate;

    /**
     * Create a new message instance.
     *
     * @param array $booking
     * @param string $messageContent
     * @param string $refNo
     * @param string $formattedPickupDate
     */
    public function __construct($booking, $messageContent, $refNo, $formattedPickupDate)
    {
        $this->booking = $booking;
        $this->messageContent = $messageContent;
        $this->refNo = $refNo;
        $this->formattedPickupDate = $formattedPickupDate;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Booking Confirmation')
                    ->view('emails.booking_confirmation')
                    ->with([
                        'booking' => $this->booking,
                        'messageContent' => $this->messageContent,
                        'refNo' => $this->refNo,
                        'formattedPickupDate' => $this->formattedPickupDate,
                    ]);
    }
}
