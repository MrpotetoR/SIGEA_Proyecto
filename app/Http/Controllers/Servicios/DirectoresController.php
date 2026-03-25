<?php

namespace App\Http\Controllers\Servicios;

use App\Http\Controllers\Controller;
use App\Models\Carrera;
use App\Models\Docente;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        $request->validate([
            'nombre'    => ['required', 'string', 'max:80', 'regex:/^[\pL\s]+$/u'],
            'apellidos' => ['required', 'string', 'max:100', 'regex:/^[\pL\s]+$/u'],
            'email'     => 'required|email|unique:users,email',
            'especialidad'   => 'nullable|string|max:100',
            'id_carrera'     => 'nullable|exists:carrera,id_carrera',
        ], [
            'nombre.regex'    => 'El nombre solo debe contener letras y espacios.',
            'apellidos.regex' => 'Los apellidos solo deben contener letras y espacios.',
        ]);

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
                'es_tutor'     => false,
            ]);

            // Asignar carrera si se seleccionó
            if ($request->id_carrera) {
                Carrera::where('id_carrera', $request->id_carrera)
                    ->update(['id_director' => $docente->id_docente]);
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
        $director->load('carrerasDirigidas');
        $carreras = Carrera::orderBy('nombre_carrera')->get();
        return view('servicios.directores.edit', compact('director', 'carreras'));
    }

    public function update(Request $request, Docente $directore)
    {
        $director = $directore;

        $request->validate([
            'nombre'    => ['required', 'string', 'max:80', 'regex:/^[\pL\s]+$/u'],
            'apellidos' => ['required', 'string', 'max:100', 'regex:/^[\pL\s]+$/u'],
            'especialidad'   => 'nullable|string|max:100',
            'id_carrera'     => 'nullable|exists:carrera,id_carrera',
        ], [
            'nombre.regex'    => 'El nombre solo debe contener letras y espacios.',
            'apellidos.regex' => 'Los apellidos solo deben contener letras y espacios.',
        ]);

        $director->update([
            'nombre'       => $request->nombre,
            'apellidos'    => $request->apellidos,
            'especialidad' => $request->especialidad,
        ]);

        // Desasignar carrera anterior y asignar nueva
        Carrera::where('id_director', $director->id_docente)->update(['id_director' => null]);
        if ($request->id_carrera) {
            Carrera::where('id_carrera', $request->id_carrera)
                ->update(['id_director' => $director->id_docente]);
        }

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
