<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class KirimEmail extends Mailable
{
    use Queueable, SerializesModels;
    protected $otp;
    protected $user;

    /**
     * Create a new message instance.
     */
    public function __construct($otp, $user)
    {
        $this->otp = $otp;
        $this->user = $user;
    }

    public function build()
    {
        return $this->view('mail.KirimEmail')
            ->with([
                'otp' => $this->otp,
                'user' => $this->user, // Menambahkan variabel $user ke view
            ])

            ->subject('KirimEmail');
    }
    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Kirim Email',
        );
    }

    /**
     * Get the message content definition.
     */
    // public function content(): Content
    // {
    //     return new Content(
    //         view: 'view.name',
    //     );
    // }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
