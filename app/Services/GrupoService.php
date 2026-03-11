<?php

namespace App\Services;

use App\Models\Alumno;
use App\Models\Docente;
use App\Models\Grupo;
use App\Models\Horario;
use Illuminate\Support\Collection;

class GrupoService
{
    public function crearGrupo(array $datos): Grupo
    {
        return Grupo::create([
            'id_carrera' => $datos['id_carrera'],
            'id_ciclo' => $datos['id_ciclo'],
            'cuatrimestre' => $datos['cuatrimestre'],
            'clave_grupo' => $datos['clave_grupo'],
            'id_tutor' => $datos['id_tutor'] ?? null,
        ]);
    }

    public function asignarHorario(Grupo $grupo, array $horarioData): Horario
    {
        return Horario::create([
            'id_grupo' => $grupo->id_grupo,
            'id_docente' => $horarioData['id_docente'],
            'id_materia' => $horarioData['id_materia'],
            'dia_semana' => $horarioData['dia_semana'],
            'hora_inicio' => $horarioData['hora_inicio'],
            'hora_fin' => $horarioData['hora_fin'],
        ]);
    }

    public function asignarTutor(Grupo $grupo, Docente $docente): void
    {
        $grupo->update(['id_tutor' => $docente->id_docente]);
    }

    public function obtenerAlumnosDeGrupo(Grupo $grupo): Collection
    {
        return $grupo->alumnos()->with('carrera')->orderBy('apellidos')->get();
    }
}
