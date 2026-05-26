<?php

namespace App\Services;

use App\Models\CajaChicaLog;
use App\Models\CajaSolicitante;
use App\Models\FondoCajaChica;
use App\Models\User;
use App\Models\ValeCajaChica;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Lógica atómica del módulo Caja Chica.
 *
 * Toda operación que modifica saldo del fondo se ejecuta dentro de una
 * transacción con lockForUpdate sobre la fila del fondo, para prevenir
 * race conditions entre autorizaciones simultáneas.
 *
 * Cada operación registra una entrada en caja_chica_log con:
 *  - quién (user_id + ip + user_agent)
 *  - qué (acción + monto_antes / monto_despues)
 *  - por qué (motivo + motivo_personalizado)
 *  - con qué soporte (evidencia_path)
 */
class CajaChicaService
{
    /**
     * Crea un vale en estado "solicitada" + registra solicitante para autocompletado.
     * No descuenta saldo todavía — eso pasa al autorizar.
     *
     * Si el monto excede el saldo o el tope individual, el vale se crea igual
     * (Opción B confirmada por el usuario): el bloqueo ocurre al intentar autorizar.
     */
    public function crearVale(array $datos, User $solicitante, Request $request): ValeCajaChica
    {
        $fondo = FondoCajaChica::actual();

        return DB::transaction(function () use ($datos, $solicitante, $request, $fondo) {
            // Registrar uso del solicitante (alta o increment).
            CajaSolicitante::registrarUso($datos['solicitante_nombre']);

            $vale = ValeCajaChica::create([
                'id_fondo'            => $fondo->id_fondo,
                'solicitante_nombre'  => trim($datos['solicitante_nombre']),
                'concepto'            => $datos['concepto'],
                'monto'               => $datos['monto'],
                'estatus'             => 'solicitada',
                'solicitado_por'      => $solicitante->id,
            ]);

            CajaChicaLog::create([
                'user_id'              => $solicitante->id,
                'vale_id'              => $vale->id_vale,
                'fondo_id'             => $fondo->id_fondo,
                'accion'               => 'crear_vale',
                'motivo'               => $datos['motivo'] ?? 'gasto_operativo',
                'motivo_personalizado' => $datos['motivo_personalizado'] ?? null,
                'monto_antes'          => null,
                'monto_despues'        => $vale->monto,
                'ip'                   => $request->ip(),
                'user_agent'           => substr((string) $request->userAgent(), 0, 255),
            ]);

            return $vale->fresh();
        });
    }

    /**
     * Edita un vale en estado "solicitada". Lanza ValidationException si no
     * es editable.
     */
    public function editarVale(ValeCajaChica $vale, array $datos, User $editor, Request $request): ValeCajaChica
    {
        if (!$vale->es_editable) {
            throw ValidationException::withMessages([
                'estatus' => 'El vale ya no es editable (solo en estado "solicitada").',
            ]);
        }

        return DB::transaction(function () use ($vale, $datos, $editor, $request) {
            $montoAntes = (float) $vale->monto;

            // Actualizar solicitante (registra uso si cambia el nombre)
            if (isset($datos['solicitante_nombre'])
                && trim($datos['solicitante_nombre']) !== $vale->solicitante_nombre) {
                CajaSolicitante::registrarUso($datos['solicitante_nombre']);
            }

            $vale->update([
                'solicitante_nombre' => trim($datos['solicitante_nombre']),
                'concepto'           => $datos['concepto'],
                'monto'              => $datos['monto'],
            ]);

            CajaChicaLog::create([
                'user_id'              => $editor->id,
                'vale_id'              => $vale->id_vale,
                'fondo_id'             => $vale->id_fondo,
                'accion'               => 'editar_vale',
                'motivo'               => $datos['motivo'] ?? 'ajuste_configuracion',
                'motivo_personalizado' => $datos['motivo_personalizado'] ?? null,
                'monto_antes'          => $montoAntes,
                'monto_despues'        => (float) $vale->monto,
                'ip'                   => $request->ip(),
                'user_agent'           => substr((string) $request->userAgent(), 0, 255),
            ]);

            return $vale->fresh();
        });
    }

