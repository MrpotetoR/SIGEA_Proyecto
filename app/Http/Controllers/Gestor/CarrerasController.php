<?php

namespace App\Http\Controllers\Gestor;

use App\Http\Controllers\Admin\ReauthController;
use App\Http\Controllers\Controller;
use App\Models\AsignacionCarreraLog;
use App\Models\Carrera;
use App\Models\GestorEscolar;
use App\Services\NotificacionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CarrerasController extends Controller
{
    public function __construct(private NotificacionService $notificaciones) {}

    public function index(Request $request)
    {
        $carreras = Carrera::misCarreras()
            ->withCount('alumnos', 'materias')
            ->when($request->tipo_periodo, fn($q, $v) => $q->where('tipo_periodo', $v))
            ->get();

        $puedeAsignar  = $this->puedeAsignarCarreras();
        $sinAsignar    = collect();
        $candidatos    = collect();
        $miGestorId    = auth()->user()->gestorEscolar?->id_personal;

        if ($puedeAsignar) {
            // Carreras sin gestor asignado en personal_carrera.
            $sinAsignar = Carrera::whereDoesntHave('personalAsignado')
                ->orderByDesc('id_carrera')
                ->get();

            $candidatos = $this->candidatosParaAsignar();
        }

        return view('gestor.carreras.index', compact(
            'carreras', 'puedeAsignar', 'sinAsignar', 'candidatos', 'miGestorId'
        ));
    }

    public function create()
    {
        $puedeAsignar = $this->puedeAsignarCarreras();
        $candidatos   = $puedeAsignar ? $this->candidatosParaAsignar() : collect();
        $miGestor     = auth()->user()->gestorEscolar; // null si admin
        $miGestorId   = $miGestor?->id_personal;
        $miCarreras   = $miGestor?->carreras()->count() ?? 0;

        return view('gestor.carreras.create', compact(
            'puedeAsignar', 'candidatos', 'miGestorId', 'miCarreras'
        ));
    }

    public function store(Request $request)
    {
        // Reauth obligatorio: cualquier creación de carrera requiere haber
        // confirmado contraseña recientemente (grace period de 10 min).
        if (!ReauthController::tieneGracePeriod('crear_carrera')) {
            return back()
                ->withInput()
                ->with('error', 'Debes confirmar tu contraseña para crear una carrera.');
        }

        $puedeAsignar = $this->puedeAsignarCarreras();

        $reglas = [
            'nombre_carrera'                  => 'required|string|max:120',
            'clave_carrera'                   => 'required|string|max:20|unique:carrera,clave_carrera',
            'rvoe'                            => ['nullable', 'string', 'max:50', 'regex:/^[A-Za-z0-9\-\/]+$/'],
            'area_academica'                  => 'required|in:' . implode(',', array_keys(Carrera::AREAS_ACADEMICAS)),
            'tipo_periodo'                    => 'required|in:' . implode(',', array_keys(Carrera::TIPOS_PERIODO)),
            'horas_servicio_social_default'   => 'required|integer|min:0|max:2000',
        ];

        // Solo se valida el campo de asignación si el usuario puede ver el combo.
        if ($puedeAsignar) {
            $reglas['gestor_asignado_id'] = ['nullable', Rule::exists('gestores_escolares', 'id_personal')];
        }

        $request->validate($reglas, [
            'tipo_periodo.required' => 'Debes seleccionar si la carrera es por cuatrimestre o semestre.',
            'tipo_periodo.in'       => 'El tipo de periodo seleccionado no es válido.',
            'rvoe.regex'            => 'El RVOE solo admite letras, números, guion (-) y diagonal (/).',
        ]);

        $gestorAsignadoId = null;
        if ($puedeAsignar && $request->filled('gestor_asignado_id')) {
            // Validar que el gestor elegido aún tenga cupo (< 4 carreras).
            $gestor = GestorEscolar::find($request->gestor_asignado_id);
            if (!$gestor || !$gestor->puedeAgregarCarrera()) {
                return back()->withInput()->withErrors([
                    'gestor_asignado_id' => 'El gestor seleccionado ya alcanzó el límite de '
                                            . GestorEscolar::MAX_CARRERAS . ' carreras.',
                ]);
            }
            $gestorAsignadoId = $gestor->id_personal;
        }

        $carrera = DB::transaction(function () use ($request, $gestorAsignadoId) {
            $carrera = Carrera::create($request->only(
                'nombre_carrera', 'clave_carrera', 'rvoe', 'area_academica', 'tipo_periodo', 'horas_servicio_social_default'
            ));

            if ($gestorAsignadoId) {
                $carrera->personalAsignado()->attach($gestorAsignadoId);
            }

            return $carrera;
        });

        // Si quedó sin asignar (gestor sin permiso especial o "sin asignar" elegido),
        // notificar a quienes sí pueden asignar carreras.
        if (!$gestorAsignadoId) {
            $this->notificarCarreraPendiente($carrera);
        }

        $mensaje = $gestorAsignadoId
            ? ($gestorAsignadoId === auth()->user()->gestorEscolar?->id_personal
                ? 'Carrera creada y asignada a ti.'
                : 'Carrera creada y asignada al gestor elegido.')
            : 'Carrera creada. Se notificó a los gestores con permiso para asignarla.';

        return redirect()->route('gestor.carreras.index')->with('success', $mensaje);
    }

    public function show(Carrera $carrera)
    {
        $carrera->load('alumnos', 'materias');
        return view('gestor.carreras.show', compact('carrera'));
    }

    public function edit(Carrera $carrera)
    {
        return view('gestor.carreras.edit', compact('carrera'));
    }

    public function update(Request $request, Carrera $carrera)
    {
        $request->validate([
            'nombre_carrera'                => 'required|string|max:120',
            'rvoe'                          => ['nullable', 'string', 'max:50', 'regex:/^[A-Za-z0-9\-\/]+$/'],
            'area_academica'                => 'required|in:' . implode(',', array_keys(Carrera::AREAS_ACADEMICAS)),
            'horas_servicio_social_default' => 'required|integer|min:0|max:2000',
        ], [
            'rvoe.regex' => 'El RVOE solo admite letras, números, guion (-) y diagonal (/).',
        ]);

        // tipo_periodo es inmutable una vez creada la carrera — el modelo también lo blinda.
        $carrera->update($request->only('nombre_carrera', 'rvoe', 'area_academica', 'horas_servicio_social_default'));

        return redirect()->route('gestor.carreras.index')->with('success', 'Carrera actualizada.');
    }

    public function destroy(Carrera $carrera)
    {
        $carrera->delete();
        return redirect()->route('gestor.carreras.index')->with('success', 'Carrera eliminada.');
    }

    /**
     * Asigna una carrera a un gestor escolar (o la deja sin asignar).
     * Acción sensible: requiere permiso especial + reauth + motivo + log.
     */
    public function asignar(Request $request, Carrera $carrera)
    {
        // Autorización: solo admin o gestor con permiso especial.
        abort_unless($this->puedeAsignarCarreras(), 403,
            'No tienes permisos para asignar carreras a otros gestores.');

        // Reauth obligatorio.
        if (!ReauthController::tieneGracePeriod('asignar_carrera')) {
            return back()->with('error',
                'Debes confirmar tu contraseña para asignar carreras. Repite la acción.');
        }

        $request->validate([
            'gestor_destino_id'    => ['nullable', Rule::exists('gestores_escolares', 'id_personal')],
            'motivo'               => ['required', Rule::in(array_keys(AsignacionCarreraLog::MOTIVOS))],
            'motivo_personalizado' => 'nullable|string|max:32|required_if:motivo,otro',
        ], [
            'motivo.required'                => 'Debes seleccionar un motivo para registrar la acción.',
            'motivo_personalizado.required_if' => 'Cuando el motivo es "Otro" debes especificarlo (máx 32 caracteres).',
        ]);

        // Estado antes del cambio: ¿la carrera ya tenía gestor?
        $gestorActual   = $carrera->personalAsignado()->first();
        $gestorActualId = $gestorActual?->id_personal;
        $gestorDestino  = $request->filled('gestor_destino_id')
            ? GestorEscolar::find($request->gestor_destino_id)
            : null;

        // Validar cupo del destino si lo hay (y no es el mismo que ya lo tenía).
        if ($gestorDestino && $gestorDestino->id_personal !== $gestorActualId
            && !$gestorDestino->puedeAgregarCarrera()) {
            return back()->withErrors([
                'gestor_destino_id' => 'El gestor seleccionado ya alcanzó el límite de '
                                     . GestorEscolar::MAX_CARRERAS . ' carreras.',
            ]);
        }

        // Determinar el tipo de acción.
        if ($gestorDestino && !$gestorActualId) {
            $accion = 'asignar';
        } elseif ($gestorDestino && $gestorActualId && $gestorDestino->id_personal !== $gestorActualId) {
            $accion = 'reasignar';
        } elseif (!$gestorDestino && $gestorActualId) {
            $accion = 'desasignar';
        } else {
            // Sin cambio real.
            return back()->with('error', 'No hay cambio que registrar.');
        }

        DB::transaction(function () use ($carrera, $gestorDestino, $request, $accion) {
            $carrera->personalAsignado()->detach();
            if ($gestorDestino) {
                $carrera->personalAsignado()->attach($gestorDestino->id_personal);
            }

            AsignacionCarreraLog::create([
                'user_id'              => auth()->id(),
                'gestor_afectado_id'   => $gestorDestino?->id_personal,
                'id_carrera'           => $carrera->id_carrera,
                'accion'               => $accion,
                'motivo'               => $request->motivo,
                'motivo_personalizado' => $request->motivo === 'otro' ? $request->motivo_personalizado : null,
                'ip'                   => $request->ip(),
                'user_agent'           => substr((string) $request->userAgent(), 0, 255),
                'created_at'           => now(),
            ]);
        });

        $mensaje = match ($accion) {
            'asignar'    => "Carrera \"{$carrera->nombre_carrera}\" asignada a {$gestorDestino->nombre_completo}.",
            'reasignar'  => "Carrera \"{$carrera->nombre_carrera}\" reasignada a {$gestorDestino->nombre_completo}.",
            'desasignar' => "Carrera \"{$carrera->nombre_carrera}\" desasignada.",
        };

        return back()->with('success', $mensaje);
    }

    /**
     * ¿El usuario actual puede asignar carreras a otros gestores?
     * - Admin: siempre sí.
     * - Gestor con flag puede_asignar_carreras=true: sí.
     * - Otros: no.
     */
    private function puedeAsignarCarreras(): bool
    {
        $user = auth()->user();
        if ($user->hasRole('admin')) {
            return true;
        }
        return (bool) ($user->gestorEscolar?->puede_asignar_carreras);
    }

    /**
     * Gestores escolares activos que aún no han alcanzado el límite de carreras.
     * Devuelve la colección ordenada por nombre con conteo de carreras actuales.
     */
    private function candidatosParaAsignar()
    {
        return GestorEscolar::withCount('carreras')
            ->whereHas('user', fn($q) => $q->where('activo', true))
            ->having('carreras_count', '<', GestorEscolar::MAX_CARRERAS)
            ->orderBy('apellidos')
            ->orderBy('nombre')
            ->get();
    }

    /**
     * Notifica a admins y a gestores con permiso especial que hay una carrera
     * pendiente de asignación.
     */
    private function notificarCarreraPendiente(Carrera $carrera): void
    {
        // Destinatarios: admins + gestores con permiso especial (todos activos).
        $admins = \App\Models\User::role('admin')->where('activo', true)->get();

        $gestoresEspeciales = \App\Models\User::role('gestor_escolar')
            ->where('activo', true)
            ->whereHas('gestorEscolar', fn($q) => $q->where('puede_asignar_carreras', true))
            ->get();

        $destinatarios = $admins->merge($gestoresEspeciales)->unique('id');
        if ($destinatarios->isEmpty()) return;

        $creador = auth()->user()->name;
        $mensaje = "{$creador} agregó una nueva carrera \"{$carrera->nombre_carrera}\" "
                 . "para que la asignes a un personal de gestor escolar y la administre.";

        $this->notificaciones->enviarMasivo(
            $destinatarios,
            'carrera_pendiente_asignar',
            'Nueva carrera para asignar',
            $mensaje,
            [
                'icono' => 'academic-cap',
                'color' => 'amber',
                'url'   => '/gestor-escolar/carreras#sin-asignar',
            ]
        );
    }
}
