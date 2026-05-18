<?php

namespace App\Mail;

use App\Models\Pedido;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Correo enviado al alumno/docente cuando su pedido pasa a "Listo para recoger".
 *
 * Se dispara desde PedidosController@listoRecoger via Mail::to(...).
 * Tambien puede encolarse implementando ShouldQueue si en el futuro se quiere
 * envio asincrono.
 */
class PedidoListoParaRecogerMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Pedido $pedido,
        public string $ubicacion,
        public string $horario,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Tu pedido {$this->pedido->folio} está listo para recoger — UDEA",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.pedido-listo',
            with: [
                'pedido'    => $this->pedido,
                'ubicacion' => $this->ubicacion,
                'horario'   => $this->horario,
                'nombre'    => $this->pedido->usuario->name,
            ],
        );
    }
}
