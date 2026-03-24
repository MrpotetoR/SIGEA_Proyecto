<?php

namespace App\Http\Controllers\Servicios;

use App\Http\Controllers\Controller;
use App\Models\Carrera;
use App\Models\Docente;
use Illuminate\Http\Request;

class CarrerasController extends Controller
{
    public function index()
    {
        $carreras = Carrera::with('director')->withCount('alumnos', 'materias')->get();
        return view('servicios.carreras.index', compact('carreras'));
    }

    public function create()
    {
        $docentes = Docente::orderBy('apellidos')->get();
        return view('servicios.carreras.create', compact('docentes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre_carrera' => 'required|string|max:120',
            'clave_carrera' => 'required|string|max:20|unique:carrera,clave_carrera',
            'id_director' => 'nullable|exists:docente,id_docente',
        ]);

        $carrera = Carrera::create($request->only('nombre_carrera', 'clave_carrera', 'id_director'));
        $this->syncRolDirector($request->id_director);
        return redirect()->route('servicios.carreras.index')->with('success', 'Carrera creada.');
    }

    public function show(Carrera $carrera)
    {
        $carrera->load('director', 'alumnos', 'materias');
        return view('servicios.carreras.show', compact('carrera'));
    }

    public function edit(Carrera $carrera)
    {
        $docentes = Docente::orderBy('apellidos')->get();
        return view('servicios.carreras.edit', compact('carrera', 'docentes'));
    }

    public function update(Request $request, Carrera $carrera)
    {
        $request->validate([
            'nombre_carrera' => 'required|string|max:120',
            'id_director' => 'nullable|exists:docente,id_docente',
        ]);

        $directorAnterior = $carrera->id_director;
        $carrera->update($request->only('nombre_carrera', 'id_director'));

        // Quitar rol al director anterior si ya no dirige ninguna carrera
        if ($directorAnterior && $directorAnterior != $request->id_director) {
            $this->quitarRolSiNoEsDirector($directorAnterior);
        }

        $this->syncRolDirector($request->id_director);
        return redirect()->route('servicios.carreras.index')->with('success', 'Carrera actualizada.');
    }

    public function destroy(Carrera $carrera)
    {
        $directorId = $carrera->id_director;
        $carrera->delete();

        if ($directorId) {
            $this->quitarRolSiNoEsDirector($directorId);
        }

        return redirect()->route('servicios.carreras.index')->with('success', 'Carrera eliminada.');
    }

    /**
     * Asigna el rol director_carrera al docente indicado.
     */
    private function syncRolDirector(?int $docenteId): void
    {
        if (!$docenteId) return;

        $docente = Docente::find($docenteId);
        if ($docente?->user && !$docente->user->hasRole('director_carrera')) {
            $docente->user->assignRole('director_carrera');
        }
    }

    /**
     * Quita el rol director_carrera si el docente ya no dirige ninguna carrera.
     */
    private function quitarRolSiNoEsDirector(int $docenteId): void
    {
        $sigueDirector = Carrera::where('id_director', $docenteId)->exists();
        if (!$sigueDirector) {
            $docente = Docente::find($docenteId);
            $docente?->user?->removeRole('director_carrera');
        }
    }
}
