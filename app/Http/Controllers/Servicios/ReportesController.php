<?php

namespace App\Http\Controllers\Servicios;

use App\Http\Controllers\Controller;
use App\Models\Alumno;
use App\Models\Carrera;
use App\Models\CicloEscolar;
use App\Services\EstadisticasCarreraService;
use Illuminate\Http\Request;

class ReportesController extends Controller
{
    public function __construct(private EstadisticasCarreraService $estadisticas) {}

    public function index(Request $request)
    {
        $carreras = Carrera::orderBy('nombre_carrera')->get();
        $ciclos = CicloEscolar::orderByDesc('fecha_inicio')->get();

        $reporte = null;
        if ($request->filled('carrera_id') && $request->filled('ciclo_id')) {
            $carrera = Carrera::find($request->carrera_id);
            $ciclo = CicloEscolar::find($request->ciclo_id);

            if ($carrera && $ciclo) {
                $reporte = [
                    'carrera' => $carrera,
                    'ciclo' => $ciclo,
                    'aprobacion' => $this->estadisticas->indiceAprobacion($carrera, $ciclo),
                    'semaforo' => $this->estadisticas->distribucionSemaforo($carrera),
                    'evaluacion_docentes' => $this->estadisticas->promedioEvaluacionDocente($carrera, $ciclo),
                ];
            }
        }

        return view('servicios.reportes', compact('carreras', 'ciclos', 'reporte'));
    }
}
