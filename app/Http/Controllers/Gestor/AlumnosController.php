<?php

namespace App\Http\Controllers\Gestor;

use App\Http\Controllers\Controller;
use App\Models\Alumno;
use App\Models\BachilleratoPlan;
use App\Models\Carrera;
use App\Models\DocumentoAlumno;
use App\Models\HistorialBaja;
use App\Models\PadreTutor;
use App\Models\PagoCuatrimestre;
use App\Models\User;
use App\Support\ContextoEducativo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AlumnosController extends Controller
{
    private \App\Services\NotificacionService $notificaciones;
    private \App\Services\IngresoCajaService $ingresosCaja;

    public function __construct(
        \App\Services\NotificacionService $notificaciones,
        \App\Services\IngresoCajaService $ingresosCaja
    ) {
        $this->notificaciones = $notificaciones;
        $this->ingresosCaja   = $ingresosCaja;
    }
    public function index(Request $request)
    {
        $esBachi   = ContextoEducativo::actual() === ContextoEducativo::BACHILLERATO;
        $modalidad = $request->input('modalidad', 'escolarizado'); // tab por defecto en bachi

        // Tabs de modalidad (solo bachillerato).
        $tabs = null;
        $planEnTab = null;
        if ($esBachi) {
            // IDs de planes por tipo_periodo para filtrar la query.
            $idsEscolarizado    = BachilleratoPlan::where('tipo_periodo', 'semestre')->pluck('id_plan_bachillerato')->all();
            $idsNoEscolarizado  = BachilleratoPlan::where('tipo_periodo', 'cuatrimestre')->pluck('id_plan_bachillerato')->all();

            // Contadores por tab (respetan los filtros de busqueda/estatus/pago).
            $base = fn() => Alumno::query()
                ->when($request->buscar, fn($q) =>
                    $q->where(fn($w) =>
                        $w->where('nombre', 'like', "%{$request->buscar}%")
                          ->orWhere('apellidos', 'like', "%{$request->buscar}%")
                          ->orWhere('id_alumno_publico', 'like', "%{$request->buscar}%")
                    )
                )
                ->when($request->estatus, fn($q) => $q->where('estatus', $request->estatus));

            $tabs = [
                'escolarizado'    => $base()->whereIn('id_plan_bachillerato', $idsEscolarizado)->count(),
                'no_escolarizado' => $base()->whereIn('id_plan_bachillerato', $idsNoEscolarizado)->count(),
                'todos'           => $base()->count(),
            ];

            $planEnTab = match ($modalidad) {
                'no_escolarizado' => $idsNoEscolarizado,
                'todos'           => null,
                default           => $idsEscolarizado,
            };
        }

        $alumnos = Alumno::with('carrera', 'planBachillerato', 'user', 'pagosCuatrimestre')
            ->when($request->buscar, fn($q) =>
                $q->where(fn($w) =>
                    $w->where('nombre', 'like', "%{$request->buscar}%")
                      ->orWhere('apellidos', 'like', "%{$request->buscar}%")
                      ->orWhere('id_alumno_publico', 'like', "%{$request->buscar}%")
                )
            )
            ->when(!$esBachi && $request->carrera_id, fn($q) => $q->where('id_carrera', $request->carrera_id))
            ->when($esBachi && $planEnTab !== null, fn($q) => $q->whereIn('id_plan_bachillerato', $planEnTab))
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

        $carreras = Carrera::misCarreras()->orderBy('nombre_carrera')->get();
        $carrera  = null;
        return view('gestor.alumnos.index', compact('alumnos', 'carreras', 'esBachi', 'tabs', 'modalidad'));
    }

    public function create()
    {
        $contexto = ContextoEducativo::actual();
        $esBachi  = $contexto === ContextoEducativo::BACHILLERATO;

        $carreras = $esBachi ? collect() : Carrera::misCarreras()->orderBy('nombre_carrera')->get();
        $planesBachi = $esBachi
            ? BachilleratoPlan::vigente()->orderBy('nombre_plan')->get()
            : collect();
        $tutores = \App\Models\Docente::where('es_tutor', true)
            ->orderBy('apellidos')->orderBy('nombre')->get();

        return view('gestor.alumnos.create', compact('carreras', 'planesBachi', 'tutores', 'contexto', 'esBachi'));
    }

    public function store(Request $request)
    {
        $contexto = ContextoEducativo::actual();
        $esBachi  = $contexto === ContextoEducativo::BACHILLERATO;

        // Limite dinamico del periodo:
        //   - Universidad: max_periodos de la carrera
        //   - Bachillerato: num_semestres del plan
        if ($esBachi) {
            $planSel = BachilleratoPlan::find($request->id_plan_bachillerato);
            $maxPeriodos = $planSel?->num_semestres ?? 6;
        } else {
            $carreraSel = Carrera::find($request->id_carrera);
            $maxPeriodos = $carreraSel?->max_periodos ?? 10;
        }

        // Reglas de pagos: cada período <= período-de-ingreso debe llegar como archivo.
        // Ej.: si alumno entra en 3°, son obligatorios pagos.1, pagos.2 y pagos.3.
        $periodoIngreso = (int) $request->input('cuatrimestre_actual', 0);
        $labelPeriodo = $esBachi
            ? (BachilleratoPlan::find($request->id_plan_bachillerato)?->label_periodo ?? 'periodo')
            : (Carrera::find($request->id_carrera)?->label_periodo ?? 'periodo');

        $reglas = [
            'nombre'              => ['required', 'string', 'max:80', 'regex:/^[\pL\s]+$/u'],
            'apellidos'           => ['required', 'string', 'max:100', 'regex:/^[\pL\s]+$/u'],
            'email'               => 'required|email|unique:users,email',
            // Universidad pide carrera; Bachillerato pide plan.
            'id_carrera'           => $esBachi ? 'nullable' : 'required|exists:carrera,id_carrera',
            'id_plan_bachillerato' => $esBachi ? 'required|exists:bachillerato_plan,id_plan_bachillerato' : 'nullable',
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

            // Documentos
            'documentos'   => 'nullable|array',
            'documentos.*' => 'nullable|file|mimes:pdf|max:5120',
        ];

        // Pagos 1..N: obligatorios, PDF, máx 5MB.
        $mensajes = [
            'nombre.regex'    => 'El nombre solo debe contener letras y espacios.',
            'apellidos.regex' => 'Los apellidos solo deben contener letras y espacios.',
        ];
        for ($i = 1; $i <= max($periodoIngreso, 1); $i++) {
            $reglas["pagos.{$i}"] = 'required|file|mimes:pdf|max:5120';
            $mensajes["pagos.{$i}.required"] = "El váucher del {$i}° {$labelPeriodo} es obligatorio.";
            $mensajes["pagos.{$i}.mimes"]    = "El váucher del {$i}° {$labelPeriodo} debe ser PDF.";
            $mensajes["pagos.{$i}.max"]      = "El váucher del {$i}° {$labelPeriodo} no debe pesar más de 5 MB.";
        }
        // Pagos posteriores: opcionales.
        $reglas['pagos.*'] = 'nullable|file|mimes:pdf|max:5120';

        $request->validate($reglas, $mensajes);

        DB::transaction(function () use ($request, $esBachi, $contexto) {
            $user = User::create([
                'name' => "{$request->nombre} {$request->apellidos}",
                'email' => $request->email,
                'password' => bcrypt('udea' . date('Y')),
                'activo' => true,
            ]);
            $user->assignRole('alumno');

            $idAlumno = $esBachi
                ? $this->generarIdAlumnoBachi($request->id_plan_bachillerato)
                : $this->generarIdAlumno($request->id_carrera);

            $alumno = Alumno::create([
                'user_id' => $user->id,
                'id_carrera' => $esBachi ? null : $request->id_carrera,
                'id_plan_bachillerato' => $esBachi ? $request->id_plan_bachillerato : null,
                'id_tutor' => $request->id_tutor,
                'id_alumno_publico' => $idAlumno,
                'nombre' => $request->nombre,
                'apellidos' => $request->apellidos,
                'cuatrimestre_actual' => $request->cuatrimestre_actual,
                'estatus' => 'activo',
                'nivel_educativo' => $contexto,
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

        return redirect()->route('gestor.alumnos.index')->with('success', 'Alumno registrado correctamente.');
    }

    public function show(Alumno $alumno)
    {
        $alumno->load('carrera', 'tutor', 'user', 'inscripciones.grupo', 'servicioSocial', 'constancias',
            'padreTutor', 'pagosCuatrimestre', 'documentos');
        return view('gestor.alumnos.show', compact('alumno'));
    }

    public function edit(Alumno $alumno)
    {
        $alumno->load('padreTutor', 'documentos', 'carrera', 'planBachillerato');

        // El nivel del alumno define qué se edita: Carrera (universidad) o Plan (bachillerato).
        $esBachi = $alumno->nivel_educativo === 'bachillerato';

        $carreras    = $esBachi ? collect() : Carrera::misCarreras()->orderBy('nombre_carrera')->get();
        $planesBachi = $esBachi
            ? BachilleratoPlan::vigente()->orderBy('nombre_plan')->get()
            : collect();
        $tutores = \App\Models\Docente::where('es_tutor', true)
            ->orderBy('apellidos')->orderBy('nombre')->get();

        return view('gestor.alumnos.edit', compact('alumno', 'carreras', 'planesBachi', 'tutores', 'esBachi'));
    }

    public function update(Request $request, Alumno $alumno)
    {
        $esBachi = $alumno->nivel_educativo === 'bachillerato';

        if ($esBachi) {
            $planSel = BachilleratoPlan::find($request->id_plan_bachillerato) ?? $alumno->planBachillerato;
            $maxPeriodos = $planSel?->num_semestres ?? 6;
        } else {
            $carreraSel = Carrera::find($request->id_carrera) ?? $alumno->carrera;
            $maxPeriodos = $carreraSel?->max_periodos ?? 10;
        }

        $request->validate([
            'nombre'              => ['required', 'string', 'max:80', 'regex:/^[\pL\s]+$/u'],
            'apellidos'           => ['required', 'string', 'max:100', 'regex:/^[\pL\s]+$/u'],
            'id_carrera'           => $esBachi ? 'nullable' : 'required|exists:carrera,id_carrera',
            'id_plan_bachillerato' => $esBachi ? 'required|exists:bachillerato_plan,id_plan_bachillerato' : 'nullable',
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

        DB::transaction(function () use ($request, $alumno, $esBachi) {
            $alumno->update([
                'nombre'               => $request->nombre,
                'apellidos'            => $request->apellidos,
                'id_carrera'           => $esBachi ? null : $request->id_carrera,
                'id_plan_bachillerato' => $esBachi ? $request->id_plan_bachillerato : null,
                'cuatrimestre_actual'  => $request->cuatrimestre_actual,
                'id_tutor'             => $request->id_tutor,
            ]);

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

        return redirect()->route('gestor.alumnos.show', $alumno)->with('success', 'Alumno actualizado.');
    }

    public function destroy(Alumno $alumno)
    {
        $alumno->user->update(['activo' => false]);
        $alumno->update(['estatus' => 'baja_definitiva']);
        return redirect()->route('gestor.alumnos.index')->with('success', 'Alumno dado de baja definitiva.');
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

    public function aprobarBaucher(Request $request, PagoCuatrimestre $pago)
    {
        if (!$pago->estaPendiente()) {
            return back()->with('error', 'Este váucher ya fue revisado.');
        }

        // Si el gestor capturó/confirmó monto al aprobar, persistirlo.
        // Si no, se usará la tarifa default vía monto_efectivo accessor.
        $request->validate([
            'monto' => 'nullable|numeric|min:0|max:9999999.99',
        ]);

        $datos = [
            'estatus'            => 'aprobado',
            'revisado_por'       => auth()->id(),
            'revisado_en'        => now(),
            'comentario_rechazo' => null,
        ];
        if ($request->filled('monto')) {
            $datos['monto'] = $request->monto;
        }
        $pago->update($datos);

        $alumno = $pago->alumno;
        $this->notificaciones->enviar(
            $alumno->user,
            'pago',
            'Váucher aprobado',
            "Tu váucher del {$pago->cuatrimestre}° cuatrimestre ha sido validado exitosamente.",
            ['icono' => 'clipboard-check', 'color' => 'green', 'url' => route('alumno.pagos')]
        );

        // Hook a Caja General: registrar el ingreso automáticamente.
        // Idempotente: si ya existe (rara doble aprobación), no duplica.
        try {
            $this->ingresosCaja->registrarColegiatura($pago->fresh(), $request->user());
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning("CajaGeneral: fallo registro colegiatura pago #{$pago->id_pago}: {$e->getMessage()}");
            // El baucher YA fue aprobado; no abortamos. El admin puede registrar el ingreso manualmente.
        }

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

    private function generarIdAlumno(int $carreraId): string
    {
        $carrera = Carrera::find($carreraId);
        $año = date('Y');
        $ultimo = Alumno::where('id_carrera', $carreraId)
            ->where('id_alumno_publico', 'like', "{$carrera->clave_carrera}-{$año}-%")
            ->count();

        $seq = str_pad($ultimo + 1, 3, '0', STR_PAD_LEFT);
        return strtoupper("{$carrera->clave_carrera}-{$año}-{$seq}");
    }

    /**
     * Genera ID de alumno de bachillerato: BACH-2026-001.
     * El prefijo es siempre "BACH" independientemente del plan,
     * para que sea facil identificar visualmente que es bachillerato.
     */
    private function generarIdAlumnoBachi(int $planId): string
    {
        $año = date('Y');
        $ultimo = Alumno::sinFiltroNivel()
            ->where('id_plan_bachillerato', $planId)
            ->where('id_alumno_publico', 'like', "BACH-{$año}-%")
            ->count();

        $seq = str_pad($ultimo + 1, 3, '0', STR_PAD_LEFT);
        return "BACH-{$año}-{$seq}";
    }
}
