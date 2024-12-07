<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class pedidotransferencia extends Mailable
{
    use Queueable, SerializesModels;

    public int $referencia;
    public string $nombre_cuenta;
    public string $banco;
    public string $iban;
    public string $bic_swift;
    public float $subtotal;
    public float $descuento;
    public string $tipo_descuento;
    public float $total;
    public array $productos;
    /**
     * Create a new message instance.
     */
    public function __construct($datos)
    {
        $this->referencia = $datos['referencia'];
        $this->nombre_cuenta = $datos['nombre_cuenta'];
        $this->banco = $datos['banco'];
        $this->iban = $datos['iban'];
        $this->bic_swift = $datos['bic_swift'];
        $this->subtotal = $datos['subtotal'];
        $this->descuento = $datos['descuento'];
        $this->tipo_descuento = $datos['tipo_descuento'];
        $this->total = $datos['total'];
        $this->productos = $datos['productos'];
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('easyshop.notifications@gmail.com', 'EasyShop Notifications'),
            subject: 'Tu pedido de EasyShop',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mails.pedidotransferencia',
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
