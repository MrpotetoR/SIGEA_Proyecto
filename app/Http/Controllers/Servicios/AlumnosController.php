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
    public function index(Request $request)
    {
        $alumnos = Alumno::with('carrera', 'user')
            ->when($request->buscar, fn($q) =>
                $q->where('nombre', 'like', "%{$request->buscar}%")
                  ->orWhere('apellidos', 'like', "%{$request->buscar}%")
                  ->orWhere('matricula', 'like', "%{$request->buscar}%")
            )
            ->when($request->carrera_id, fn($q) => $q->where('id_carrera', $request->carrera_id))
            ->when($request->estatus, fn($q) => $q->where('estatus', $request->estatus))
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
        $request->validate([
            'nombre'              => ['required', 'string', 'max:80', 'regex:/^[\pL\s]+$/u'],
            'apellidos'           => ['required', 'string', 'max:100', 'regex:/^[\pL\s]+$/u'],
            'email'               => 'required|email|unique:users,email',
            'id_carrera'          => 'required|exists:carrera,id_carrera',
            'cuatrimestre_actual' => 'required|integer|min:1|max:10',
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
                            "pagos.$cuatri" => "Los bauchers deben cargarse en orden consecutivo. Falta el {$esperado}° cuatrimestre.",
                        ]);
                    }
                    $path = $file->store("alumnos/{$alumno->id_alumno}/pagos", 'public');
                    PagoCuatrimestre::create([
                        'id_alumno'    => $alumno->id_alumno,
                        'cuatrimestre' => (int) $cuatri,
                        'baucher_path' => $path,
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
        $alumno->load('padreTutor', 'documentos');
        $carreras = Carrera::orderBy('nombre_carrera')->get();
        $tutores = \App\Models\Docente::where('es_tutor', true)
            ->orderBy('apellidos')->orderBy('nombre')->get();
        return view('servicios.alumnos.edit', compact('alumno', 'carreras', 'tutores'));
    }

    public function update(Request $request, Alumno $alumno)
    {
        $request->validate([
            'nombre'              => ['required', 'string', 'max:80', 'regex:/^[\pL\s]+$/u'],
            'apellidos'           => ['required', 'string', 'max:100', 'regex:/^[\pL\s]+$/u'],
            'id_carrera'          => 'required|exists:carrera,id_carrera',
            'cuatrimestre_actual' => 'required|integer|min:1|max:10',
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
        $request->validate([
            'cuatrimestre' => 'required|integer|min:1|max:10',
            'baucher'      => 'required|file|mimes:pdf|max:5120',
        ]);

        $cuatri = (int) $request->cuatrimestre;

        // Regla secuencial: el siguiente cuatrimestre permitido = cargados + 1
        $cargados = $alumno->pagosCuatrimestre()->count();
        $siguiente = $cargados + 1;

        if ($cuatri !== $siguiente) {
            return back()->with('error', "Solo puedes cargar el baucher del {$siguiente}° cuatrimestre. Los bauchers deben subirse en orden consecutivo.");
        }

        // No se permite reemplazar
        if ($alumno->pagosCuatrimestre()->where('cuatrimestre', $cuatri)->exists()) {
            return back()->with('error', 'Este baucher ya fue cargado y no puede modificarse.');
        }

        $path = $request->file('baucher')->store("alumnos/{$alumno->id_alumno}/pagos", 'public');
        PagoCuatrimestre::create([
            'id_alumno'    => $alumno->id_alumno,
            'cuatrimestre' => $cuatri,
            'baucher_path' => $path,
        ]);

        return back()->with('success', "Baucher del {$cuatri}° cuatrimestre cargado correctamente.");
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
