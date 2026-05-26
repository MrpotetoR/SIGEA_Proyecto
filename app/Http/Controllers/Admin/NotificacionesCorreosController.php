<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminCorreoNotificacion;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/**
 * CRUD de correos adicionales del administrador actual para recibir
 * notificaciones del módulo Caja Chica (saldo bajo, reposición pendiente).
 *
 * Cada admin gestiona SUS PROPIOS correos extras (máx 3). Estos correos
 * reciben copia de las notificaciones enviadas a su correo principal.
 */
class NotificacionesCorreosController extends Controller
{
    public function index(Request $request)
    {
        $correos = $request->user()
            ->correosAdicionales()
            ->orderByDesc('activo')
            ->orderBy('email')
            ->get();

        $max     = AdminCorreoNotificacion::MAX_POR_ADMIN;
        $usados  = $correos->count();
        $libres  = max(0, $max - $usados);

        return view('admin.caja-chica.correos', compact('correos', 'max', 'usados', 'libres'));
    }

    public function store(Request $request)
    {
        $admin = $request->user();

        // Validar antes de evaluar cupo (para mensajes de error coherentes).
        $request->validate([
            'email'              => 'required|email|max:150',
            'nombre_destinatario' => 'nullable|string|max:100',
        ]);

        // No permitir duplicados (mismo admin, mismo email).
        if ($admin->correosAdicionales()->where('email', $request->email)->exists()) {
            throw ValidationException::withMessages([
                'email' => 'Ya tienes registrado este correo.',
            ]);
        }

        // No permitir el correo principal del propio admin (es redundante).
        if (strcasecmp($request->email, $admin->email) === 0) {
            throw ValidationException::withMessages([
                'email' => 'No agregues tu correo principal — ya recibe las notificaciones automáticamente.',
            ]);
        }

        // Validar cupo (máx 3).
        if ($admin->correosAdicionales()->count() >= AdminCorreoNotificacion::MAX_POR_ADMIN) {
            throw ValidationException::withMessages([
                'email' => 'Ya tienes ' . AdminCorreoNotificacion::MAX_POR_ADMIN . ' correos adicionales (máximo). Elimina uno primero.',
            ]);
        }

        $admin->correosAdicionales()->create([
            'email'               => $request->email,
            'nombre_destinatario' => $request->nombre_destinatario,
            'activo'              => true,
        ]);

        return redirect()->route('admin.caja-chica.correos.index')
            ->with('success', "Correo agregado. Recibirá notificaciones de Caja Chica.");
    }

    public function toggle(Request $request, AdminCorreoNotificacion $correo)
    {
        // Solo puede modificar los suyos.
        if ($correo->admin_user_id !== $request->user()->id) {
            abort(403);
        }

        $correo->update(['activo' => !$correo->activo]);
        return redirect()->route('admin.caja-chica.correos.index')
            ->with('success', $correo->activo ? 'Correo reactivado.' : 'Correo pausado (no recibirá notificaciones).');
    }

    public function destroy(Request $request, AdminCorreoNotificacion $correo)
    {
        if ($correo->admin_user_id !== $request->user()->id) {
            abort(403);
        }

        $correo->delete();
        return redirect()->route('admin.caja-chica.correos.index')
            ->with('success', 'Correo eliminado.');
    }
}