    /**
     * Autoriza un vale. Bloquea la fila del fondo, valida saldo y tope
     * individual, descuenta y registra log.
     */
    public function autorizarVale(
        ValeCajaChica $vale,
        User $autorizador,
        array $datos,
        Request $request
    ): ValeCajaChica {
        if (!$vale->puedeTransicionarA('autorizada')) {
            throw ValidationException::withMessages([
                'estatus' => "El vale en estado '{$vale->estatus}' no se puede autorizar.",
            ]);
        }

        return DB::transaction(function () use ($vale, $autorizador, $datos, $request) {
            // Lock pesimista sobre la fila del fondo.
            $fondo = FondoCajaChica::lockForUpdate()->find($vale->id_fondo);
            $monto = (float) $vale->monto;

            // Validar saldo (Opción B: se permite crear pero NO autorizar sin saldo)
            if (!$fondo->tieneSaldoPara($monto)) {
                throw ValidationException::withMessages([
                    'monto' => sprintf(
                        'Saldo insuficiente. Saldo actual: $%s. Monto del vale: $%s. Repone el fondo primero.',
                        number_format((float) $fondo->saldo_actual, 2),
                        number_format($monto, 2)
                    ),
                ]);
            }

            // Validar tope individual.
            if ($fondo->excedeTopeIndividual($monto)) {
                throw ValidationException::withMessages([
                    'monto' => sprintf(
                        'El monto del vale ($%s) excede el tope individual configurado ($%s).',
                        number_format($monto, 2),
                        number_format((float) $fondo->tope_vale_individual, 2)
                    ),
                ]);
            }

            $saldoAntes = (float) $fondo->saldo_actual;
            $fondo->saldo_actual = $saldoAntes - $monto;
            $fondo->save();

            $vale->update([
                'estatus'         => 'autorizada',
                'autorizado_por'  => $autorizador->id,
                'autorizado_en'   => now(),
            ]);

            CajaChicaLog::create([
                'user_id'              => $autorizador->id,
                'vale_id'              => $vale->id_vale,
                'fondo_id'             => $fondo->id_fondo,
                'accion'               => 'autorizar_vale',
                'motivo'               => $datos['motivo'] ?? 'gasto_operativo',
                'motivo_personalizado' => $datos['motivo_personalizado'] ?? null,
                'monto_antes'          => $saldoAntes,
                'monto_despues'        => $fondo->saldo_actual,
                'evidencia_path'       => $datos['evidencia_path'] ?? null,
                'ip'                   => $request->ip(),
                'user_agent'           => substr((string) $request->userAgent(), 0, 255),
            ]);

            return $vale->fresh();
        });
    }

    /**
     * Rechaza un vale en estado "solicitada". No toca saldo.
     */
    public function rechazarVale(
        ValeCajaChica $vale,
        User $autorizador,
        array $datos,
        Request $request
    ): ValeCajaChica {
        if (!$vale->puedeTransicionarA('rechazada')) {
            throw ValidationException::withMessages([
                'estatus' => "El vale en estado '{$vale->estatus}' no se puede rechazar.",
            ]);
        }
        if (empty($datos['motivo_rechazo'])) {
            throw ValidationException::withMessages([
                'motivo_rechazo' => 'Debes indicar el motivo del rechazo.',
            ]);
        }

        return DB::transaction(function () use ($vale, $autorizador, $datos, $request) {
            $vale->update([
                'estatus'         => 'rechazada',
                'autorizado_por'  => $autorizador->id,
                'autorizado_en'   => now(),
                'motivo_rechazo'  => $datos['motivo_rechazo'],
            ]);

            CajaChicaLog::create([
                'user_id'              => $autorizador->id,
                'vale_id'              => $vale->id_vale,
                'fondo_id'             => $vale->id_fondo,
                'accion'               => 'rechazar_vale',
                'motivo'               => $datos['motivo'] ?? 'ajuste_configuracion',
                'motivo_personalizado' => $datos['motivo_personalizado'] ?? null,
                'monto_antes'          => null,
                'monto_despues'        => (float) $vale->monto,
                'evidencia_path'       => $datos['evidencia_path'] ?? null,
                'ip'                   => $request->ip(),
                'user_agent'           => substr((string) $request->userAgent(), 0, 255),
            ]);

            return $vale->fresh();
        });
    }

