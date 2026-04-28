<?php

namespace App\Http\Controllers\Servicios;

use App\Http\Controllers\Controller;
use App\Models\Carrera;
use App\Models\Materia;
use Illuminate\Http\Request;

class MateriasController extends Controller
{
    public function index(Request $request)
    {
        $materias = Materia::with('carrera')
            ->when($request->carrera_id, fn($q) => $q->where('id_carrera', $request->carrera_id))
            ->orderBy('cuatrimestre')->orderBy('nombre_materia')
            ->paginate(25)->withQueryString();

        $carreras = Carrera::misCarreras()->orderBy('nombre_carrera')->get();
        return view('servicios.materias.index', compact('materias', 'carreras'));
    }

    public function create()
    {
        $carreras = Carrera::misCarreras()->orderBy('nombre_carrera')->get();
        return view('servicios.materias.create', compact('carreras'));
    }

    public function store(Request $request)
    {
        $carreraSel = Carrera::find($request->id_carrera);
        $maxPeriodos = $carreraSel?->max_periodos ?? 10;

        $request->validate([
            'id_carrera'     => 'required|exists:carrera,id_carrera',
            'nombre_materia' => 'required|string|max:120',
            'cuatrimestre'   => "required|integer|min:1|max:{$maxPeriodos}",
            'horas_semana'   => 'required|integer|min:1',
        ], [
            'cuatrimestre.max' => "La carrera seleccionada solo tiene {$maxPeriodos} " . ($carreraSel?->tipo_periodo === 'semestre' ? 'semestres' : 'cuatrimestres') . '.',
        ]);
        Materia::create($request->only('id_carrera', 'nombre_materia', 'cuatrimestre', 'horas_semana'));
        return redirect()->route('servicios.materias.index')->with('success', 'Materia creada.');
    }

    public function show(Materia $materia) { return view('servicios.materias.show', compact('materia')); }

    public function edit(Materia $materia)
    {
        $carreras = Carrera::misCarreras()->orderBy('nombre_carrera')->get();
        return view('servicios.materias.edit', compact('materia', 'carreras'));
    }

    public function update(Request $request, Materia $materia)
    {
        $maxPeriodos = $materia->carrera?->max_periodos ?? 10;
        $request->validate([
            'nombre_materia' => 'required|string|max:120',
            'cuatrimestre'   => "required|integer|min:1|max:{$maxPeriodos}",
            'horas_semana'   => 'required|integer|min:1',
        ], [
            'cuatrimestre.max' => "Esta carrera solo tiene {$maxPeriodos} " . ($materia->carrera?->tipo_periodo === 'semestre' ? 'semestres' : 'cuatrimestres') . '.',
        ]);
        $materia->update($request->only('nombre_materia', 'cuatrimestre', 'horas_semana'));
        return redirect()->route('servicios.materias.index')->with('success', 'Materia actualizada.');
    }

    public function destroy(Materia $materia)
    {
        $materia->delete();
        return redirect()->route('servicios.materias.index')->with('success', 'Materia eliminada.');
    }
}
