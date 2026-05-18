<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Alumno;
use App\Models\BachilleratoPlan;
use App\Models\Carrera;
use App\Models\CicloEscolar;
use App\Models\Docente;
use App\Models\Grupo;
use App\Models\Materia;

/**
 * Reportes consolidados a nivel institucional.
 *
 * A diferencia del panel del Gestor (que esta filtrado por nivel via el
 * scope global NivelEducativoScope), aqui usamos `sinFiltroNivel()` para ver
 * la institucion completa.
 */
class ReportesConsolidadosController extends Controller
{
    public function index()
    {
        // Totales por nivel.
        $alumnosPorNivel = [
            'universidad'  => Alumno::sinFiltroNivel()->where('nivel_educativo', 'universidad')->count(),
            'bachillerato' => Alumno::sinFiltroNivel()->where('nivel_educativo', 'bachillerato')->count(),
        ];

        $alumnosActivosPorNivel = [
            'universidad'  => Alumno::sinFiltroNivel()->where('nivel_educativo', 'universidad')->where('estatus', 'activo')->count(),
            'bachillerato' => Alumno::sinFiltroNivel()->where('nivel_educativo', 'bachillerato')->where('estatus', 'activo')->count(),
        ];

        $docentesPorNivel = [
            'universidad'  => Docente::sinFiltroDeCarreras()->sinFiltroNivel()->where('nivel_educativo', 'universidad')->count(),
            'bachillerato' => Docente::sinFiltroDeCarreras()->sinFiltroNivel()->where('nivel_educativo', 'bachillerato')->count(),
        ];

        $gruposPorNivel = [
            'universidad'  => Grupo::sinFiltroDeCarreras()->sinFiltroNivel()->where('nivel_educativo', 'universidad')->count(),
            'bachillerato' => Grupo::sinFiltroDeCarreras()->sinFiltroNivel()->where('nivel_educativo', 'bachillerato')->count(),
        ];

        $materiasPorNivel = [
            'universidad'  => Materia::sinFiltroDeCarreras()->sinFiltroNivel()->where('nivel_educativo', 'universidad')->count(),
            'bachillerato' => Materia::sinFiltroDeCarreras()->sinFiltroNivel()->where('nivel_educativo', 'bachillerato')->count(),
        ];

        // Otros totales unicos.
        $totalCarreras = Carrera::count();
        $totalPlanes   = BachilleratoPlan::count();
        $cicloActivo   = CicloEscolar::cicloActual();

        // Distribucion de alumnos por carrera (Universidad).
        $alumnosPorCarrera = Carrera::withCount(['alumnos' => fn($q) => $q->where('estatus', 'activo')])
            ->orderByDesc('alumnos_count')
            ->take(10)
            ->get();

        // Distribucion de alumnos por plan (Bachillerato).
        $alumnosPorPlan = BachilleratoPlan::withCount(['alumnos' => fn($q) => $q->where('estatus', 'activo')])
            ->orderByDesc('alumnos_count')
            ->get();

        return view('admin.reportes-consolidados', compact(
            'alumnosPorNivel',
            'alumnosActivosPorNivel',
            'docentesPorNivel',
            'gruposPorNivel',
            'materiasPorNivel',
            'totalCarreras',
            'totalPlanes',
            'cicloActivo',
            'alumnosPorCarrera',
            'alumnosPorPlan',
        ));
    }
}
