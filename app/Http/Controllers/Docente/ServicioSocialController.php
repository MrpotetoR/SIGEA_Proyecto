<?php

namespace App\Http\Controllers\Docente;

use App\Http\Controllers\Controller;
use App\Models\ServicioSocial;
use App\Models\Alumno;
use Illuminate\Http\Request;

class ServicioSocialController extends Controller
{
    public function index(Request $request)
    {
        $docente = $request->user()->docente;
        $alumnos = $docente
            ? Alumno::whereHas('inscripciones.grupo.horarios', fn($q) =>
                $q->where('id_docente', $docente->id_docente)
              )->with('servicioSocial')->get()
            : collect();

        return view('docente.servicio-social.index', compact('docente', 'alumnos'));
    }

    public function create() { return view('docente.servicio-social.create'); }

    public function store(Request $request)
    {
        $request->validate([
            'id_alumno' => 'required|exists:alumno,id_alumno',
            'horas_acumuladas' => 'required|numeric|min:0',
            'estatus' => 'required|in:en_curso,completado',
        ]);

        ServicioSocial::updateOrCreate(
            ['id_alumno' => $request->id_alumno],
            $request->only('horas_acumuladas', 'estatus')
        );

        return redirect()->route('docente.servicio-social.index')->with('success', 'Servicio social actualizado.');
    }

    public function edit(ServicioSocial $servicioSocial) { return view('docente.servicio-social.edit', compact('servicioSocial')); }

    public function update(Request $request, ServicioSocial $servicioSocial)
    {
        $request->validate([
            'horas_acumuladas' => 'required|numeric|min:0',
            'estatus' => 'required|in:en_curso,completado',
        ]);
        $servicioSocial->update($request->only('horas_acumuladas', 'estatus'));
        return redirect()->route('docente.servicio-social.index')->with('success', 'Actualizado.');
    }

    public function destroy(ServicioSocial $servicioSocial)
    {
        $servicioSocial->delete();
        return redirect()->route('docente.servicio-social.index')->with('success', 'Eliminado.');
    }

    public function show(ServicioSocial $servicioSocial) { return view('docente.servicio-social.show', compact('servicioSocial')); }
}
