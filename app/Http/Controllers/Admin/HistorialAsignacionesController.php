<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AsignacionCarreraLog;
use App\Models\Carrera;
use App\Models\GestorEscolar;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * Historial de auditoría de asignaciones de carrera. Solo lectura, accesible
 * desde el panel Admin → Gestores Escolares → "Historial de asignaciones".
 */
class HistorialAsignacionesController extends Controller
{
    public function index(Request $request)
    {
        $logs = AsignacionCarreraLog::with(['usuario', 'gestorAfectado', 'carrera'])
            ->when($request->user_id,
                fn($q, $v) => $q->where('user_id', $v))
            ->when($request->gestor_id,
                fn($q, $v) => $q->where('gestor_afectado_id', $v))
            ->when($request->carrera_id,
                fn($q, $v) => $q->where('id_carrera', $v))
            ->when($request->accion,
                fn($q, $v) => $q->where('accion', $v))
            ->when($request->motivo,
                fn($q, $v) => $q->where('motivo', $v))
            ->when($request->desde,
                fn($q, $v) => $q->whereDate('created_at', '>=', $v))
            ->when($request->hasta,
                fn($q, $v) => $q->whereDate('created_at', '<=', $v))
            ->orderByDesc('created_at')
            ->paginate(25)
            ->withQueryString();

        // Listas para filtros.
        $usuariosQueActuan = User::whereHas('roles',
                fn($q) => $q->whereIn('name', ['admin', 'gestor_escolar']))
            ->orderBy('name')
            ->get(['id', 'name']);

        $gestores = GestorEscolar::orderBy('apellidos')->orderBy('nombre')->get();
        $carreras = Carrera::orderBy('nombre_carrera')->get(['id_carrera', 'nombre_carrera', 'clave_carrera']);

        return view('admin.personal.historial', compact(
            'logs', 'usuariosQueActuan', 'gestores', 'carreras'
        ));
    }
}
