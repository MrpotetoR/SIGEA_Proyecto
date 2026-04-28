<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Carrera;
use App\Models\DocumentoPersonalSE;
use App\Models\PersonalServiciosEscolares;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PersonalController extends Controller
{
    public function index(Request $request)
    {
        $personal = PersonalServiciosEscolares::with('user', 'carreras')
            ->when($request->buscar, fn($q) =>
                $q->where('nombre', 'like', "%{$request->buscar}%")
                  ->orWhere('apellidos', 'like', "%{$request->buscar}%")
                  ->orWhereHas('user', fn($u) => $u->where('email', 'like', "%{$request->buscar}%"))
            )
            ->orderBy('apellidos')
            ->paginate(20)->withQueryString();

        return view('admin.personal.index', compact('personal'));
    }

    public function create()
    {
        // Solo carreras sin asignar (para evitar conflicto con la regla 1:1).
        $carrerasDisponibles = Carrera::doesntHave('personalAsignado')
            ->orderBy('nombre_carrera')->get();

        return view('admin.personal.create', compact('carrerasDisponibles'));
    }

    public function store(Request $request)
    {
        $rules = [
            'nombre'       => ['required', 'string', 'max:80', 'regex:/^[\pL\s]+$/u'],
            'apellidos'    => ['required', 'string', 'max:100', 'regex:/^[\pL\s]+$/u'],
            'email'        => 'required|email|unique:users,email',
            'num_cedula'   => 'nullable|string|max:30',
            'rfc'          => 'nullable|string|max:20',
            'especialidad' => 'required|string|max:150',
            'carreras'     => 'nullable|array|max:' . PersonalServiciosEscolares::MAX_CARRERAS,
            'carreras.*'   => 'integer|exists:carrera,id_carrera',
        ];
        $messages = [
            'nombre.regex'        => 'El nombre solo debe contener letras y espacios.',
            'apellidos.regex'     => 'Los apellidos solo deben contener letras y espacios.',
            'carreras.max'        => 'Solo puedes asignar hasta ' . PersonalServiciosEscolares::MAX_CARRERAS . ' carreras por personal.',
            'especialidad.required' => 'La especialidad es obligatoria.',
        ];
        foreach (DocumentoPersonalSE::TIPOS as $tipo => $label) {
            $rules["documentos.$tipo"]              = 'required|file|mimes:pdf|max:5120';
            $messages["documentos.$tipo.required"]  = "El documento \"$label\" es obligatorio.";
            $messages["documentos.$tipo.mimes"]     = "El documento \"$label\" debe ser un archivo PDF.";
            $messages["documentos.$tipo.max"]       = "El documento \"$label\" no debe pesar más de 5 MB.";
        }
        $request->validate($rules, $messages);

        // Validar que las carreras seleccionadas no estén ya asignadas a otra persona.
        if ($request->carreras) {
            $yaAsignadas = DB::table('personal_carrera')
                ->whereIn('id_carrera', $request->carreras)
                ->pluck('id_carrera');

            if ($yaAsignadas->isNotEmpty()) {
                $nombres = Carrera::whereIn('id_carrera', $yaAsignadas)
                    ->pluck('nombre_carrera')->implode(', ');
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'carreras' => "Las siguientes carreras ya están asignadas: {$nombres}",
                ]);
            }
        }

        DB::transaction(function () use ($request) {
            $user = User::create([
                'name'     => "{$request->nombre} {$request->apellidos}",
                'email'    => $request->email,
                'password' => bcrypt('servicios' . date('Y')),
                'activo'   => true,
            ]);
            $user->assignRole('servicios_escolares');

            $personal = PersonalServiciosEscolares::create([
                'user_id'      => $user->id,
                'nombre'       => $request->nombre,
                'apellidos'    => $request->apellidos,
                'num_cedula'   => $request->num_cedula,
                'rfc'          => $request->rfc,
                'especialidad' => $request->especialidad,
            ]);

            // Carreras asignadas (opcional al crear).
            if ($request->carreras) {
                $personal->carreras()->sync($request->carreras);
            }

            // Documentos.
            if ($request->hasFile('documentos')) {
                foreach ($request->file('documentos') as $tipo => $file) {
                    if (!$file || !array_key_exists($tipo, DocumentoPersonalSE::TIPOS)) continue;
                    $path = $file->store("personal_se/{$personal->id_personal}/documentos", 'public');
                    DocumentoPersonalSE::create([
                        'id_personal'  => $personal->id_personal,
                        'tipo'         => $tipo,
                        'archivo_path' => $path,
                    ]);
                }
            }
        });

        return redirect()->route('admin.personal.index')
            ->with('success', 'Personal de Servicios Escolares registrado correctamente.');
    }

    public function show(PersonalServiciosEscolares $personal)
    {
        $personal->load('user', 'carreras', 'documentos');
        return view('admin.personal.show', compact('personal'));
    }

    public function edit(PersonalServiciosEscolares $personal)
    {
        $personal->load('user', 'carreras', 'documentos');

        // Carreras: las suyas + las que estén libres.
        $carrerasDisponibles = Carrera::where(function ($q) use ($personal) {
                $q->doesntHave('personalAsignado')
                  ->orWhereHas('personalAsignado', fn($p) => $p->where('personal_servicios_escolares.id_personal', $personal->id_personal));
            })
            ->orderBy('nombre_carrera')->get();

        return view('admin.personal.edit', compact('personal', 'carrerasDisponibles'));
    }

    public function update(Request $request, PersonalServiciosEscolares $personal)
    {
        $request->validate([
            'nombre'       => ['required', 'string', 'max:80', 'regex:/^[\pL\s]+$/u'],
            'apellidos'    => ['required', 'string', 'max:100', 'regex:/^[\pL\s]+$/u'],
            'email'        => 'required|email|unique:users,email,' . $personal->user_id,
            'num_cedula'   => 'nullable|string|max:30',
            'rfc'          => 'nullable|string|max:20',
            'especialidad' => 'required|string|max:150',
            'documentos'   => 'nullable|array',
            'documentos.*' => 'nullable|file|mimes:pdf|max:5120',
        ], [
            'nombre.regex'    => 'El nombre solo debe contener letras y espacios.',
            'apellidos.regex' => 'Los apellidos solo deben contener letras y espacios.',
            'especialidad.required' => 'La especialidad es obligatoria.',
        ]);

        DB::transaction(function () use ($request, $personal) {
            $personal->update([
                'nombre'       => $request->nombre,
                'apellidos'    => $request->apellidos,
                'num_cedula'   => $request->num_cedula,
                'rfc'          => $request->rfc,
                'especialidad' => $request->especialidad,
            ]);

            $personal->user->update([
                'name'  => "{$request->nombre} {$request->apellidos}",
                'email' => $request->email,
            ]);

            // Documentos (reemplazo).
            if ($request->hasFile('documentos')) {
                foreach ($request->file('documentos') as $tipo => $file) {
                    if (!$file || !array_key_exists($tipo, DocumentoPersonalSE::TIPOS)) continue;
                    $existing = DocumentoPersonalSE::where('id_personal', $personal->id_personal)
                        ->where('tipo', $tipo)->first();
                    if ($existing) Storage::disk('public')->delete($existing->archivo_path);
                    $path = $file->store("personal_se/{$personal->id_personal}/documentos", 'public');
                    DocumentoPersonalSE::updateOrCreate(
                        ['id_personal' => $personal->id_personal, 'tipo' => $tipo],
                        ['archivo_path' => $path, 'subido_en' => now()]
                    );
                }
            }
        });

        return redirect()->route('admin.personal.show', $personal)
            ->with('success', 'Personal actualizado.');
    }

    public function destroy(PersonalServiciosEscolares $personal)
    {
        DB::transaction(function () use ($personal) {
            // Liberar carreras (vuelven a "sin asignar").
            $personal->carreras()->detach();
            // Soft delete del personal y desactivar usuario.
            $personal->user->update(['activo' => false]);
            $personal->user->delete(); // soft delete
            $personal->delete();        // soft delete
        });

        return redirect()->route('admin.personal.index')
            ->with('success', 'Personal eliminado. Sus carreras quedaron disponibles para reasignar.');
    }
}
