<?php
namespace App\Mail;

use App\Models\User;
use Illuminate\Mail\Mailable;

class PasswordResetMail extends Mailable
{
    public $user;
    public $token;

    public function __construct(User $user, $token)
    {
        $this->user = $user;
        $this->token = $token;
    }

    public function build()
    {
        return $this->subject('Password Reset Instructions')
                    ->view('emails.password_reset')
                    ->with([
                        'user' => $this->user,
                        'token' => $this->token,
                    ]);
    }
}
