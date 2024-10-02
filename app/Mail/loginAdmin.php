<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class loginAdmin extends Mailable
{
    use Queueable, SerializesModels;
    public $ip;
    public $nombre;
    public $email;
    public $fecha;
    public $aplicacion;
    public $ciudad;
    public $pais;
    public $host;
    /**
     * Create a new message instance.
     */
    public function __construct($datos)
    {
        //$this->ip = $ip;
        $this->nombre = $datos['nombre']; // Al pasar los datos en un array desde el controlador, debemos pasarlo con [''] y no con '->', que es para objetos
        $this->email = $datos['email'];
        $this->fecha = $datos['fecha'];
        $this->ip = $datos['ip'];
        $this->aplicacion = $datos['aplicacion'];
        $this->ciudad = $datos['ciudad'];
        $this->pais = $datos['pais'];
        $this->host = $datos['host'];
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('easyshop.notifications@gmail.com', 'EasyShop Notifications'),
            subject: 'Nuevo inicio de sesión con permisos de administración',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mails.loginadmin',
            
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
