<?php

namespace App\Console\Commands;

use App\Mail\SaldoCajaChicaBajo;
use App\Models\AdminCorreoNotificacion;
use App\Models\FondoCajaChica;
use App\Models\Notificacion;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Revisa el saldo de la Caja Chica y notifica al admin cuando faltan ≤ 3 días
 * para el fin de mes y el fondo no está completo.
 *
 * Programado en routes/console.php para correr diariamente a las 09:00.
 * También se puede ejecutar manualmente:
 *   php artisan caja-chica:revisar-saldo
 *   php artisan caja-chica:revisar-saldo --force   (ignora el filtro de 3 días)
 */
class RevisarSaldoCajaChica extends Command
{
    protected $signature = 'caja-chica:revisar-saldo
                            {--force : Ejecutar aunque no estemos a 3 días o menos del fin de mes}';

    protected $description = 'Notifica al admin si el saldo de Caja Chica está por debajo del monto base a 3 días o menos del fin de mes';

    public function handle(): int
    {
        $hoy = Carbon::today();
        $finDeMes = $hoy->copy()->endOfMonth();
        $diasRestantes = (int) $hoy->diffInDays($finDeMes, false);

        $this->info("Hoy: {$hoy->format('Y-m-d')} · Fin de mes: {$finDeMes->format('Y-m-d')} · Días restantes: {$diasRestantes}");

        // Solo notificar dentro de la ventana de 3 días (o si --force)
        if (!$this->option('force') && $diasRestantes > 3) {
            $this->line("Aún quedan {$diasRestantes} días — sin notificación (umbral: ≤ 3).");
            return self::SUCCESS;
        }

        $fondo = FondoCajaChica::actual();
        $faltante = (float) $fondo->faltante_reponer;

        if ($faltante <= 0) {
            $this->line("Fondo completo (\${$fondo->saldo_actual}/\${$fondo->monto_base}) — no requiere reposición.");
            return self::SUCCESS;
        }

        $this->warn("Falta reponer: \${$faltante}");

        // Recolectar admins activos
        $admins = User::role('admin')->where('activo', true)->get();
        if ($admins->isEmpty()) {
            $this->error('No hay administradores activos para notificar.');
            return self::FAILURE;
        }

        $totalCorreos = 0;
        $totalInternas = 0;

        foreach ($admins as $admin) {
            // Notificación interna en el sistema
            Notificacion::create([
                'user_id' => $admin->id,
                'tipo'    => 'caja_chica_saldo_bajo',
                'titulo'  => 'Reposición de Caja Chica pendiente',
                'mensaje' => sprintf(
                    'Faltan %d día(s) para el cierre y el fondo necesita $%s para volver al monto base.',
                    max(0, $diasRestantes),
                    number_format($faltante, 2)
                ),
                'icono'   => 'clipboard-check',
                'color'   => $diasRestantes <= 1 ? 'red' : 'amber',
                'url'     => route('admin.caja-chica.fondo.edit', [], false),
            ]);
            $totalInternas++;

            // Destinatarios: el admin + sus correos adicionales activos
            $destinatarios = [
                ['email' => $admin->email, 'nombre' => $admin->name],
            ];
            foreach ($admin->correosAdicionales()->activos()->get() as $extra) {
                $destinatarios[] = [
                    'email'  => $extra->email,
                    'nombre' => $extra->nombre_destinatario ?: $extra->email,
                ];
            }

            foreach ($destinatarios as $d) {
                try {
                    Mail::to($d['email'], $d['nombre'])->send(
                        new SaldoCajaChicaBajo($fondo, max(0, $diasRestantes), $d['nombre'])
                    );
                    $totalCorreos++;
                    $this->line("  ✓ Correo enviado a {$d['email']}");
                } catch (\Throwable $e) {
                    $this->error("  ✗ Error enviando a {$d['email']}: {$e->getMessage()}");
                    Log::error('CajaChica · fallo de envío de correo', [
                        'destino' => $d['email'],
                        'error'   => $e->getMessage(),
                    ]);
                }
            }
        }

        $this->info("Listo: {$totalInternas} notificación(es) interna(s) y {$totalCorreos} correo(s) enviados.");
        return self::SUCCESS;
    }
}
