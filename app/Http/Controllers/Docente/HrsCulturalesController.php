<?php

namespace App\Http\Controllers\Docente;

use App\Http\Controllers\Controller;
use App\Models\HrsCulturalesDeportivas;
use App\Models\Alumno;
use Illuminate\Http\Request;

/**
 * Gestion de Horas ACUDE por parte del docente.
 *
 * LIMITE_ACUDE: tope institucional total (acumulado) de horas ACUDE por alumno.
 */


class HrsCulturalesController extends Controller
{
    private const LIMITE_ACUDE = 90;

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

    public function create(Request $request)
    {
        $docente = $request->user()->docente;
        $alumnos = $docente
            ? Alumno::whereHas('inscripciones.grupo.horarios', fn($q) =>
                $q->where('id_docente', $docente->id_docente)
              )->orderBy('apellidos')->get()
            : collect();

        return view('docente.horas-culturales.create', compact('alumnos'));
    }

    public function store(Request $request)
    {
        $docente = $request->user()->docente;

        // Solo permitimos registrar horas a alumnos que pertenecen a los
        // grupos del docente logueado — de lo contrario el registro no
        // aparece en el listado por el filtro de index().
        $alumnosPermitidos = $docente
            ? Alumno::whereHas('inscripciones.grupo.horarios', fn($q) =>
                $q->where('id_docente', $docente->id_docente)
              )->pluck('id_alumno')->all()
            : [];

        $request->validate([
            'id_alumno' => ['required', 'exists:alumno,id_alumno', \Illuminate\Validation\Rule::in($alumnosPermitidos)],
            'horas_acumuladas' => ['required', 'integer', 'min:1', 'max:'.self::LIMITE_ACUDE],
            'descripcion' => 'nullable|string|max:255',
        ], [
            'id_alumno.in' => 'Solo puedes registrar horas a alumnos de tus grupos.',
            'horas_acumuladas.max' => 'El limite institucional ACUDE es de '.self::LIMITE_ACUDE.' horas.',
        ]);

        // Validar tope acumulado por alumno
        $acumuladas = (float) HrsCulturalesDeportivas::where('id_alumno', $request->id_alumno)->sum('horas_acumuladas');
        $disponibles = max(0, self::LIMITE_ACUDE - $acumuladas);
        if ((float) $request->horas_acumuladas > $disponibles) {
            return back()
                ->withInput()
                ->withErrors(['horas_acumuladas' =>
                    'El alumno ya tiene '.$acumuladas.' h registradas. Solo puedes agregar hasta '.$disponibles.' h para no pasar el tope de '.self::LIMITE_ACUDE.' h.'
                ]);
        }

        HrsCulturalesDeportivas::create($request->only('id_alumno', 'horas_acumuladas', 'descripcion'));
        return redirect()->route('docente.horas-culturales.index')->with('success', 'Horas registradas.');
    }

    public function edit(HrsCulturalesDeportivas $horasCultural) { return view('docente.horas-culturales.edit', compact('horasCultural')); }

    public function update(Request $request, HrsCulturalesDeportivas $horasCultural)
    {
        $request->validate([
            'horas_acumuladas' => ['required', 'integer', 'min:1', 'max:'.self::LIMITE_ACUDE],
            'descripcion' => 'nullable|string|max:255',
        ], [
            'horas_acumuladas.max' => 'El limite institucional ACUDE es de '.self::LIMITE_ACUDE.' horas.',
        ]);

        // Tope acumulado por alumno excluyendo este mismo registro
        $acumuladas = (float) HrsCulturalesDeportivas::where('id_alumno', $horasCultural->id_alumno)
            ->where('id_registro', '!=', $horasCultural->id_registro)
            ->sum('horas_acumuladas');
        $disponibles = max(0, self::LIMITE_ACUDE - $acumuladas);
        if ((float) $request->horas_acumuladas > $disponibles) {
            return back()
                ->withInput()
                ->withErrors(['horas_acumuladas' =>
                    'El alumno tiene '.$acumuladas.' h en otros registros. Solo puedes dejar este registro en hasta '.$disponibles.' h para no pasar el tope de '.self::LIMITE_ACUDE.' h.'
                ]);
        }

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
