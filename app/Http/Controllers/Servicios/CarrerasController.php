<?php

namespace App\Http\Controllers\Servicios;

use App\Http\Controllers\Controller;
use App\Models\Carrera;
use App\Models\Docente;
use Illuminate\Http\Request;

class CarrerasController extends Controller
{
    public function index(Request $request)
    {
        $carreras = Carrera::misCarreras()
            ->with('director')->withCount('alumnos', 'materias')
            ->when($request->tipo_periodo, fn($q, $v) => $q->where('tipo_periodo', $v))
            ->get();
        return view('servicios.carreras.index', compact('carreras'));
    }

    public function create()
    {
        $docentes = \App\Models\Docente::orderBy('apellidos')->orderBy('nombre')->get();
        return view('servicios.carreras.create', compact('docentes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre_carrera'    => 'required|string|max:120',
            'clave_carrera'     => 'required|string|max:20|unique:carrera,clave_carrera',
            'id_director'       => 'nullable|exists:docente,id_docente',
            'area_academica'    => 'required|in:' . implode(',', array_keys(Carrera::AREAS_ACADEMICAS)),
            'tipo_periodo'      => 'required|in:' . implode(',', array_keys(Carrera::TIPOS_PERIODO)),
        ], [
            'tipo_periodo.required' => 'Debes seleccionar si la carrera es por cuatrimestre o semestre.',
            'tipo_periodo.in'       => 'El tipo de periodo seleccionado no es válido.',
        ]);

        // duracion_periodos la fija el modelo (10 cuatrimestres / 7 semestres) automáticamente
        $carrera = Carrera::create($request->only('nombre_carrera', 'clave_carrera', 'id_director', 'area_academica', 'tipo_periodo'));
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
        $docentes = \App\Models\Docente::orderBy('apellidos')->orderBy('nombre')->get();
        return view('servicios.carreras.edit', compact('carrera', 'docentes'));
    }

    public function update(Request $request, Carrera $carrera)
    {
        $request->validate([
            'nombre_carrera' => 'required|string|max:120',
            'id_director'    => 'nullable|exists:docente,id_docente',
            'area_academica' => 'required|in:' . implode(',', array_keys(Carrera::AREAS_ACADEMICAS)),
        ]);

        // tipo_periodo es inmutable una vez creada la carrera — el modelo también lo blinda.
        $directorAnterior = $carrera->id_director;
        $carrera->update($request->only('nombre_carrera', 'id_director', 'area_academica'));

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
