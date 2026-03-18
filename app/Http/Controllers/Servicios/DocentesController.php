<?php

namespace App\Http\Controllers\Servicios;

use App\Http\Controllers\Controller;
use App\Models\Docente;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        return view('servicios.docentes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:80',
            'apellidos' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'especialidad' => 'nullable|string|max:100',
            'horas_contrato' => 'nullable|integer|min:1|max:40',
            'es_tutor' => 'boolean',
        ]);

        DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => "{$request->nombre} {$request->apellidos}",
                'email' => $request->email,
                'password' => bcrypt('docente' . date('Y')),
                'activo' => true,
            ]);
            $user->assignRole('docente');

            Docente::create([
                'user_id' => $user->id,
                'nombre' => $request->nombre,
                'apellidos' => $request->apellidos,
                'especialidad' => $request->especialidad,
                'horas_contrato' => $request->tipo_contrato === 'planta' ? null : $request->horas_contrato,
                'es_tutor' => $request->boolean('es_tutor'),
            ]);
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
        return view('servicios.docentes.edit', compact('docente'));
    }

    public function update(Request $request, Docente $docente)
    {
        $request->validate([
            'nombre' => 'required|string|max:80',
            'apellidos' => 'required|string|max:100',
            'especialidad' => 'nullable|string|max:100',
            'horas_contrato' => 'nullable|integer|min:1|max:40',
        ]);
        $docente->update([
            'nombre'         => $request->nombre,
            'apellidos'      => $request->apellidos,
            'especialidad'   => $request->especialidad,
            'horas_contrato' => $request->tipo_contrato === 'planta' ? null : $request->horas_contrato,
            'es_tutor'       => $request->boolean('es_tutor'),
        ]);
        return redirect()->route('servicios.docentes.index')->with('success', 'Docente actualizado.');
    }

    public function destroy(Docente $docente)
    {
        $docente->user->update(['activo' => false]);
        return redirect()->route('servicios.docentes.index')->with('success', 'Docente desactivado.');
    }
}
