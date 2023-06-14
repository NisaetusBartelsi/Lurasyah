<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class ResetLinkEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $resetLink;

    /**
     * Create a new message instance.
     *
     * @param  User  $user
     * @param  string  $resetLink
     * @return void
     */
    public function __construct(User $user, string $resetLink)
    {
        $this->user = $user;
        $this->resetLink = $resetLink;

    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('forgotpass.reset-password')
            ->subject('Reset Password')
            ->with([
                'resetLink' => 'https://www.google.co.id',
            ]);
    }
}
