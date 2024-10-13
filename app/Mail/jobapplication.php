<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Address;

class jobapplication extends Mailable
{
    use Queueable, SerializesModels;

    public $nombre;
    public $job;

    /**
     * Create a new message instance.
     */
    public function __construct($datos)
    {
        $this->nombre = $datos['nombre'];
        $this->job = $datos['job'];
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('easyshop.notifications@gmail.com', 'EasyShop Notifications'),
            subject: "Tu aplicación a la oferta de empleo {$this->job}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mails.jobapplication',
        );
    }

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
