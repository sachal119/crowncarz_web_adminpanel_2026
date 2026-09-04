<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CustomerReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public $customers;
    public $fromDate;
    public $toDate;

    public function __construct($customers, $fromDate, $toDate)
    {
        $this->customers = $customers;
        $this->fromDate = $fromDate;
        $this->toDate = $toDate;
    }

    public function build()
    {
        return $this->subject('CrownCarz — Customer Booking Report')
                    ->view('emails.customer_report')
                    ->with([
                        'customers' => $this->customers,
                        'from' => $this->fromDate,
                        'to' => $this->toDate,
                    ]);
    }
}
