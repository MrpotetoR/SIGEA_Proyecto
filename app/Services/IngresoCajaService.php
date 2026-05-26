<?php

namespace App\Services;

use App\Models\CobroTramite;
use App\Models\IngresoCajaGeneral;
use App\Models\PagoCuatrimestre;
use App\Models\Pedido;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Registro automático de ingresos en la Caja General.
 *
 * Cada operación es idempotente: si ya existe un ingreso vigente para la
 * referencia dada, no se crea uno duplicado. Esto previene que una
 * re-aprobación o un retry accidental genere registros dobles.
 *
 * Para cancelar/reversar un ingreso, usar `cancelar(...)` que marca la fila
 * con cancelado_en + motivo (sin borrarla), manteniendo trazabilidad.
 */
class IngresoCajaService
{
    /**
     * Registra el ingreso de una colegiatura cuando el gestor aprueba un baucher.
     *
     * El monto se toma de $pago->monto si está capturado; si no, de la tarifa
     * default en configuracion_institucional (clave colegiatura.monto_default).
     */
    public function registrarColegiatura(PagoCuatrimestre $pago, User $gestor): ?IngresoCajaGeneral
    {
        // Idempotencia: si ya existe ingreso vigente para este pago, no duplicar.
        $existente = IngresoCajaGeneral::vigentes()
            ->where('referencia_tipo', 'pago_cuatrimestre')
            ->where('referencia_id', $pago->id_pago)
            ->first();
        if ($existente) return $existente;

        $alumno = $pago->alumno;
        $monto = $pago->monto_efectivo;

        // Si el monto es 0 (no hay tarifa ni capturado), no registrar — sería ingreso nulo.
        if ($monto <= 0) {
            return null;
        }

        return DB::transaction(function () use ($pago, $alumno, $monto, $gestor) {
            return IngresoCajaGeneral::create([
                'tipo'            => 'colegiatura',
                'referencia_tipo' => 'pago_cuatrimestre',
                'referencia_id'   => $pago->id_pago,
                'concepto'        => sprintf(
                    'Colegiatura %s° — %s',
                    $pago->cuatrimestre,
                    $alumno?->nombre_completo ?? 'Alumno #' . $pago->id_alumno
                ),
                'monto'           => $monto,
                'alumno_id'       => $pago->id_alumno,
                'user_id'         => $gestor->id,
                'metodo_pago'     => 'transferencia', // baucher = transferencia
                'fecha_cobro'     => now(),
            ]);
        });
    }

    /**
     * Registra el ingreso de un pedido de tienda cuando se aprueba el pago.
     */
    public function registrarProducto(Pedido $pedido, User $gestor): ?IngresoCajaGeneral
    {
        $existente = IngresoCajaGeneral::vigentes()
            ->where('referencia_tipo', 'pedido')
            ->where('referencia_id', $pedido->id_pedido)
            ->first();
        if ($existente) return $existente;

        $monto = (float) $pedido->total;
        if ($monto <= 0) return null;

        $alumnoId = $pedido->usuario?->alumno?->id_alumno;

        return DB::transaction(function () use ($pedido, $monto, $gestor, $alumnoId) {
            return IngresoCajaGeneral::create([
                'tipo'            => 'producto',
                'referencia_tipo' => 'pedido',
                'referencia_id'   => $pedido->id_pedido,
                'concepto'        => sprintf(
                    'Pedido tienda %s — %s',
                    $pedido->folio,
                    $pedido->usuario?->name ?? 'Cliente'
                ),
                'monto'           => $monto,
                'alumno_id'       => $alumnoId,
                'user_id'         => $gestor->id,
                'metodo_pago'     => 'transferencia',
                'fecha_cobro'     => now(),
            ]);
        });
    }

    /**
     * Registra el ingreso de un cobro de trámite (kárdex, constancia, etc.).
     * Se invoca desde CobroTramiteController::store después de persistir el cobro.
     */
    public function registrarTramite(CobroTramite $cobro, User $usuario): IngresoCajaGeneral
    {
        return DB::transaction(function () use ($cobro, $usuario) {
            return IngresoCajaGeneral::create([
                'tipo'            => 'tramite',
                'referencia_tipo' => 'cobro_tramite',
                'referencia_id'   => $cobro->id_cobro,
                'concepto'        => sprintf(
                    '%s — %s',
                    $cobro->concepto_legible,
                    $cobro->alumno?->nombre_completo ?? 'Alumno'
                ),
                'monto'           => $cobro->monto,
                'alumno_id'       => $cobro->alumno_id,
                'user_id'         => $usuario->id,
                'metodo_pago'     => $cobro->metodo_pago,
                'fecha_cobro'     => $cobro->cobrado_en ?? now(),
            ]);
        });
    }

    /**
     * Registra un ingreso manual capturado por admin (sin referencia a otra
     * tabla del sistema). Útil para ingresos imprevistos o ajustes.
     */
    public function registrarManual(array $datos, User $admin): IngresoCajaGeneral
    {
        return DB::transaction(function () use ($datos, $admin) {
            return IngresoCajaGeneral::create([
                'tipo'            => 'otro',
                'referencia_tipo' => 'manual',
                'referencia_id'   => null,
                'concepto'        => $datos['concepto'],
                'monto'           => $datos['monto'],
                'alumno_id'       => $datos['alumno_id'] ?? null,
                'user_id'         => $admin->id,
                'metodo_pago'     => $datos['metodo_pago'] ?? 'efectivo',
                'fecha_cobro'     => $datos['fecha_cobro'] ?? now(),
            ]);
        });
    }

    /**
     * Marca un ingreso como cancelado (reversa). No borra el registro,
     * solo lo excluye de los reportes vigentes.
     */
    public function cancelar(IngresoCajaGeneral $ingreso, User $usuario, string $motivo): IngresoCajaGeneral
    {
        if ($ingreso->esta_cancelado) {
            return $ingreso; // idempotente
        }

        $ingreso->update([
            'cancelado_en'       => now(),
            'cancelado_por'      => $usuario->id,
            'motivo_cancelacion' => $motivo,
        ]);

        return $ingreso->fresh();
    }

    /**
     * Cancela el ingreso asociado a una referencia (pago/pedido/trámite).
     * Útil cuando se cancela el origen y queremos reversar automáticamente.
     */
    public function cancelarPorReferencia(string $referenciaTipo, int $referenciaId, User $usuario, string $motivo): ?IngresoCajaGeneral
    {
        $ingreso = IngresoCajaGeneral::vigentes()
            ->where('referencia_tipo', $referenciaTipo)
            ->where('referencia_id', $referenciaId)
            ->first();

        return $ingreso ? $this->cancelar($ingreso, $usuario, $motivo) : null;
    }
}
