<?php

namespace App\Http\Controllers\Gestor;

use App\Http\Controllers\Controller;
use App\Models\BachilleratoPlan;
use App\Models\Carrera;
use App\Models\Materia;
use App\Support\ContextoEducativo;
use Illuminate\Http\Request;

class MateriasController extends Controller
{
    public function index(Request $request)
    {
        $esBachi = ContextoEducativo::actual() === ContextoEducativo::BACHILLERATO;

        $materias = Materia::with($esBachi ? 'planBachillerato' : 'carrera')
            ->when(!$esBachi && $request->carrera_id, fn($q) => $q->where('id_carrera', $request->carrera_id))
            ->when($esBachi && $request->plan_id, fn($q) => $q->where('id_plan_bachillerato', $request->plan_id))
            ->orderBy('cuatrimestre')->orderBy('nombre_materia')
            ->paginate(25)->withQueryString();

        $carreras    = $esBachi ? collect() : Carrera::misCarreras()->orderBy('nombre_carrera')->get();
        $planesBachi = $esBachi ? BachilleratoPlan::vigente()->orderBy('nombre_plan')->get() : collect();

        return view('gestor.materias.index', compact('materias', 'carreras', 'planesBachi', 'esBachi'));
    }

    public function create()
    {
        $esBachi = ContextoEducativo::actual() === ContextoEducativo::BACHILLERATO;
        $carreras    = $esBachi ? collect() : Carrera::misCarreras()->orderBy('nombre_carrera')->get();
        $planesBachi = $esBachi ? BachilleratoPlan::vigente()->orderBy('nombre_plan')->get() : collect();

        return view('gestor.materias.create', compact('carreras', 'planesBachi', 'esBachi'));
    }

    public function store(Request $request)
    {
        $contexto = ContextoEducativo::actual();
        $esBachi  = $contexto === ContextoEducativo::BACHILLERATO;

        if ($esBachi) {
            $plan = BachilleratoPlan::find($request->id_plan_bachillerato);
            $maxPeriodos = $plan?->num_semestres ?? 6;
        } else {
            $carreraSel = Carrera::find($request->id_carrera);
            $maxPeriodos = $carreraSel?->max_periodos ?? 10;
        }

        $request->validate([
            'id_carrera'           => $esBachi ? 'nullable' : 'required|exists:carrera,id_carrera',
            'id_plan_bachillerato' => $esBachi ? 'required|exists:bachillerato_plan,id_plan_bachillerato' : 'nullable',
            'nombre_materia'       => 'required|string|max:120',
            'cuatrimestre'         => "required|integer|min:1|max:{$maxPeriodos}",
            'horas_semana'         => 'nullable|integer|min:1|max:60',
        ]);

        Materia::create([
            'id_carrera'           => $esBachi ? null : $request->id_carrera,
            'id_plan_bachillerato' => $esBachi ? $request->id_plan_bachillerato : null,
            'nombre_materia'       => $request->nombre_materia,
            'cuatrimestre'         => $request->cuatrimestre,
            'horas_semana'         => $request->horas_semana,
            'nivel_educativo'      => $contexto,
        ]);

        return redirect()->route('gestor.materias.index')->with('success', 'Materia creada.');
    }

    public function show(Materia $materia) { return view('gestor.materias.show', compact('materia')); }

    public function edit(Materia $materia)
    {
        $esBachi = $materia->nivel_educativo === 'bachillerato';
        $carreras    = $esBachi ? collect() : Carrera::misCarreras()->orderBy('nombre_carrera')->get();
        $planesBachi = $esBachi ? BachilleratoPlan::vigente()->orderBy('nombre_plan')->get() : collect();

        return view('gestor.materias.edit', compact('materia', 'carreras', 'planesBachi', 'esBachi'));
    }

    public function update(Request $request, Materia $materia)
    {
        $esBachi = $materia->nivel_educativo === 'bachillerato';

        $maxPeriodos = $esBachi
            ? ($materia->planBachillerato?->num_semestres ?? 6)
            : ($materia->carrera?->max_periodos ?? 10);

        $request->validate([
            'nombre_materia' => 'required|string|max:120',
            'cuatrimestre'   => "required|integer|min:1|max:{$maxPeriodos}",
            'horas_semana'   => 'nullable|integer|min:1|max:60',
        ]);

        $materia->update($request->only('nombre_materia', 'cuatrimestre', 'horas_semana'));
        return redirect()->route('gestor.materias.index')->with('success', 'Materia actualizada.');
    }

    public function destroy(Materia $materia)
    {
        $materia->delete();
        return redirect()->route('gestor.materias.index')->with('success', 'Materia eliminada.');
    }
}
