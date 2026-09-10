<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TurnoverReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public $totals;
    public $fromDate;
    public $toDate;
    public $invoiceDate;

    /**
     * Create a new message instance.
     */
    public function __construct($totals, $fromDate, $toDate, $invoiceDate = null)
    {
        $this->totals = $totals;
        $this->fromDate = $fromDate;
        $this->toDate = $toDate;
        $this->invoiceDate = $invoiceDate ?? date('d M Y');
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('CrownCarz — Turnover Report (' . $this->fromDate . ' - ' . $this->toDate . ')')
                    ->view('emails.turnover_report')
                    ->with([
                        'totals' => $this->totals,
                        'from' => $this->fromDate,
                        'to' => $this->toDate,
                        'invoiceDate' => $this->invoiceDate,
                    ]);
    }
}
