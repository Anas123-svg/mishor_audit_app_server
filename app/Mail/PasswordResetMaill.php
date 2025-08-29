<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordResetMaill extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $resetCode;

    public function __construct($user, $resetCode)
    {
        $this->user = $user;
        $this->resetCode = $resetCode;
    }

    public function build()
    {
        return $this->subject('Password Reset Request')
                    ->view('emails.password-reset')
                    ->with([
                        'name' => $this->user->name,
                        'resetCode' => $this->resetCode,
                    ]);
    }
}