    /**
     * Cancela un vale. Si estaba autorizada/comprobada, devuelve saldo al fondo.
     * Si estaba solicitada, solo cambia estado.
     */
    public function cancelarVale(
        ValeCajaChica $vale,
        User $usuario,
        array $datos,
        Request $request
    ): ValeCajaChica {
        if (!$vale->puedeTransicionarA('cancelada')) {
            throw ValidationException::withMessages([
                'estatus' => "El vale en estado '{$vale->estatus}' no se puede cancelar.",
            ]);
        }

        $devolverSaldo = in_array($vale->estatus, ['autorizada', 'comprobada'], true);

        return DB::transaction(function () use ($vale, $usuario, $datos, $request, $devolverSaldo) {
            $saldoAntes  = null;
            $saldoDespues = null;

            if ($devolverSaldo) {
                $fondo = FondoCajaChica::lockForUpdate()->find($vale->id_fondo);
                $saldoAntes = (float) $fondo->saldo_actual;
                $fondo->saldo_actual = $saldoAntes + (float) $vale->monto;
                $fondo->save();
                $saldoDespues = $fondo->saldo_actual;
            }

            $vale->update([
                'estatus'        => 'cancelada',
                'cancelado_por'  => $usuario->id,
                'cancelado_en'   => now(),
            ]);

            CajaChicaLog::create([
                'user_id'              => $usuario->id,
                'vale_id'              => $vale->id_vale,
                'fondo_id'             => $vale->id_fondo,
                'accion'               => 'cancelar_vale',
                'motivo'               => $datos['motivo'] ?? 'ajuste_configuracion',
                'motivo_personalizado' => $datos['motivo_personalizado'] ?? null,
                'monto_antes'          => $saldoAntes,
                'monto_despues'        => $saldoDespues,
                'evidencia_path'       => $datos['evidencia_path'] ?? null,
                'ip'                   => $request->ip(),
                'user_agent'           => substr((string) $request->userAgent(), 0, 255),
            ]);

            return $vale->fresh();
        });
    }

    /**
     * Sube la factura y marca el vale como "comprobada" (cierre).
     * Una sola factura por vale, inmutable tras carga.
     */
    public function subirFactura(
        ValeCajaChica $vale,
        User $usuario,
        UploadedFile $file,
        array $datos,
        Request $request
    ): ValeCajaChica {
        if ($vale->tiene_factura) {
            throw ValidationException::withMessages([
                'factura' => 'Este vale ya tiene una factura cargada. No se puede modificar.',
            ]);
        }
        if (!$vale->puedeTransicionarA('comprobada')) {
            throw ValidationException::withMessages([
                'estatus' => "El vale en estado '{$vale->estatus}' no admite carga de factura.",
            ]);
        }

        return DB::transaction(function () use ($vale, $usuario, $file, $datos, $request) {
            $path = $file->store(
                'caja-chica/facturas/' . date('Y/m'),
                'public'
            );

            $vale->update([
                'estatus'             => 'comprobada',
                'factura_path'        => $path,
                'factura_subida_en'   => now(),
                'factura_subida_por'  => $usuario->id,
                'cerrado_por'         => $usuario->id,
                'cerrado_en'          => now(),
            ]);

            CajaChicaLog::create([
                'user_id'              => $usuario->id,
                'vale_id'              => $vale->id_vale,
                'fondo_id'             => $vale->id_fondo,
                'accion'               => 'subir_factura',
                'motivo'               => $datos['motivo'] ?? 'gasto_operativo',
                'motivo_personalizado' => $datos['motivo_personalizado'] ?? null,
                'monto_antes'          => null,
                'monto_despues'        => (float) $vale->monto,
                'evidencia_path'       => $datos['evidencia_path'] ?? null,
                'ip'                   => $request->ip(),
                'user_agent'           => substr((string) $request->userAgent(), 0, 255),
            ]);

            return $vale->fresh();
        });
    }
}
