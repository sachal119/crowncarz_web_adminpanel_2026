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
    public $selectedCustomerName;
    public $pdfContent;

    public function __construct($customers, $fromDate, $toDate, $selectedCustomerName = null, $pdfContent = null)
    {
        $this->customers = $customers;
        $this->fromDate = $fromDate;
        $this->toDate = $toDate;
        $this->selectedCustomerName = $selectedCustomerName;
        $this->pdfContent = $pdfContent;
    }

    public function build()
    {
        $formattedFrom = date('d-M-Y', strtotime($this->fromDate));
        $formattedTo   = date('d-M-Y', strtotime($this->toDate));
        $fileName      = "CrownCarz_Invoice_{$this->fromDate}_to_{$this->toDate}.pdf";

        $mail = $this->subject("Crown Carz — Customer Invoice & Statement ({$formattedFrom} to {$formattedTo})")
                     ->view('emails.customer_report')
                     ->with([
                         'customers'            => $this->customers,
                         'from'                 => $this->fromDate,
                         'to'                   => $this->toDate,
                         'selectedCustomerName' => $this->selectedCustomerName,
                     ]);

        if (!empty($this->pdfContent)) {
            $mail->attachData($this->pdfContent, $fileName, [
                'mime' => 'application/pdf',
            ]);
        }

        return $mail;
    }
}
