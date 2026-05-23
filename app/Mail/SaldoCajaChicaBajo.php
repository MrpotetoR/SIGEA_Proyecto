<?php

namespace App\Mail;

use App\Models\FondoCajaChica;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Notificación de saldo bajo en la Caja Chica.
 *
 * Se envía al admin (y sus correos adicionales) cuando faltan ≤ 3 días para
 * el fin de mes y el saldo está por debajo del monto base configurado.
 *
 * Disparado por el comando programado: php artisan caja-chica:revisar-saldo
 */
class SaldoCajaChicaBajo extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public FondoCajaChica $fondo,
        public int $diasParaFinMes,
        public string $nombreDestinatario,
    ) {}

    public function envelope(): Envelope
    {
        $faltante = number_format((float) $this->fondo->faltante_reponer, 2);
        return new Envelope(
            subject: "⚠ Caja Chica · Repone \${$faltante} antes de fin de mes — UDEA",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.caja-chica-saldo-bajo',
            with: [
                'fondo'              => $this->fondo,
                'diasParaFinMes'     => $this->diasParaFinMes,
                'nombreDestinatario' => $this->nombreDestinatario,
                'saldoActual'        => number_format((float) $this->fondo->saldo_actual, 2),
                'montoBase'          => number_format((float) $this->fondo->monto_base, 2),
                'faltante'           => number_format((float) $this->fondo->faltante_reponer, 2),
                'porcentaje'         => $this->fondo->porcentaje_saldo,
                'nivelSemaforo'      => $this->fondo->nivel_semaforo,
            ],
        );
    }
}
