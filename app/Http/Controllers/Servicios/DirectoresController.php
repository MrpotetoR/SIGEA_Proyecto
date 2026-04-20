<?php

namespace App\Http\Controllers\Servicios;

use App\Http\Controllers\Controller;
use App\Models\Carrera;
use App\Models\Docente;
use App\Models\DocumentoDocente;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DirectoresController extends Controller
{
    public function index(Request $request)
    {
        // Directores = Docentes cuyo user tiene rol director_carrera
        $directores = Docente::with('user', 'carrerasDirigidas')
            ->whereHas('user', fn($q) => $q->role('director_carrera'))
            ->when($request->buscar, fn($q) =>
                $q->where('nombre', 'like', "%{$request->buscar}%")
                  ->orWhere('apellidos', 'like', "%{$request->buscar}%")
            )
            ->orderBy('apellidos')
            ->paginate(20)->withQueryString();

        return view('servicios.directores.index', compact('directores'));
    }

    public function create()
    {
        $carreras = Carrera::orderBy('nombre_carrera')->get();
        return view('servicios.directores.create', compact('carreras'));
    }

    public function store(Request $request)
    {
        $rules = [
            'nombre'       => ['required', 'string', 'max:80', 'regex:/^[\pL\s]+$/u'],
            'apellidos'    => ['required', 'string', 'max:100', 'regex:/^[\pL\s]+$/u'],
            'email'        => 'required|email|unique:users,email',
            'especialidad' => 'nullable|string|max:100',
            'num_cedula'   => 'nullable|string|max:30',
            'rfc'          => 'nullable|string|max:20',
            'id_carrera'   => 'nullable|exists:carrera,id_carrera',
        ];
        $messages = [
            'nombre.regex'    => 'El nombre solo debe contener letras y espacios.',
            'apellidos.regex' => 'Los apellidos solo deben contener letras y espacios.',
        ];
        foreach (DocumentoDocente::TIPOS as $tipo => $label) {
            $rules["documentos.$tipo"] = 'required|file|mimes:pdf|max:5120';
            $messages["documentos.$tipo.required"] = "El documento \"$label\" es obligatorio.";
            $messages["documentos.$tipo.mimes"]    = "El documento \"$label\" debe ser un archivo PDF.";
            $messages["documentos.$tipo.max"]      = "El documento \"$label\" no debe pesar más de 5 MB.";
        }
        $request->validate($rules, $messages);

        DB::transaction(function () use ($request) {
            $user = User::create([
                'name'     => "{$request->nombre} {$request->apellidos}",
                'email'    => $request->email,
                'password' => bcrypt('director' . date('Y')),
                'activo'   => true,
            ]);
            $user->assignRole('director_carrera');

            $docente = Docente::create([
                'user_id'      => $user->id,
                'nombre'       => $request->nombre,
                'apellidos'    => $request->apellidos,
                'especialidad' => $request->especialidad,
                'num_cedula'   => $request->num_cedula,
                'rfc'          => $request->rfc,
                'es_tutor'     => false,
            ]);

            // Asignar carrera si se seleccionó
            if ($request->id_carrera) {
                Carrera::where('id_carrera', $request->id_carrera)
                    ->update(['id_director' => $docente->id_docente]);
            }

            // Documentos
            if ($request->hasFile('documentos')) {
                foreach ($request->file('documentos') as $tipo => $file) {
                    if (!$file || !array_key_exists($tipo, DocumentoDocente::TIPOS)) continue;
                    $path = $file->store("docentes/{$docente->id_docente}/documentos", 'public');
                    DocumentoDocente::create([
                        'id_docente'   => $docente->id_docente,
                        'tipo'         => $tipo,
                        'archivo_path' => $path,
                    ]);
                }
            }
        });

        return redirect()->route('servicios.directores.index')->with('success', 'Director de carrera registrado.');
    }

    public function show(Docente $directore)
    {
        $director = $directore;
        $director->load('user', 'carrerasDirigidas');
        return view('servicios.directores.show', compact('director'));
    }

    public function edit(Docente $directore)
    {
        $director = $directore;
        $director->load('carrerasDirigidas', 'documentos');
        $carreras = Carrera::orderBy('nombre_carrera')->get();
        return view('servicios.directores.edit', compact('director', 'carreras'));
    }

    public function update(Request $request, Docente $directore)
    {
        $director = $directore;

        $request->validate([
            'nombre'       => ['required', 'string', 'max:80', 'regex:/^[\pL\s]+$/u'],
            'apellidos'    => ['required', 'string', 'max:100', 'regex:/^[\pL\s]+$/u'],
            'especialidad' => 'nullable|string|max:100',
            'num_cedula'   => 'nullable|string|max:30',
            'rfc'          => 'nullable|string|max:20',
            'id_carrera'   => 'nullable|exists:carrera,id_carrera',

            'documentos'   => 'nullable|array',
            'documentos.*' => 'nullable|file|mimes:pdf|max:5120',
        ], [
            'nombre.regex'    => 'El nombre solo debe contener letras y espacios.',
            'apellidos.regex' => 'Los apellidos solo deben contener letras y espacios.',
        ]);

        DB::transaction(function () use ($request, $director) {
            $director->update([
                'nombre'       => $request->nombre,
                'apellidos'    => $request->apellidos,
                'especialidad' => $request->especialidad,
                'num_cedula'   => $request->num_cedula,
                'rfc'          => $request->rfc,
            ]);

            // Desasignar carrera anterior y asignar nueva
            Carrera::where('id_director', $director->id_docente)->update(['id_director' => null]);
            if ($request->id_carrera) {
                Carrera::where('id_carrera', $request->id_carrera)
                    ->update(['id_director' => $director->id_docente]);
            }

            // Documentos (reemplazo)
            if ($request->hasFile('documentos')) {
                foreach ($request->file('documentos') as $tipo => $file) {
                    if (!$file || !array_key_exists($tipo, DocumentoDocente::TIPOS)) continue;
                    $existing = DocumentoDocente::where('id_docente', $director->id_docente)
                        ->where('tipo', $tipo)->first();
                    if ($existing) Storage::disk('public')->delete($existing->archivo_path);
                    $path = $file->store("docentes/{$director->id_docente}/documentos", 'public');
                    DocumentoDocente::updateOrCreate(
                        ['id_docente' => $director->id_docente, 'tipo' => $tipo],
                        ['archivo_path' => $path, 'subido_en' => now()]
                    );
                }
            }

            // Verificación final: todos los documentos obligatorios deben existir
            $tiposExistentes = DocumentoDocente::where('id_docente', $director->id_docente)
                ->pluck('tipo')->toArray();
            $faltantes = array_diff(array_keys(DocumentoDocente::TIPOS), $tiposExistentes);
            if (!empty($faltantes)) {
                $errores = [];
                foreach ($faltantes as $tipo) {
                    $errores["documentos.$tipo"] = "El documento \"" . DocumentoDocente::TIPOS[$tipo] . "\" es obligatorio. Debes cargarlo antes de guardar.";
                }
                throw \Illuminate\Validation\ValidationException::withMessages($errores);
            }
        });

        return redirect()->route('servicios.directores.index')->with('success', 'Director actualizado.');
    }

    public function destroy(Docente $directore)
    {
        $director = $directore;
        // Desasignar carreras
        Carrera::where('id_director', $director->id_docente)->update(['id_director' => null]);
        $director->user->update(['activo' => false]);
        return redirect()->route('servicios.directores.index')->with('success', 'Director desactivado.');
    }
}
