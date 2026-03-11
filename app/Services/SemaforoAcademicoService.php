<?php

namespace App\Services;

use App\Models\Alumno;
use App\Models\CicloEscolar;
use App\Models\SemaforoAcademico;

class SemaforoAcademicoService
{
    public function calcularSemaforo(Alumno $alumno, CicloEscolar $ciclo): string
    {
        $promedio = $alumno->calificaciones()
            ->where('id_ciclo', $ciclo->id_ciclo)
            ->avg('calificacion') ?? 0;

        $totalAsistencias = $alumno->asistencias()
            ->whereHas('horario.grupo', fn($q) => $q->where('id_ciclo', $ciclo->id_ciclo))
            ->count();

        $presentes = $alumno->asistencias()
            ->whereHas('horario.grupo', fn($q) => $q->where('id_ciclo', $ciclo->id_ciclo))
            ->where('estatus', 'presente')
            ->count();

        $porcentajeAsistencia = $totalAsistencias > 0
            ? ($presentes / $totalAsistencias) * 100
            : 100;

        $nivel = match (true) {
            $promedio < 7 || $porcentajeAsistencia < 70 => 'rojo',
            $promedio < 8 || $porcentajeAsistencia < 80 => 'amarillo',
            default => 'verde',
        };

        SemaforoAcademico::updateOrCreate(
            ['id_alumno' => $alumno->id_alumno, 'id_ciclo' => $ciclo->id_ciclo],
            [
                'nivel' => $nivel,
                'promedio_calificaciones' => round($promedio, 2),
                'porcentaje_asistencia' => round($porcentajeAsistencia, 2),
            ]
        );

        return $nivel;
    }

    public function actualizarTodos(CicloEscolar $ciclo): void
    {
        Alumno::activos()->each(fn($alumno) => $this->calcularSemaforo($alumno, $ciclo));
    }

    public function enviarAlertasTutores(): void
    {
        $ciclo = CicloEscolar::cicloActual();
        if (!$ciclo) return;

        $alumnosEnRojo = \App\Models\SemaforoAcademico::where('id_ciclo', $ciclo->id_ciclo)
            ->where('nivel', 'rojo')
            ->with('alumno.tutor.user')
            ->get();

        foreach ($alumnosEnRojo as $semaforo) {
            // TODO: enviar notificación al tutor
            // Notificacion::send($semaforo->alumno->tutor->user, new AlertaSemaforoNotification($semaforo->alumno));
        }
    }
}
