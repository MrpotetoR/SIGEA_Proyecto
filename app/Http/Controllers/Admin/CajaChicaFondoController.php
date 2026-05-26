<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\ReauthController;
use App\Http\Controllers\Controller;
use App\Models\CajaChicaLog;
use App\Models\FondoCajaChica;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Configuración del fondo único de Caja Chica.
 *
 * Solo el admin puede:
 *  - Definir/ajustar el monto_base objetivo.
 *  - Definir el tope_vale_individual (opcional, NULL = sin tope).
 *  - Configurar los umbrales del semáforo (umbral_verde, umbral_amarillo).
 *  - Reponer saldo (parcial o total) — endpoint separado: repone().
 *
 * Cada cambio:
 *  - Requiere reauth con grace period activo (acciones:
 *    configurar_tope_caja_chica / reponer_fondo).
 *  - Captura motivo (predefinido + libre opcional) y evidencia (imagen/PDF).
 *  - Deja huella en caja_chica_log con monto_antes/monto_despues.
 */
class CajaChicaFondoController extends Controller
{
    /** Mostrar el formulario de configuración (vista). */
    public function edit()
    {
        $fondo = FondoCajaChica::actual();

        // Últimos 10 movimientos sobre el fondo, para preview del historial.
        $ultimosMovimientos = CajaChicaLog::with('usuario')
            ->where('fondo_id', $fondo->id_fondo)
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return view('admin.caja-chica.fondo', compact('fondo', 'ultimosMovimientos'));
    }

    /**
     * Actualiza monto_base, tope individual y umbrales del semáforo.
     * No toca saldo_actual (eso se hace vía repone()).
     */
    public function update(Request $request)
    {
        $request->validate([
            'monto_base'           => 'required|numeric|min:0|max:9999999.99',
            'tope_vale_individual' => 'nullable|numeric|min:0|max:9999999.99',
            'umbral_verde'         => 'required|numeric|min:0|max:9999999.99',
            'umbral_amarillo'      => 'required|numeric|min:0|max:9999999.99|lt:umbral_verde',
            'motivo'               => 'required|string|in:' . implode(',', array_keys(CajaChicaLog::MOTIVOS)),
            'motivo_personalizado' => 'nullable|string|max:100|required_if:motivo,otro',
            'evidencia'            => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ], [
            'umbral_amarillo.lt' => 'El umbral amarillo debe ser MENOR al umbral verde.',
            'motivo_personalizado.required_if' => 'Especifica el motivo cuando seleccionas "Otro".',
        ]);

        // Reauth obligatoria para configurar tope/umbrales.
        if (!ReauthController::tieneGracePeriod('configurar_tope_caja_chica')) {
            throw ValidationException::withMessages([
                'reauth' => 'Necesitas confirmar tu contraseña antes de modificar el fondo.',
            ]);
        }

        $fondo = FondoCajaChica::actual();

        // Snapshot de valores previos para el log.
        $montoBaseAntes    = (float) $fondo->monto_base;
        $umbralVerdeAntes  = (float) $fondo->umbral_verde;
        $umbralAmarilloAntes = (float) $fondo->umbral_amarillo;

        // Guardar evidencia si llega.
        $evidenciaPath = null;
        if ($request->hasFile('evidencia')) {
            $evidenciaPath = $request->file('evidencia')->store(
                'caja-chica/evidencias/' . date('Y/m'),
                'public'
            );
        }

        DB::transaction(function () use (
            $request, $fondo, $montoBaseAntes, $umbralVerdeAntes, $umbralAmarilloAntes, $evidenciaPath
        ) {
            $fondo->update([
                'monto_base'           => $request->monto_base,
                'tope_vale_individual' => $request->tope_vale_individual,
                'umbral_verde'         => $request->umbral_verde,
                'umbral_amarillo'      => $request->umbral_amarillo,
                'configurado_por'      => $request->user()->id,
                'configurado_en'       => now(),
            ]);

            // Log del cambio del monto base (si aplica).
            if ((float) $request->monto_base !== $montoBaseAntes) {
                CajaChicaLog::create([
                    'user_id'              => $request->user()->id,
                    'fondo_id'             => $fondo->id_fondo,
                    'accion'               => 'configurar_tope',
                    'motivo'               => $request->motivo,
                    'motivo_personalizado' => $request->motivo_personalizado,
                    'monto_antes'          => $montoBaseAntes,
                    'monto_despues'        => $request->monto_base,
                    'evidencia_path'       => $evidenciaPath,
                    'ip'                   => $request->ip(),
                    'user_agent'           => substr((string) $request->userAgent(), 0, 255),
                ]);
            }

            // Log del cambio de umbrales (si aplica).
            $umbralesCambiaron = ((float) $request->umbral_verde !== $umbralVerdeAntes)
                || ((float) $request->umbral_amarillo !== $umbralAmarilloAntes);

            if ($umbralesCambiaron) {
                CajaChicaLog::create([
                    'user_id'              => $request->user()->id,
                    'fondo_id'             => $fondo->id_fondo,
                    'accion'               => 'configurar_umbrales',
                    'motivo'               => $request->motivo,
                    'motivo_personalizado' => $request->motivo_personalizado,
                    'monto_antes'          => $umbralAmarilloAntes,
                    'monto_despues'        => (float) $request->umbral_amarillo,
                    'evidencia_path'       => $evidenciaPath,
                    'ip'                   => $request->ip(),
                    'user_agent'           => substr((string) $request->userAgent(), 0, 255),
                ]);
            }
        });

        return redirect()->route('admin.caja-chica.fondo.edit')
            ->with('success', 'Configuración del fondo actualizada correctamente.');
    }

