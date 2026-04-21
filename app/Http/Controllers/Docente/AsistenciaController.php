<?php

namespace App\Http\Controllers\Docente;

use App\Http\Controllers\Controller;
use App\Models\Asistencia;
use App\Models\Grupo;
use App\Services\AsistenciaService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AsistenciaController extends Controller
{
    public function __construct(private AsistenciaService $service) {}

    public function index(Request $request)
    {
        $docente = $request->user()->docente;
        $grupos = $docente
            ? $docente->horarios()->with('grupo.carrera', 'materia')->get()->groupBy('id_grupo')
            : collect();

        return view('docente.asistencia.index', compact('docente', 'grupos'));
    }

    public function show(Request $request, Grupo $grupo)
    {
        $fecha = $request->input('fecha', today()->toDateString());
        $horario = $grupo->horarios()
            ->where('id_docente', $request->user()->docente->id_docente)
            ->with('materia')
            ->first();

        $alumnos = $grupo->alumnos()->orderBy('apellidos')->get();

        $asistencias = collect();
        if ($horario) {
            $asistencias = Asistencia::where('id_horario', $horario->id_horario)
                ->where('fecha', $fecha)
                ->pluck('estatus', 'id_alumno');
        }

        return view('docente.asistencia.show', compact('grupo', 'horario', 'alumnos', 'asistencias', 'fecha'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_grupo' => 'required|exists:grupo,id_grupo',
            'id_horario' => 'required|exists:horario,id_horario',
            'fecha' => 'required|date',
            'asistencia' => 'required|array',
        ]);

        $this->service->registrarAsistencia(
            \App\Models\Horario::find($request->id_horario),
            Carbon::parse($request->fecha),
            $request->asistencia
        );

        return back()->with('success', 'Asistencia registrada correctamente.');
    }

    /**
     * Cuadrícula tipo Excel: Alumno × Fechas del período, con totales por alumno
     * (derecha) y totales por día (abajo). Resalta filas con ≥3 faltas en rojo suave.
     */
    public function historial(Request $request, Grupo $grupo)
    {
        $horario = $grupo->horarios()
            ->where('id_docente', $request->user()->docente->id_docente)
            ->with('materia')
            ->first();

        if (!$horario) abort(404);

        // Período: por defecto mes actual. Acepta rango personalizado.
        $desde = $request->input('desde', today()->startOfMonth()->toDateString());
        $hasta = $request->input('hasta', today()->toDateString());

        $alumnos = $grupo->alumnos()->orderBy('apellidos')->get();

        // Todas las asistencias del horario en el rango, indexadas por alumno y fecha.
        $registros = Asistencia::where('id_horario', $horario->id_horario)
            ->whereBetween('fecha', [$desde, $hasta])
            ->get()
            ->groupBy('id_alumno')
            ->map(fn($colec) => $colec->keyBy(fn($a) => Carbon::parse($a->fecha)->toDateString()));

        // Fechas únicas capturadas, ordenadas.
        $fechas = Asistencia::where('id_horario', $horario->id_horario)
            ->whereBetween('fecha', [$desde, $hasta])
            ->distinct()
            ->orderBy('fecha')
            ->pluck('fecha')
            ->map(fn($f) => Carbon::parse($f)->toDateString())
            ->values();

        // Matriz alumno→fecha→estatus + totales por alumno.
        $matriz = [];
        $totalesPorDia = [];
        foreach ($fechas as $f) {
            $totalesPorDia[$f] = ['presente' => 0, 'ausente' => 0, 'retardo' => 0];
        }

        foreach ($alumnos as $a) {
            $fila = ['alumno' => $a, 'dias' => [], 'presentes' => 0, 'faltas' => 0, 'retardos' => 0, 'total' => 0];
            $regsAlumno = $registros[$a->id_alumno] ?? collect();

            foreach ($fechas as $f) {
                $reg = $regsAlumno[$f] ?? null;
                $estatus = $reg?->estatus;
                $fila['dias'][$f] = $estatus;

                if ($estatus) {
                    $fila['total']++;
                    $totalesPorDia[$f][$estatus]++;
                    match ($estatus) {
                        'presente' => $fila['presentes']++,
                        'ausente'  => $fila['faltas']++,
                        'retardo'  => $fila['retardos']++,
                        default    => null,
                    };
                }
            }

            $fila['porcentaje'] = $fila['total'] > 0
                ? round(($fila['presentes'] / $fila['total']) * 100, 1)
                : 0;

            $matriz[] = $fila;
        }

        return view('docente.asistencia.historial', compact(
            'grupo', 'horario', 'fechas', 'matriz', 'totalesPorDia', 'desde', 'hasta'
        ));
    }

    public function pdf(Request $request, Grupo $grupo)
    {
        $horario = $grupo->horarios()
            ->where('id_docente', $request->user()->docente->id_docente)
            ->with('materia')
            ->first();

        if (!$horario) abort(404);

        $path = $this->service->generarListaPDF($grupo, $horario->materia);
        return response()->download($path);
    }
}
