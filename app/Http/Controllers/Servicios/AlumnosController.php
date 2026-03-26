<?php

namespace App\Http\Controllers\Servicios;

use App\Http\Controllers\Controller;
use App\Models\Alumno;
use App\Models\Carrera;
use App\Models\HistorialBaja;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        return view('servicios.alumnos.create', compact('carreras'));
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

            Alumno::create([
                'user_id' => $user->id,
                'id_carrera' => $request->id_carrera,
                'id_tutor' => $request->id_tutor,
                'matricula' => $matricula,
                'nombre' => $request->nombre,
                'apellidos' => $request->apellidos,
                'cuatrimestre_actual' => $request->cuatrimestre_actual,
                'estatus' => 'activo',
            ]);
        });

        return redirect()->route('servicios.alumnos.index')->with('success', 'Alumno registrado correctamente.');
    }

    public function show(Alumno $alumno)
    {
        $alumno->load('carrera', 'tutor', 'user', 'inscripciones.grupo', 'servicioSocial', 'constancias');
        return view('servicios.alumnos.show', compact('alumno'));
    }

    public function edit(Alumno $alumno)
    {
        $carreras = Carrera::orderBy('nombre_carrera')->get();
        return view('servicios.alumnos.edit', compact('alumno', 'carreras'));
    }

    public function update(Request $request, Alumno $alumno)
    {
        $request->validate([
            'nombre'              => ['required', 'string', 'max:80', 'regex:/^[\pL\s]+$/u'],
            'apellidos'           => ['required', 'string', 'max:100', 'regex:/^[\pL\s]+$/u'],
            'id_carrera'          => 'required|exists:carrera,id_carrera',
            'cuatrimestre_actual' => 'required|integer|min:1|max:10',
            'id_tutor'            => 'nullable|exists:docente,id_docente',
        ], [
            'nombre.regex'    => 'El nombre solo debe contener letras y espacios.',
            'apellidos.regex' => 'Los apellidos solo deben contener letras y espacios.',
        ]);

        $alumno->update($request->only('nombre', 'apellidos', 'id_carrera', 'cuatrimestre_actual', 'id_tutor'));

        return redirect()->route('servicios.alumnos.index')->with('success', 'Alumno actualizado.');
    }

    public function destroy(Alumno $alumno)
    {
        $alumno->user->update(['activo' => false]);
        $alumno->update(['estatus' => 'baja_definitiva']);
        return redirect()->route('servicios.alumnos.index')->with('success', 'Alumno dado de baja definitiva.');
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