    /**
     * Reponer saldo de la Caja Chica (parcial o total).
     * Acción separada porque tiene semántica distinta al ajuste de configuración.
     */
    public function repone(Request $request)
    {
        $request->validate([
            'monto'                => 'required|numeric|min:0.01|max:9999999.99',
            'motivo'               => 'required|string|in:' . implode(',', array_keys(CajaChicaLog::MOTIVOS)),
            'motivo_personalizado' => 'nullable|string|max:100|required_if:motivo,otro',
            'evidencia'            => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ], [
            'motivo_personalizado.required_if' => 'Especifica el motivo cuando seleccionas "Otro".',
        ]);

        if (!ReauthController::tieneGracePeriod('reponer_fondo')) {
            throw ValidationException::withMessages([
                'reauth' => 'Necesitas confirmar tu contraseña antes de reponer el fondo.',
            ]);
        }

        $fondo = FondoCajaChica::actual();

        // No permitir que el saldo supere el monto_base con la reposición.
        $saldoFinal = (float) $fondo->saldo_actual + (float) $request->monto;
        if ($saldoFinal > (float) $fondo->monto_base) {
            throw ValidationException::withMessages([
                'monto' => sprintf(
                    'La reposición excedería el monto base. Saldo actual: $%s. Máximo a reponer: $%s.',
                    number_format((float) $fondo->saldo_actual, 2),
                    number_format(max(0, (float) $fondo->monto_base - (float) $fondo->saldo_actual), 2)
                ),
            ]);
        }

        $evidenciaPath = null;
        if ($request->hasFile('evidencia')) {
            $evidenciaPath = $request->file('evidencia')->store(
                'caja-chica/evidencias/' . date('Y/m'),
                'public'
            );
        }

        DB::transaction(function () use ($request, $fondo, $evidenciaPath) {
            $saldoAntes = (float) $fondo->saldo_actual;

            // Lock pesimista para evitar race conditions con autorizaciones simultáneas.
            $fondoLock = FondoCajaChica::lockForUpdate()->find($fondo->id_fondo);
            $fondoLock->saldo_actual = (float) $fondoLock->saldo_actual + (float) $request->monto;
            $fondoLock->save();

            CajaChicaLog::create([
                'user_id'              => $request->user()->id,
                'fondo_id'             => $fondo->id_fondo,
                'accion'               => 'reponer_fondo',
                'motivo'               => $request->motivo,
                'motivo_personalizado' => $request->motivo_personalizado,
                'monto_antes'          => $saldoAntes,
                'monto_despues'        => $fondoLock->saldo_actual,
                'evidencia_path'       => $evidenciaPath,
                'ip'                   => $request->ip(),
                'user_agent'           => substr((string) $request->userAgent(), 0, 255),
            ]);
        });

        return redirect()->route('admin.caja-chica.fondo.edit')
            ->with('success', sprintf('Saldo repuesto en $%s.', number_format((float) $request->monto, 2)));
    }
}
