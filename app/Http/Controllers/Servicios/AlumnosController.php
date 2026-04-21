<?php

namespace App\Http\Controllers\Servicios;

use App\Http\Controllers\Controller;
use App\Models\Alumno;
use App\Models\Carrera;
use App\Models\DocumentoAlumno;
use App\Models\HistorialBaja;
use App\Models\PadreTutor;
use App\Models\PagoCuatrimestre;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AlumnosController extends Controller
{
    private \App\Services\NotificacionService $notificaciones;

    public function __construct(\App\Services\NotificacionService $notificaciones)
    {
        $this->notificaciones = $notificaciones;
    }
    public function index(Request $request)
    {
        $alumnos = Alumno::with('carrera', 'user', 'pagosCuatrimestre')
            ->when($request->buscar, fn($q) =>
                $q->where('nombre', 'like', "%{$request->buscar}%")
                  ->orWhere('apellidos', 'like', "%{$request->buscar}%")
                  ->orWhere('matricula', 'like', "%{$request->buscar}%")
            )
            ->when($request->carrera_id, fn($q) => $q->where('id_carrera', $request->carrera_id))
            ->when($request->estatus, fn($q) => $q->where('estatus', $request->estatus))
            ->when($request->pago_estado, function ($q) use ($request) {
                $aprobadosSql = '(SELECT COALESCE(COUNT(*), 0) FROM pago_cuatrimestre WHERE pago_cuatrimestre.id_alumno = alumno.id_alumno AND pago_cuatrimestre.estatus = \'aprobado\')';
                $pendientesSql = '(SELECT COALESCE(COUNT(*), 0) FROM pago_cuatrimestre WHERE pago_cuatrimestre.id_alumno = alumno.id_alumno AND pago_cuatrimestre.estatus = \'pendiente\')';

                if ($request->pago_estado === 'revision') {
                    $q->whereRaw("$pendientesSql > 0");
                } elseif ($request->pago_estado === 'pagado') {
                    $q->whereRaw("$pendientesSql = 0")
                      ->whereRaw("$aprobadosSql >= alumno.cuatrimestre_actual");
                } elseif ($request->pago_estado === 'sin_pago') {
                    $q->whereRaw("$pendientesSql = 0")
                      ->whereRaw("$aprobadosSql < alumno.cuatrimestre_actual");
                }
            })
            ->orderBy('apellidos')
            ->paginate(20)->withQueryString();

        $carreras = Carrera::orderBy('nombre_carrera')->get();

        return view('servicios.alumnos.index', compact('alumnos', 'carreras'));
    }

    public function create()
    {
        $carreras = Carrera::orderBy('nombre_carrera')->get();
        $tutores = \App\Models\Docente::where('es_tutor', true)
            ->orderBy('apellidos')->orderBy('nombre')->get();
        return view('servicios.alumnos.create', compact('carreras', 'tutores'));
    }

    public function store(Request $request)
    {
        // Límite dinámico del periodo según la carrera seleccionada
        $carreraSel = Carrera::find($request->id_carrera);
        $maxPeriodos = $carreraSel?->max_periodos ?? 10;

        $request->validate([
            'nombre'              => ['required', 'string', 'max:80', 'regex:/^[\pL\s]+$/u'],
            'apellidos'           => ['required', 'string', 'max:100', 'regex:/^[\pL\s]+$/u'],
            'email'               => 'required|email|unique:users,email',
            'id_carrera'          => 'required|exists:carrera,id_carrera',
            'cuatrimestre_actual' => "required|integer|min:1|max:{$maxPeriodos}",
            'id_tutor'            => 'nullable|exists:docente,id_docente',

            // Padre / Tutor
            'padre.nombre'              => 'nullable|string|max:80',
            'padre.apellidos'           => 'nullable|string|max:100',
            'padre.email'               => 'nullable|email|max:150',
            'padre.telefono'            => 'nullable|string|max:20',
            'padre.telefono_emergencia' => 'nullable|string|max:20',
            'padre.ine'                 => 'nullable|file|mimes:pdf|max:5120',

            // Pagos
            'pagos'   => 'nullable|array',
            'pagos.*' => 'nullable|file|mimes:pdf|max:5120',

            // Documentos
            'documentos'   => 'nullable|array',
            'documentos.*' => 'nullable|file|mimes:pdf|max:5120',
        ], [
            'nombre.regex'    => 'El nombre solo debe contener letras y espacios.',
            'apellidos.regex' => 'Los apellidos solo deben contener letras y espacios.',
        ]);

        DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => "{$request->nombre} {$request->apellidos}",
                'email' => $request->email,
                'password' => bcrypt('sigea' . date('Y')),
                'activo' => true,
            ]);
            $user->assignRole('alumno');

            $matricula = $this->generarMatricula($request->id_carrera);

            $alumno = Alumno::create([
                'user_id' => $user->id,
                'id_carrera' => $request->id_carrera,
                'id_tutor' => $request->id_tutor,
                'matricula' => $matricula,
                'nombre' => $request->nombre,
                'apellidos' => $request->apellidos,
                'cuatrimestre_actual' => $request->cuatrimestre_actual,
                'estatus' => 'activo',
            ]);

            // Padre / Tutor
            $padre = $request->input('padre', []);
            if (!empty($padre['nombre']) || !empty($padre['apellidos'])) {
                $inePath = null;
                if ($request->hasFile('padre.ine')) {
                    $inePath = $request->file('padre.ine')->store("alumnos/{$alumno->id_alumno}/padre", 'public');
                }
                PadreTutor::create([
                    'id_alumno'           => $alumno->id_alumno,
                    'nombre'              => $padre['nombre'] ?? '',
                    'apellidos'           => $padre['apellidos'] ?? '',
                    'email'               => $padre['email'] ?? null,
                    'telefono'            => $padre['telefono'] ?? null,
                    'telefono_emergencia' => $padre['telefono_emergencia'] ?? null,
                    'ine_path'            => $inePath,
                ]);
            }

            // Pagos por cuatrimestre — validación secuencial (solo consecutivos desde 1°)
            if ($request->hasFile('pagos')) {
                $files = $request->file('pagos');
                ksort($files);
                $esperado = 1;
                foreach ($files as $cuatri => $file) {
                    if (!$file) continue;
                    if ((int) $cuatri !== $esperado) {
                        throw \Illuminate\Validation\ValidationException::withMessages([
                            "pagos.$cuatri" => "Los váuchers deben cargarse en orden consecutivo. Falta el {$esperado}° cuatrimestre.",
                        ]);
                    }
                    $path = $file->store("alumnos/{$alumno->id_alumno}/pagos", 'public');
                    PagoCuatrimestre::create([
                        'id_alumno'    => $alumno->id_alumno,
                        'cuatrimestre' => (int) $cuatri,
                        'baucher_path' => $path,
                        'estatus'      => 'aprobado',
                        'subido_por'   => auth()->id(),
                        'revisado_por' => auth()->id(),
                        'revisado_en'  => now(),
                    ]);
                    $esperado++;
                }
            }

            // Documentos
            if ($request->hasFile('documentos')) {
                foreach ($request->file('documentos') as $tipo => $file) {
                    if (!$file || !array_key_exists($tipo, DocumentoAlumno::TIPOS)) continue;
                    $path = $file->store("alumnos/{$alumno->id_alumno}/documentos", 'public');
                    DocumentoAlumno::create([
                        'id_alumno'    => $alumno->id_alumno,
                        'tipo'         => $tipo,
                        'archivo_path' => $path,
                    ]);
                }
            }
        });

        return redirect()->route('servicios.alumnos.index')->with('success', 'Alumno registrado correctamente.');
    }

    public function show(Alumno $alumno)
    {
        $alumno->load('carrera', 'tutor', 'user', 'inscripciones.grupo', 'servicioSocial', 'constancias',
            'padreTutor', 'pagosCuatrimestre', 'documentos');
        return view('servicios.alumnos.show', compact('alumno'));
    }

    public function edit(Alumno $alumno)
    {
        $alumno->load('padreTutor', 'documentos', 'carrera');
        $carreras = Carrera::orderBy('nombre_carrera')->get();
        $tutores = \App\Models\Docente::where('es_tutor', true)
            ->orderBy('apellidos')->orderBy('nombre')->get();
        return view('servicios.alumnos.edit', compact('alumno', 'carreras', 'tutores'));
    }

    public function update(Request $request, Alumno $alumno)
    {
        // Límite dinámico del periodo: si cambian de carrera, usa la nueva; si no, la del alumno.
        $carreraSel = Carrera::find($request->id_carrera) ?? $alumno->carrera;
        $maxPeriodos = $carreraSel?->max_periodos ?? 10;

        $request->validate([
            'nombre'              => ['required', 'string', 'max:80', 'regex:/^[\pL\s]+$/u'],
            'apellidos'           => ['required', 'string', 'max:100', 'regex:/^[\pL\s]+$/u'],
            'id_carrera'          => 'required|exists:carrera,id_carrera',
            'cuatrimestre_actual' => "required|integer|min:1|max:{$maxPeriodos}",
            'id_tutor'            => 'nullable|exists:docente,id_docente',

            'padre.nombre'              => 'nullable|string|max:80',
            'padre.apellidos'           => 'nullable|string|max:100',
            'padre.email'               => 'nullable|email|max:150',
            'padre.telefono'            => 'nullable|string|max:20',
            'padre.telefono_emergencia' => 'nullable|string|max:20',
            'padre.ine'                 => 'nullable|file|mimes:pdf|max:5120',

            'documentos'   => 'nullable|array',
            'documentos.*' => 'nullable|file|mimes:pdf|max:5120',
        ], [
            'nombre.regex'    => 'El nombre solo debe contener letras y espacios.',
            'apellidos.regex' => 'Los apellidos solo deben contener letras y espacios.',
        ]);

        DB::transaction(function () use ($request, $alumno) {
            $alumno->update($request->only('nombre', 'apellidos', 'id_carrera', 'cuatrimestre_actual', 'id_tutor'));

            // Padre / Tutor (upsert)
            $padre = $request->input('padre', []);
            if (!empty($padre['nombre']) || !empty($padre['apellidos']) || $request->hasFile('padre.ine')) {
                $existing = $alumno->padreTutor;
                $inePath = $existing?->ine_path;
                if ($request->hasFile('padre.ine')) {
                    if ($inePath) Storage::disk('public')->delete($inePath);
                    $inePath = $request->file('padre.ine')->store("alumnos/{$alumno->id_alumno}/padre", 'public');
                }
                PadreTutor::updateOrCreate(
                    ['id_alumno' => $alumno->id_alumno],
                    [
                        'nombre'              => $padre['nombre'] ?? ($existing->nombre ?? ''),
                        'apellidos'           => $padre['apellidos'] ?? ($existing->apellidos ?? ''),
                        'email'               => $padre['email'] ?? null,
                        'telefono'            => $padre['telefono'] ?? null,
                        'telefono_emergencia' => $padre['telefono_emergencia'] ?? null,
                        'ine_path'            => $inePath,
                    ]
                );
            }

            // Documentos (reemplazo)
            if ($request->hasFile('documentos')) {
                foreach ($request->file('documentos') as $tipo => $file) {
                    if (!$file || !array_key_exists($tipo, DocumentoAlumno::TIPOS)) continue;
                    $existing = DocumentoAlumno::where('id_alumno', $alumno->id_alumno)->where('tipo', $tipo)->first();
                    if ($existing) Storage::disk('public')->delete($existing->archivo_path);
                    $path = $file->store("alumnos/{$alumno->id_alumno}/documentos", 'public');
                    DocumentoAlumno::updateOrCreate(
                        ['id_alumno' => $alumno->id_alumno, 'tipo' => $tipo],
                        ['archivo_path' => $path, 'subido_en' => now()]
                    );
                }
            }
        });

        return redirect()->route('servicios.alumnos.show', $alumno)->with('success', 'Alumno actualizado.');
    }

    public function destroy(Alumno $alumno)
    {
        $alumno->user->update(['activo' => false]);
        $alumno->update(['estatus' => 'baja_definitiva']);
        return redirect()->route('servicios.alumnos.index')->with('success', 'Alumno dado de baja definitiva.');
    }

    public function subirBaucher(Request $request, Alumno $alumno)
    {
        $maxPeriodos = $alumno->carrera?->max_periodos ?? 10;
        $request->validate([
            'cuatrimestre' => "required|integer|min:1|max:{$maxPeriodos}",
            'baucher'      => 'required|file|mimes:pdf|max:5120',
        ]);

        $cuatri = (int) $request->cuatrimestre;

        // Regla secuencial: solo aprobados cuentan como completados
        $maxAprobado = $alumno->pagosCuatrimestre()
            ->where('estatus', 'aprobado')
            ->max('cuatrimestre') ?? 0;

        if ($cuatri !== $maxAprobado + 1) {
            return back()->with('error', "Solo puedes cargar el váucher del " . ($maxAprobado + 1) . "° cuatrimestre.");
        }

        // No se permite reemplazar uno aprobado
        if ($alumno->pagosCuatrimestre()->where('cuatrimestre', $cuatri)->where('estatus', 'aprobado')->exists()) {
            return back()->with('error', 'Este váucher ya fue cargado y aprobado.');
        }

        $path = $request->file('baucher')->store("alumnos/{$alumno->id_alumno}/pagos", 'public');
        PagoCuatrimestre::updateOrCreate(
            ['id_alumno' => $alumno->id_alumno, 'cuatrimestre' => $cuatri],
            [
                'baucher_path' => $path,
                'estatus'      => 'aprobado',
                'subido_por'   => auth()->id(),
                'revisado_por' => auth()->id(),
                'revisado_en'  => now(),
            ]
        );

        return back()->with('success', "Váucher del {$cuatri}° cuatrimestre cargado correctamente.");
    }

    public function registrarBaja(Request $request, Alumno $alumno)
    {
        $request->validate([
            'tipo_baja' => 'required|in:temporal,definitiva',
            'motivo' => 'required|string|max:500',
            'fecha_baja' => 'required|date',
        ]);

        HistorialBaja::create([
            'id_alumno' => $alumno->id_alumno,
            'autorizada_por' => auth()->id(),
            'tipo_baja' => $request->tipo_baja,
            'fecha_baja' => $request->fecha_baja,
            'motivo' => $request->motivo,
        ]);

        $alumno->update(['estatus' => "baja_{$request->tipo_baja}"]);

        return back()->with('success', 'Baja registrada.');
    }

    public function registrarReingreso(Request $request, Alumno $alumno)
    {
        $request->validate(['fecha_reingreso' => 'required|date']);

        $alumno->historialBajas()->latest('fecha_baja')->first()?->update([
            'fecha_reingreso' => $request->fecha_reingreso,
        ]);

        $alumno->update(['estatus' => 'activo']);

        return back()->with('success', 'Reingreso registrado.');
    }

    public function aprobarBaucher(PagoCuatrimestre $pago)
    {
        if (!$pago->estaPendiente()) {
            return back()->with('error', 'Este váucher ya fue revisado.');
        }

        $pago->update([
            'estatus'      => 'aprobado',
            'revisado_por' => auth()->id(),
            'revisado_en'  => now(),
            'comentario_rechazo' => null,
        ]);

        $alumno = $pago->alumno;
        $this->notificaciones->enviar(
            $alumno->user,
            'pago',
            'Váucher aprobado',
            "Tu váucher del {$pago->cuatrimestre}° cuatrimestre ha sido validado exitosamente.",
            ['icono' => 'clipboard-check', 'color' => 'green', 'url' => route('alumno.pagos')]
        );

        return back()->with('success', "Váucher del {$pago->cuatrimestre}° cuatrimestre aprobado.");
    }

    public function rechazarBaucher(Request $request, PagoCuatrimestre $pago)
    {
        $request->validate([
            'comentario_rechazo' => 'required|string|max:500',
        ]);

        if (!$pago->estaPendiente()) {
            return back()->with('error', 'Este váucher ya fue revisado.');
        }

        $pago->update([
            'estatus'             => 'rechazado',
            'revisado_por'        => auth()->id(),
            'revisado_en'         => now(),
            'comentario_rechazo'  => $request->comentario_rechazo,
        ]);

        $alumno = $pago->alumno;
        $this->notificaciones->enviar(
            $alumno->user,
            'pago',
            'Váucher rechazado — correcciones necesarias',
            "Tu váucher del {$pago->cuatrimestre}° cuatrimestre fue rechazado. Observaciones: {$request->comentario_rechazo}",
            ['icono' => 'clipboard-check', 'color' => 'red', 'url' => route('alumno.pagos')]
        );

        return back()->with('success', "Váucher del {$pago->cuatrimestre}° cuatrimestre rechazado. Se notificó al alumno.");
    }

    public function eliminarDocumento(DocumentoAlumno $documento)
    {
        if ($documento->archivo_path) {
            Storage::disk('public')->delete($documento->archivo_path);
        }
        $documento->delete();
        return back()->with('success', 'Documento eliminado. Puedes volver a cargarlo desde aquí.');
    }

    private function generarMatricula(int $carreraId): string
    {
        $carrera = Carrera::find($carreraId);
        $año = date('Y');
        $ultimo = Alumno::where('id_carrera', $carreraId)
            ->where('matricula', 'like', "{$carrera->clave_carrera}{$año}%")
            ->count();

        return strtoupper("{$carrera->clave_carrera}{$año}" . str_pad($ultimo + 1, 4, '0', STR_PAD_LEFT));
    }
}
