<?php

namespace App\Http\Controllers\Docente;

use App\Http\Controllers\Controller;
use App\Models\ServicioSocial;
use App\Models\Alumno;
use Illuminate\Http\Request;

/**
 * Gestion de Servicio Social por parte del docente.
 *
 * LIMITE_SS: tope institucional total de horas de servicio social por alumno.
 */
class ServicioSocialController extends Controller
{
    private const LIMITE_SS = 160;

    public function index(Request $request)
    {
        $docente = $request->user()->docente;
        $alumnos = $docente
            ? Alumno::whereHas('inscripciones.grupo.horarios', fn($q) =>
                $q->where('id_docente', $docente->id_docente)
              )->with('servicioSocial')->orderBy('apellidos')->get()
            : collect();

        return view('docente.servicio-social.index', compact('docente', 'alumnos'));
    }

    public function create(Request $request)
    {
        $docente = $request->user()->docente;
        $alumnos = $docente
            ? Alumno::whereHas('inscripciones.grupo.horarios', fn($q) =>
                $q->where('id_docente', $docente->id_docente)
              )->orderBy('apellidos')->get()
            : collect();

        return view('docente.servicio-social.create', compact('alumnos'));
    }

    public function store(Request $request)
    {
        $docente = $request->user()->docente;

        // Solo alumnos de los grupos del docente
        $alumnosPermitidos = $docente
            ? Alumno::whereHas('inscripciones.grupo.horarios', fn($q) =>
                $q->where('id_docente', $docente->id_docente)
              )->pluck('id_alumno')->all()
            : [];

        $request->validate([
            'id_alumno' => ['required', 'exists:alumno,id_alumno', \Illuminate\Validation\Rule::in($alumnosPermitidos)],
            'institucion' => ['nullable', 'string', 'max:150', 'regex:/^[\pL\pN\s]+$/u'],
            'horas_acumuladas' => ['required', 'numeric', 'min:0', 'max:'.self::LIMITE_SS],
            'estatus' => 'required|in:en_curso,completado',
        ], [
            'id_alumno.in' => 'Solo puedes registrar servicio social a alumnos de tus grupos.',
            'institucion.regex' => 'La institución solo puede contener letras y números.',
            'horas_acumuladas.max' => 'El limite institucional de servicio social es de '.self::LIMITE_SS.' horas.',
        ]);

        // Coherencia: si horas >= limite entonces estatus auto-completado
        $horas = (float) $request->horas_acumuladas;
        $estatus = $horas >= self::LIMITE_SS ? 'completado' : $request->estatus;

        ServicioSocial::updateOrCreate(
            ['id_alumno' => $request->id_alumno],
            [
                'institucion' => $request->institucion,
                'horas_acumuladas' => $horas,
                'horas_requeridas' => self::LIMITE_SS,
                'estatus' => $estatus,
            ]
        );

        return redirect()->route('docente.servicio-social.index')->with('success', 'Servicio social registrado.');
    }

    public function edit(ServicioSocial $servicioSocial)
    {
        return view('docente.servicio-social.edit', compact('servicioSocial'));
    }

    public function update(Request $request, ServicioSocial $servicioSocial)
    {
        $request->validate([
            'institucion' => ['nullable', 'string', 'max:150', 'regex:/^[\pL\pN\s]+$/u'],
            'horas_acumuladas' => ['required', 'numeric', 'min:0', 'max:'.self::LIMITE_SS],
            'estatus' => 'required|in:en_curso,completado',
        ], [
            'institucion.regex' => 'La institución solo puede contener letras y números.',
            'horas_acumuladas.max' => 'El limite institucional de servicio social es de '.self::LIMITE_SS.' horas.',
        ]);

        $horas = (float) $request->horas_acumuladas;
        $estatus = $horas >= self::LIMITE_SS ? 'completado' : $request->estatus;

        $servicioSocial->update([
            'institucion' => $request->institucion,
            'horas_acumuladas' => $horas,
            'horas_requeridas' => self::LIMITE_SS,
            'estatus' => $estatus,
        ]);

        return redirect()->route('docente.servicio-social.index')->with('success', 'Servicio social actualizado.');
    }

    public function destroy(ServicioSocial $servicioSocial)
    {
        $servicioSocial->delete();
        return redirect()->route('docente.servicio-social.index')->with('success', 'Registro eliminado.');
    }

    public function show(ServicioSocial $servicioSocial)
    {
        return view('docente.servicio-social.show', compact('servicioSocial'));
    }
}
