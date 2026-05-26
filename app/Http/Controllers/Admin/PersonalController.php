<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\ReauthController;
use App\Http\Controllers\Controller;
use App\Models\CajaChicaLog;
use App\Models\Carrera;
use App\Models\DocumentoPersonalSE;
use App\Models\GestorEscolar;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class PersonalController extends Controller
{
    public function index(Request $request)
    {
        $personal = GestorEscolar::with('user', 'carreras')
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

        // Cupos disponibles para el permiso de Caja Chica (máx. 3 gestores).
        $cupoCajaChicaUsado = GestorEscolar::where('puede_gestionar_caja_chica', true)->count();
        $cupoCajaChicaMax   = GestorEscolar::MAX_GESTORES_CAJA_CHICA;

        return view('admin.personal.create', compact(
            'carrerasDisponibles', 'cupoCajaChicaUsado', 'cupoCajaChicaMax'
        ));
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
            'carreras'     => 'nullable|array|max:' . GestorEscolar::MAX_CARRERAS,
            'carreras.*'   => 'integer|exists:carrera,id_carrera',
        ];
        $messages = [
            'nombre.regex'        => 'El nombre solo debe contener letras y espacios.',
            'apellidos.regex'     => 'Los apellidos solo deben contener letras y espacios.',
            'carreras.max'        => 'Solo puedes asignar hasta ' . GestorEscolar::MAX_CARRERAS . ' carreras por personal.',
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

        // Permiso especial: solo se concede si el admin confirmó por reauth
        // (verificamos el grace period que dejó el ReauthController).
        $puedeAsignar = $request->boolean('puede_asignar_carreras')
            && ReauthController::tieneGracePeriod('otorgar_permiso_especial');

        // Permiso de Caja Chica: requiere reauth + validación de cupo (máx 3).
        $puedeCajaChica = $request->boolean('puede_gestionar_caja_chica')
            && ReauthController::tieneGracePeriod('otorgar_permiso_caja_chica');

        if ($puedeCajaChica) {
            $cupoUsado = GestorEscolar::where('puede_gestionar_caja_chica', true)->count();
            if ($cupoUsado >= GestorEscolar::MAX_GESTORES_CAJA_CHICA) {
                throw ValidationException::withMessages([
                    'puede_gestionar_caja_chica' =>
                        'Ya hay ' . GestorEscolar::MAX_GESTORES_CAJA_CHICA .
                        ' gestores con permiso de Caja Chica (máximo permitido). Revoca a otro primero.',
                ]);
            }
        }

        DB::transaction(function () use ($request, $puedeAsignar, $puedeCajaChica) {
            $user = User::create([
                'name'     => "{$request->nombre} {$request->apellidos}",
                'email'    => $request->email,
                'password' => bcrypt('gestor' . date('Y')),
                'activo'   => true,
            ]);
            $user->assignRole('gestor_escolar');

            $personal = GestorEscolar::create([
                'user_id'                    => $user->id,
                'nombre'                     => $request->nombre,
                'apellidos'                  => $request->apellidos,
                'num_cedula'                 => $request->num_cedula,
                'rfc'                        => $request->rfc,
                'especialidad'               => $request->especialidad,
                'puede_asignar_carreras'     => $puedeAsignar,
                'puede_gestionar_caja_chica' => $puedeCajaChica,
            ]);

            // Auditoría: si se otorgó permiso de Caja Chica, dejar huella.
            if ($puedeCajaChica) {
                CajaChicaLog::create([
                    'user_id'              => $request->user()->id,
                    'gestor_afectado_id'   => $personal->id_personal,
                    'accion'               => 'otorgar_permiso',
                    'motivo'               => $request->input('motivo_caja_chica', 'reorganizacion'),
                    'motivo_personalizado' => $request->input('motivo_caja_chica_libre'),
                    'ip'                   => $request->ip(),
                    'user_agent'           => substr((string) $request->userAgent(), 0, 255),
                ]);
            }

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
            ->with('success', 'Gestor Escolar registrado correctamente.');
    }

    public function show(GestorEscolar $personal)
    {
        $personal->load('user', 'carreras', 'documentos');
        return view('admin.personal.show', compact('personal'));
    }

    public function edit(GestorEscolar $personal)
    {
        $personal->load('user', 'carreras', 'documentos');

        // Carreras: las suyas + las que estén libres.
        $carrerasDisponibles = Carrera::where(function ($q) use ($personal) {
                $q->doesntHave('personalAsignado')
                  ->orWhereHas('personalAsignado', fn($p) => $p->where('gestores_escolares.id_personal', $personal->id_personal));
            })
            ->orderBy('nombre_carrera')->get();

        // Cupos del permiso de Caja Chica (excluye al gestor actual si ya lo tiene).
        $cupoCajaChicaUsado = GestorEscolar::where('puede_gestionar_caja_chica', true)
            ->where('id_personal', '!=', $personal->id_personal)
            ->count();
        $cupoCajaChicaMax = GestorEscolar::MAX_GESTORES_CAJA_CHICA;

        return view('admin.personal.edit', compact(
            'personal', 'carrerasDisponibles', 'cupoCajaChicaUsado', 'cupoCajaChicaMax'
        ));
    }

    public function update(Request $request, GestorEscolar $personal)
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

        // Permiso especial: el flag llega del form. Si CAMBIA respecto a lo
        // que el gestor tenía, exigir reauth (otorgar o revocar).
        $flagSolicitado   = $request->boolean('puede_asignar_carreras');
        $valorActual      = (bool) $personal->puede_asignar_carreras;
        $puedeAsignarFinal = $valorActual;

        if ($flagSolicitado !== $valorActual) {
            $accion = $flagSolicitado ? 'otorgar_permiso_especial' : 'revocar_permiso_especial';
            if (ReauthController::tieneGracePeriod($accion)) {
                $puedeAsignarFinal = $flagSolicitado;
            }
            // Si no hay grace period, ignoramos el cambio (no se altera el flag).
        }

        // Permiso de Caja Chica: misma mecánica + validación de cupo al otorgar.
        $cajaChicaSolicitado = $request->boolean('puede_gestionar_caja_chica');
        $cajaChicaActual     = (bool) $personal->puede_gestionar_caja_chica;
        $puedeCajaChicaFinal = $cajaChicaActual;
        $cajaChicaCambio     = null; // 'otorgar' | 'revocar' | null

        if ($cajaChicaSolicitado !== $cajaChicaActual) {
            $accion = $cajaChicaSolicitado ? 'otorgar_permiso_caja_chica' : 'revocar_permiso_caja_chica';
            if (ReauthController::tieneGracePeriod($accion)) {
                if ($cajaChicaSolicitado) {
                    // Validar cupo (máx 3, sin contar al gestor actual).
                    $cupoUsado = GestorEscolar::where('puede_gestionar_caja_chica', true)
                        ->where('id_personal', '!=', $personal->id_personal)
                        ->count();
                    if ($cupoUsado >= GestorEscolar::MAX_GESTORES_CAJA_CHICA) {
                        throw ValidationException::withMessages([
                            'puede_gestionar_caja_chica' =>
                                'Ya hay ' . GestorEscolar::MAX_GESTORES_CAJA_CHICA .
                                ' gestores con permiso de Caja Chica (máximo permitido). Revoca a otro primero.',
                        ]);
                    }
                }
                $puedeCajaChicaFinal = $cajaChicaSolicitado;
                $cajaChicaCambio = $cajaChicaSolicitado ? 'otorgar' : 'revocar';
            }
        }

        DB::transaction(function () use (
            $request, $personal, $puedeAsignarFinal, $puedeCajaChicaFinal, $cajaChicaCambio
        ) {
            $personal->update([
                'nombre'                     => $request->nombre,
                'apellidos'                  => $request->apellidos,
                'num_cedula'                 => $request->num_cedula,
                'rfc'                        => $request->rfc,
                'especialidad'               => $request->especialidad,
                'puede_asignar_carreras'     => $puedeAsignarFinal,
                'puede_gestionar_caja_chica' => $puedeCajaChicaFinal,
            ]);

            // Auditoría: log del cambio de permiso de Caja Chica.
            if ($cajaChicaCambio !== null) {
                CajaChicaLog::create([
                    'user_id'              => $request->user()->id,
                    'gestor_afectado_id'   => $personal->id_personal,
                    'accion'               => $cajaChicaCambio === 'otorgar' ? 'otorgar_permiso' : 'revocar_permiso',
                    'motivo'               => $request->input('motivo_caja_chica', 'reorganizacion'),
                    'motivo_personalizado' => $request->input('motivo_caja_chica_libre'),
                    'ip'                   => $request->ip(),
                    'user_agent'           => substr((string) $request->userAgent(), 0, 255),
                ]);
            }

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

    public function destroy(GestorEscolar $personal)
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
