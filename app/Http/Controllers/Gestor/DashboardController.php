<?php

namespace App\Http\Controllers\Gestor;

use App\Http\Controllers\Controller;
use App\Models\Alumno;
use App\Models\BachilleratoPlan;
use App\Models\Carrera;
use App\Models\CicloEscolar;
use App\Models\Docente;
use App\Models\Grupo;
use App\Support\ContextoEducativo;

class DashboardController extends Controller
{
    public function index()
    {
        $esBachi = ContextoEducativo::actual() === ContextoEducativo::BACHILLERATO;

        // El scope global NivelEducativoScope ya filtra todas las queries.
        // En Universidad la 4a KPI son carreras; en Bachillerato son grupos.
        $stats = [
            'total_alumnos'     => Alumno::activos()->count(),
            'bajas_temporales'  => Alumno::where('estatus', 'baja_temporal')->count(),
            'total_docentes'    => Docente::count(),
            'cuarta_kpi_valor'  => $esBachi
                ? Grupo::count()
                : Carrera::misCarreras()->count(),
            'cuarta_kpi_label'  => $esBachi ? 'Grupos' : 'Carreras',
            'ciclo_activo'      => CicloEscolar::cicloActual(),
            'es_bachi'          => $esBachi,
        ];

        // Mantener clave 'total_carreras' por compatibilidad con la vista actual.
        $stats['total_carreras'] = $stats['cuarta_kpi_valor'];

        return view('gestor.dashboard', compact('stats'));
    }
}
