<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class StaffCredentialsMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $staffName,
        public string $staffEmail,
        public string $temporaryPassword,
        public string $role,
        public string $loginUrl,
    ) {
    }

    public function build(): self
    {
        return $this->subject('Your Crown Carz staff account')
            ->view('emails.staff_credentials');
    }
}
