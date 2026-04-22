<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Correo de recuperacion de contrasena.
 *
 * Envia un codigo de 6 digitos al usuario para verificar su identidad
 * antes de permitirle establecer una nueva contrasena.
 */
class CodigoRecuperacionPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $codigo;
    public string $nombreUsuario;
    public int $minutosExpiracion;

    public function __construct(string $codigo, string $nombreUsuario = 'usuario', int $minutosExpiracion = 15)
    {
        $this->codigo = $codigo;
        $this->nombreUsuario = $nombreUsuario;
        $this->minutosExpiracion = $minutosExpiracion;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'SIGEA — Codigo de recuperacion de contrasena',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.codigo-recuperacion',
            with: [
                'codigo' => $this->codigo,
                'nombreUsuario' => $this->nombreUsuario,
                'minutosExpiracion' => $this->minutosExpiracion,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
