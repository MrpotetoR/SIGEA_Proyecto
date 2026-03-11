<?php

namespace App\Http\Controllers\Docente;

use App\Http\Controllers\Controller;
use App\Models\HrsCulturalesDeportivas;
use App\Models\Alumno;
use Illuminate\Http\Request;

class HrsCulturalesController extends Controller
{
    public function index(Request $request)
    {
        $docente = $request->user()->docente;
        $alumnos = $docente
            ? Alumno::whereHas('inscripciones.grupo.horarios', fn($q) =>
                $q->where('id_docente', $docente->id_docente)
              )->with('carrera')->get()
            : collect();

        $registros = HrsCulturalesDeportivas::whereIn('id_alumno', $alumnos->pluck('id_alumno'))
            ->with('alumno')
            ->orderByDesc('id_registro')
            ->paginate(20);

        return view('docente.horas-culturales.index', compact('docente', 'alumnos', 'registros'));
    }

    public function create() { return view('docente.horas-culturales.create'); }

    public function store(Request $request)
    {
        $request->validate([
            'id_alumno' => 'required|exists:alumno,id_alumno',
            'tipo' => 'required|in:cultural,deportiva',
            'horas_acumuladas' => 'required|numeric|min:0.5',
            'descripcion' => 'nullable|string|max:255',
        ]);

        HrsCulturalesDeportivas::create($request->only('id_alumno', 'tipo', 'horas_acumuladas', 'descripcion'));
        return redirect()->route('docente.horas-culturales.index')->with('success', 'Horas registradas.');
    }

    public function edit(HrsCulturalesDeportivas $horasCultural) { return view('docente.horas-culturales.edit', compact('horasCultural')); }

    public function update(Request $request, HrsCulturalesDeportivas $horasCultural)
    {
        $request->validate([
            'horas_acumuladas' => 'required|numeric|min:0.5',
            'descripcion' => 'nullable|string|max:255',
        ]);
        $horasCultural->update($request->only('horas_acumuladas', 'descripcion'));
        return redirect()->route('docente.horas-culturales.index')->with('success', 'Registro actualizado.');
    }

    public function destroy(HrsCulturalesDeportivas $horasCultural)
    {
        $horasCultural->delete();
        return redirect()->route('docente.horas-culturales.index')->with('success', 'Registro eliminado.');
    }

    public function show(HrsCulturalesDeportivas $horasCultural) { return view('docente.horas-culturales.show', compact('horasCultural')); }
}
