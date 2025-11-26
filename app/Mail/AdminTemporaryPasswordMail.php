<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminTemporaryPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public string $tempPassword;

    public function __construct(User $user, string $tempPassword)
    {
        $this->user = $user;
        $this->tempPassword = $tempPassword;
    }

    public function build()
    {
        return $this->subject('Your Temporary Admin Password')
                    ->view('emails.admin-temp-password');
    }
}
