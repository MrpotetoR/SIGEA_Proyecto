<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CajaChicaLog;
use App\Models\User;
use App\Models\ValeCajaChica;
use Illuminate\Http\Request;

/**
 * Historial de auditoría del módulo Caja Chica (solo lectura).
 *
 * Accesible desde Panel Admin → Caja Chica → Historial.
 * Patrón replicado de HistorialAsignacionesController.
 */
class HistorialCajaChicaController extends Controller
{
    public function index(Request $request)
    {
        $logs = CajaChicaLog::with(['usuario', 'vale', 'fondo', 'gestorAfectado'])
            ->when($request->user_id,
                fn($q, $v) => $q->where('user_id', $v))
            ->when($request->accion,
                fn($q, $v) => $q->where('accion', $v))
            ->when($request->motivo,
                fn($q, $v) => $q->where('motivo', $v))
            ->when($request->vale_folio,
                fn($q, $v) => $q->whereHas('vale', fn($qv) => $qv->where('folio', 'like', "%{$v}%")))
            ->when($request->desde,
                fn($q, $v) => $q->whereDate('created_at', '>=', $v))
            ->when($request->hasta,
                fn($q, $v) => $q->whereDate('created_at', '<=', $v))
            ->when($request->con_evidencia === '1',
                fn($q) => $q->whereNotNull('evidencia_path'))
            ->orderByDesc('created_at')
            ->paginate(25)
            ->withQueryString();

        // Listas para filtros
        $usuariosQueActuan = User::whereHas('roles',
                fn($q) => $q->whereIn('name', ['admin', 'gestor_escolar']))
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('admin.caja-chica.historial', compact('logs', 'usuariosQueActuan'));
    }
}
