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

class DocentesController extends Controller
{
    public function index(Request $request)
    {
        $docentes = Docente::with('user')
            ->when($request->buscar, fn($q) =>
                $q->where('nombre', 'like', "%{$request->buscar}%")
                  ->orWhere('apellidos', 'like', "%{$request->buscar}%")
            )
            ->orderBy('apellidos')
            ->paginate(20)->withQueryString();

        return view('servicios.docentes.index', compact('docentes'));
    }

    public function create()
    {
        $carreras = Carrera::orderBy('nombre_carrera')->get();
        return view('servicios.docentes.create', compact('carreras'));
    }

    public function store(Request $request)
    {
        $rules = [
            'nombre'         => ['required', 'string', 'max:80', 'regex:/^[\pL\s]+$/u'],
            'apellidos'      => ['required', 'string', 'max:100', 'regex:/^[\pL\s]+$/u'],
            'email'          => 'required|email|unique:users,email',
            'especialidad'   => 'nullable|string|max:100',
            'num_cedula'     => 'nullable|string|max:30',
            'rfc'            => 'nullable|string|max:20',
            'horas_contrato' => 'nullable|integer|min:1|max:40',
            'es_tutor'       => 'boolean',

            'carreras'   => 'nullable|array',
            'carreras.*' => 'exists:carrera,id_carrera',
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
                'name' => "{$request->nombre} {$request->apellidos}",
                'email' => $request->email,
                'password' => bcrypt('docente' . date('Y')),
                'activo' => true,
            ]);
            $user->assignRole('docente');

            $docente = Docente::create([
                'user_id'        => $user->id,
                'nombre'         => $request->nombre,
                'apellidos'      => $request->apellidos,
                'especialidad'   => $request->especialidad,
                'num_cedula'     => $request->num_cedula,
                'rfc'            => $request->rfc,
                'horas_contrato' => $request->horas_contrato,
                'es_tutor'       => $request->boolean('es_tutor'),
            ]);

            $carrerasSel = array_filter(array_unique($request->input('carreras', [])));
            if (!empty($carrerasSel)) {
                $docente->carrerasImparte()->sync($carrerasSel);
            }

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

        return redirect()->route('servicios.docentes.index')->with('success', 'Docente registrado.');
    }

    public function show(Docente $docente)
    {
        $docente->load('user', 'horarios.grupo', 'horarios.materia', 'evaluaciones');
        return view('servicios.docentes.show', compact('docente'));
    }

    public function edit(Docente $docente)
    {
        $docente->load('documentos', 'carrerasImparte');
        $carreras = Carrera::orderBy('nombre_carrera')->get();
        return view('servicios.docentes.edit', compact('docente', 'carreras'));
    }

    public function update(Request $request, Docente $docente)
    {
        $rules = [
            'nombre'         => ['required', 'string', 'max:80', 'regex:/^[\pL\s]+$/u'],
            'apellidos'      => ['required', 'string', 'max:100', 'regex:/^[\pL\s]+$/u'],
            'especialidad'   => 'nullable|string|max:100',
            'num_cedula'     => 'nullable|string|max:30',
            'rfc'            => 'nullable|string|max:20',
            'horas_contrato' => 'nullable|integer|min:1|max:40',

            'carreras'   => 'nullable|array',
            'carreras.*' => 'exists:carrera,id_carrera',

            'documentos'   => 'nullable|array',
            'documentos.*' => 'nullable|file|mimes:pdf|max:5120',
        ];
        $messages = [
            'nombre.regex'    => 'El nombre solo debe contener letras y espacios.',
            'apellidos.regex' => 'Los apellidos solo deben contener letras y espacios.',
        ];
        foreach (DocumentoDocente::TIPOS as $tipo => $label) {
            $messages["documentos.$tipo.mimes"] = "El documento \"$label\" debe ser un archivo PDF.";
            $messages["documentos.$tipo.max"]   = "El documento \"$label\" no debe pesar más de 5 MB.";
        }
        $request->validate($rules, $messages);

        DB::transaction(function () use ($request, $docente) {
            $docente->update([
                'nombre'         => $request->nombre,
                'apellidos'      => $request->apellidos,
                'especialidad'   => $request->especialidad,
                'num_cedula'     => $request->num_cedula,
                'rfc'            => $request->rfc,
                'horas_contrato' => $request->horas_contrato,
                'es_tutor'       => $request->boolean('es_tutor'),
            ]);

            $carrerasSel = array_filter(array_unique($request->input('carreras', [])));
            $docente->carrerasImparte()->sync($carrerasSel);

            if ($request->hasFile('documentos')) {
                foreach ($request->file('documentos') as $tipo => $file) {
                    if (!$file || !array_key_exists($tipo, DocumentoDocente::TIPOS)) continue;
                    $existing = DocumentoDocente::where('id_docente', $docente->id_docente)
                        ->where('tipo', $tipo)->first();
                    if ($existing) Storage::disk('public')->delete($existing->archivo_path);
                    $path = $file->store("docentes/{$docente->id_docente}/documentos", 'public');
                    DocumentoDocente::updateOrCreate(
                        ['id_docente' => $docente->id_docente, 'tipo' => $tipo],
                        ['archivo_path' => $path, 'subido_en' => now()]
                    );
                }
            }

            // Verificación final: todos los documentos obligatorios deben existir
            $tiposExistentes = DocumentoDocente::where('id_docente', $docente->id_docente)
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

        return redirect()->route('servicios.docentes.index')->with('success', 'Docente actualizado.');
    }

    public function destroy(Docente $docente)
    {
        $docente->user->update(['activo' => false]);
        return redirect()->route('servicios.docentes.index')->with('success', 'Docente desactivado.');
    }

    public function eliminarDocumento(DocumentoDocente $documento)
    {
        if ($documento->archivo_path) {
            Storage::disk('public')->delete($documento->archivo_path);
        }
        $documento->delete();
        return back()->with('success', 'Documento eliminado.');
    }
}
