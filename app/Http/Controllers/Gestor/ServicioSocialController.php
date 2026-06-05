<?php

namespace App\Http\Controllers\Gestor;

use App\Http\Controllers\Controller;
use App\Models\Alumno;
use App\Models\ServicioSocial;
use Illuminate\Http\Request;

/**
 * Gestion de Servicio Social — responsabilidad del Gestor Escolar.
 *
 * El gestor puede registrar, editar y eliminar el servicio social de
 * cualquier alumno bajo su contexto activo (Universidad/Bachillerato).
 * El scope global NivelEducativoScope ya filtra los alumnos visibles.
 *
 * Las horas requeridas por alumno se inicializan con el default de su carrera
 * (`carrera.horas_servicio_social_default`) y el gestor puede sobreescribir
 * caso por caso (ej. Nutrición hospitalaria vs básica).
 */
class ServicioSocialController extends Controller
{
    /** Tope máximo absoluto de horas de SS (para validación sanity-check). */
    private const TOPE_ABSOLUTO_HORAS = 2000;

    public function index(Request $request)
    {
        $alumnos = Alumno::with('servicioSocial', 'carrera', 'planBachillerato')
            ->when($request->buscar, fn($q) =>
                $q->where(fn($w) =>
                    $w->where('nombre', 'like', "%{$request->buscar}%")
                      ->orWhere('apellidos', 'like', "%{$request->buscar}%")
                      ->orWhere('id_alumno_publico', 'like', "%{$request->buscar}%")
                )
            )
            ->when($request->estatus_ss, function ($q) use ($request) {
                if ($request->estatus_ss === 'sin_registro') {
                    $q->whereDoesntHave('servicioSocial');
                } else {
                    $q->whereHas('servicioSocial', fn($w) => $w->where('estatus', $request->estatus_ss));
                }
            })
            ->orderBy('apellidos')
            ->paginate(25)->withQueryString();

        return view('gestor.servicio-social.index', compact('alumnos'));
    }

    public function create()
    {
        // Solo alumnos del contexto activo (scope global) que aun no tengan SS registrado.
        $alumnos = Alumno::with('carrera')
            ->whereDoesntHave('servicioSocial')
            ->orderBy('apellidos')->orderBy('nombre')->get();

        return view('gestor.servicio-social.create', compact('alumnos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_alumno'        => 'required|exists:alumno,id_alumno',
            'institucion'      => ['nullable', 'string', 'max:150', 'regex:/^[\pL\pN\s]+$/u'],
            'horas_acumuladas' => ['required', 'numeric', 'min:0', 'max:' . self::TOPE_ABSOLUTO_HORAS],
            'horas_requeridas' => ['nullable', 'numeric', 'min:0', 'max:' . self::TOPE_ABSOLUTO_HORAS],
            'estatus'          => 'required|in:en_curso,completado',
        ], [
            'institucion.regex'    => 'La institución solo puede contener letras y números.',
            'horas_acumuladas.max' => 'El tope absoluto de horas es ' . self::TOPE_ABSOLUTO_HORAS . '.',
            'horas_requeridas.max' => 'El tope absoluto de horas es ' . self::TOPE_ABSOLUTO_HORAS . '.',
        ]);

        // Horas requeridas: lo que mande el gestor, o el default de la carrera del alumno, o 480 como último fallback.
        $alumno = Alumno::with('carrera')->findOrFail($request->id_alumno);
        $horasRequeridas = $request->filled('horas_requeridas')
            ? (float) $request->horas_requeridas
            : (float) ($alumno->carrera?->horas_servicio_social_default ?? 480);

        $horasAcumuladas = (float) $request->horas_acumuladas;
        $estatus = $horasAcumuladas >= $horasRequeridas ? 'completado' : $request->estatus;

        ServicioSocial::updateOrCreate(
            ['id_alumno' => $request->id_alumno],
            [
                'institucion'      => $request->institucion,
                'horas_acumuladas' => $horasAcumuladas,
                'horas_requeridas' => $horasRequeridas,
                'estatus'          => $estatus,
            ]
        );

        return redirect()->route('gestor.servicio-social.index')
            ->with('success', 'Servicio social registrado.');
    }

    public function show(ServicioSocial $servicioSocial)
    {
        return view('gestor.servicio-social.show', compact('servicioSocial'));
    }

    public function edit(ServicioSocial $servicioSocial)
    {
        return view('gestor.servicio-social.edit', compact('servicioSocial'));
    }

    public function update(Request $request, ServicioSocial $servicioSocial)
    {
        $request->validate([
            'institucion'      => ['nullable', 'string', 'max:150', 'regex:/^[\pL\pN\s]+$/u'],
            'horas_acumuladas' => ['required', 'numeric', 'min:0', 'max:' . self::TOPE_ABSOLUTO_HORAS],
            'horas_requeridas' => ['required', 'numeric', 'min:0', 'max:' . self::TOPE_ABSOLUTO_HORAS],
            'estatus'          => 'required|in:en_curso,completado',
        ], [
            'institucion.regex'    => 'La institución solo puede contener letras y números.',
            'horas_acumuladas.max' => 'El tope absoluto de horas es ' . self::TOPE_ABSOLUTO_HORAS . '.',
            'horas_requeridas.max' => 'El tope absoluto de horas es ' . self::TOPE_ABSOLUTO_HORAS . '.',
        ]);

        $horasAcumuladas = (float) $request->horas_acumuladas;
        $horasRequeridas = (float) $request->horas_requeridas;
        $estatus = $horasAcumuladas >= $horasRequeridas ? 'completado' : $request->estatus;

        $servicioSocial->update([
            'institucion'      => $request->institucion,
            'horas_acumuladas' => $horasAcumuladas,
            'horas_requeridas' => $horasRequeridas,
            'estatus'          => $estatus,
        ]);

        return redirect()->route('gestor.servicio-social.index')
            ->with('success', 'Servicio social actualizado.');
    }

    public function destroy(ServicioSocial $servicioSocial)
    {
        $servicioSocial->delete();
        return redirect()->route('gestor.servicio-social.index')
            ->with('success', 'Registro eliminado.');
    }
}
