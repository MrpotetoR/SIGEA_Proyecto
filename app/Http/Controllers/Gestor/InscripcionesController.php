<?php

namespace App\Http\Controllers\Gestor;

use App\Http\Controllers\Controller;
use App\Models\Alumno;
use App\Models\Carrera;
use App\Models\CicloEscolar;
use App\Models\Grupo;
use App\Models\Inscripcion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InscripcionesController extends Controller
{
    public function index(Request $request)
    {
        $inscripciones = Inscripcion::with('alumno.carrera', 'grupo.cicloEscolar')
            ->when($request->grupo, fn($q) => $q->where('id_grupo', $request->grupo))
            ->when($request->carrera, fn($q) =>
                $q->whereHas('alumno', fn($a) => $a->where('id_carrera', $request->carrera))
            )
            ->when($request->ciclo, fn($q) =>
                $q->whereHas('grupo', fn($g) => $g->where('id_ciclo', $request->ciclo))
            )
            ->when($request->buscar, fn($q) =>
                $q->whereHas('alumno', fn($a) =>
                    $a->where('id_alumno_publico', 'like', "%{$request->buscar}%")
                      ->orWhere('nombre', 'like', "%{$request->buscar}%")
                      ->orWhere('apellidos', 'like', "%{$request->buscar}%")
                )
            )
            ->orderByDesc('fecha_inscripcion')
            ->paginate(25)->withQueryString();

        $grupos = Grupo::with('cicloEscolar', 'carrera')->orderBy('clave_grupo')->get();
        $carreras = Carrera::misCarreras()->orderBy('nombre_carrera')->get();
        $ciclos = CicloEscolar::orderByDesc('fecha_inicio')->get();

        return view('gestor.inscripciones.index', compact('inscripciones', 'grupos', 'carreras', 'ciclos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_alumno' => 'required|exists:alumno,id_alumno',
            'id_grupo' => 'required|exists:grupo,id_grupo',
        ]);

        $grupo = Grupo::findOrFail($request->id_grupo);

        // 1) Mismo alumno + mismo grupo (duplicado exacto)
        $existeMismoGrupo = Inscripcion::where('id_alumno', $request->id_alumno)
            ->where('id_grupo', $request->id_grupo)->exists();
        if ($existeMismoGrupo) {
            return back()->withInput()->with('error', 'El alumno ya se encuentra inscrito en este grupo y ciclo escolar.');
        }

        // 2) Mismo alumno en otro grupo del mismo ciclo escolar
        $existeMismoCiclo = Inscripcion::where('id_alumno', $request->id_alumno)
            ->whereHas('grupo', fn($g) => $g->where('id_ciclo', $grupo->id_ciclo))
            ->with('grupo')
            ->first();
        if ($existeMismoCiclo) {
            return back()->withInput()->with(
                'error',
                'El alumno ya se encuentra inscrito en este ciclo escolar (grupo '
                . $existeMismoCiclo->grupo->clave_grupo . '). Elimina esa inscripción primero si deseas cambiarlo.'
            );
        }

        Inscripcion::create([
            'id_alumno' => $request->id_alumno,
            'id_grupo' => $request->id_grupo,
            'fecha_inscripcion' => today(),
        ]);

        return back()->with('success', 'Inscripción realizada.');
    }

    /**
     * Endpoint AJAX: valida en tiempo real si el alumno ya tiene inscripción
     * en el mismo grupo o en otro grupo del mismo ciclo escolar.
     */
    public function check(Request $request)
    {
        $request->validate([
            'id_alumno' => 'required|exists:alumno,id_alumno',
            'id_grupo'  => 'required|exists:grupo,id_grupo',
        ]);

        $grupo = Grupo::with('cicloEscolar')->findOrFail($request->id_grupo);

        $mismoGrupo = Inscripcion::where('id_alumno', $request->id_alumno)
            ->where('id_grupo', $request->id_grupo)->exists();
        if ($mismoGrupo) {
            return response()->json([
                'conflict' => true,
                'message'  => 'El alumno ya se encuentra inscrito en este grupo y ciclo escolar.',
            ]);
        }

        $mismoCiclo = Inscripcion::where('id_alumno', $request->id_alumno)
            ->whereHas('grupo', fn($g) => $g->where('id_ciclo', $grupo->id_ciclo))
            ->with('grupo')->first();
        if ($mismoCiclo) {
            return response()->json([
                'conflict' => true,
                'message'  => 'El alumno ya está inscrito en este ciclo escolar (grupo '
                    . $mismoCiclo->grupo->clave_grupo . ').',
            ]);
        }

        return response()->json(['conflict' => false]);
    }

    public function destroy(Inscripcion $inscripcion)
    {
        $inscripcion->delete();
        return back()->with('success', 'Inscripción eliminada.');
    }

    /**
     * Pantalla de promoción masiva: lista los grupos disponibles para promover
     * y permite seleccionar uno como origen. Cuando se selecciona, la vista
     * llama a `previewPromocion` (AJAX) para obtener los alumnos + grupo destino.
     */
    public function promoverForm(Request $request)
    {
        $carreras = Carrera::misCarreras()->orderBy('nombre_carrera')->get();
        $ciclos = CicloEscolar::orderByDesc('fecha_inicio')->get();

        // Grupos disponibles como ORIGEN — filtrables por carrera/ciclo
        $grupos = Grupo::with('carrera', 'cicloEscolar')
            ->when($request->carrera, fn($q) => $q->where('id_carrera', $request->carrera))
            ->when($request->ciclo, fn($q) => $q->where('id_ciclo', $request->ciclo))
            ->orderBy('id_ciclo', 'desc')
            ->orderBy('id_carrera')
            ->orderBy('cuatrimestre')
            ->orderBy('clave_grupo')
            ->get();

        return view('gestor.inscripciones.promover', compact('grupos', 'carreras', 'ciclos'));
    }

    /**
     * Endpoint AJAX: recibe un grupo origen y devuelve:
     *   - Los alumnos actualmente inscritos en él
     *   - Estado de cada alumno (normal / egresable / baja / ya_inscrito)
     *   - Grupo destino sugerido (siguiente cuatri en el siguiente ciclo)
     *   - Bloqueadores generales (sin grupo destino, sin ciclo siguiente, etc.)
     */
    public function previewPromocion(Request $request)
    {
        $request->validate(['id_grupo' => 'required|exists:grupo,id_grupo']);

        $grupoOrigen = Grupo::with('carrera', 'cicloEscolar')->findOrFail($request->id_grupo);
        $carrera = $grupoOrigen->carrera;
        $cicloOrigen = $grupoOrigen->cicloEscolar;

        // 1) ¿Hay ciclo siguiente? — el ciclo escolar cuya fecha_inicio sea
        //    inmediatamente posterior al ciclo de origen.
        $cicloDestino = CicloEscolar::where('fecha_inicio', '>', $cicloOrigen->fecha_fin)
            ->orderBy('fecha_inicio', 'asc')
            ->first();

        // 2) ¿Hay grupo destino? — misma carrera + (cuatrimestre+1) + ciclo destino
        $grupoDestino = null;
        $cuatriDestino = $grupoOrigen->cuatrimestre + 1;
        if ($cicloDestino) {
            $grupoDestino = Grupo::with('cicloEscolar')
                ->where('id_carrera', $grupoOrigen->id_carrera)
                ->where('cuatrimestre', $cuatriDestino)
                ->where('id_ciclo', $cicloDestino->id_ciclo)
                ->first();
        }

        // 3) Alumnos del grupo origen + análisis de estado individual
        $inscripciones = Inscripcion::with('alumno')
            ->where('id_grupo', $grupoOrigen->id_grupo)
            ->get();

        $maxPeriodos = $carrera->max_periodos ?? 10;
        $alumnos = $inscripciones->map(function ($ins) use ($maxPeriodos, $grupoDestino) {
            $a = $ins->alumno;
            if (!$a) {
                return null;
            }

            $estado = 'normal';
            $mensaje = null;
            $bloqueado = false;

            // Estatus académico
            if (in_array($a->estatus, ['baja_temporal', 'baja_definitiva'])) {
                $estado = 'baja';
                $mensaje = 'Alumno dado de baja';
                $bloqueado = true;
            }
            // ¿Está en el último cuatri/semestre?
            elseif ($a->cuatrimestre_actual >= $maxPeriodos) {
                $estado = 'egresable';
                $mensaje = 'En último periodo (Egresable)';
                $bloqueado = true;
            }
            // ¿Ya tiene inscripción en el ciclo destino?
            elseif ($grupoDestino) {
                $yaInscrito = Inscripcion::where('id_alumno', $a->id_alumno)
                    ->whereHas('grupo', fn($g) => $g->where('id_ciclo', $grupoDestino->id_ciclo))
                    ->exists();
                if ($yaInscrito) {
                    $estado = 'ya_inscrito';
                    $mensaje = 'Ya inscrito en el ciclo destino';
                    $bloqueado = true;
                }
            }

            return [
                'id_alumno' => $a->id_alumno,
                'id_publico' => $a->id_alumno_publico,
                'nombre' => trim(($a->nombre ?? '') . ' ' . ($a->apellidos ?? '')),
                'cuatrimestre_actual' => $a->cuatrimestre_actual,
                'estatus' => $a->estatus,
                'estado' => $estado,
                'mensaje' => $mensaje,
                'bloqueado' => $bloqueado,
            ];
        })->filter()->values();

        return response()->json([
            'origen' => [
                'id_grupo' => $grupoOrigen->id_grupo,
                'clave' => $grupoOrigen->clave_grupo,
                'cuatrimestre' => $grupoOrigen->cuatrimestre,
                'carrera' => $carrera?->nombre_carrera,
                'ciclo' => $cicloOrigen?->nombre,
            ],
            'destino' => $grupoDestino ? [
                'id_grupo' => $grupoDestino->id_grupo,
                'clave' => $grupoDestino->clave_grupo,
                'cuatrimestre' => $grupoDestino->cuatrimestre,
                'ciclo' => $grupoDestino->cicloEscolar?->nombre,
            ] : null,
            'ciclo_destino_existe' => $cicloDestino !== null,
            'ciclo_destino_nombre' => $cicloDestino?->nombre,
            'cuatri_destino' => $cuatriDestino,
            'max_periodos' => $maxPeriodos,
            'label_periodo' => $carrera?->label_periodo ?? 'Cuatrimestre',
            'alumnos' => $alumnos,
            'total' => $alumnos->count(),
            'promovibles' => $alumnos->where('bloqueado', false)->count(),
        ]);
    }

    /**
     * Ejecuta la promoción en batch dentro de una transacción atómica:
     *   - Crea N inscripciones (alumno → grupo destino)
     *   - Actualiza alumno.cuatrimestre_actual += 1
     *   - Si cualquier paso falla → rollback completo
     */
    public function promover(Request $request)
    {
        $request->validate([
            'id_grupo_origen' => 'required|exists:grupo,id_grupo',
            'id_grupo_destino' => 'required|exists:grupo,id_grupo',
            'alumnos' => 'required|array|min:1',
            'alumnos.*' => 'integer|exists:alumno,id_alumno',
        ]);

        $grupoOrigen = Grupo::findOrFail($request->id_grupo_origen);
        $grupoDestino = Grupo::with('cicloEscolar')->findOrFail($request->id_grupo_destino);

        // Validación de coherencia: el grupo destino debe ser de la misma carrera
        // y un cuatrimestre mayor que el origen.
        if ($grupoDestino->id_carrera !== $grupoOrigen->id_carrera) {
            return back()->with('error', 'El grupo destino debe pertenecer a la misma carrera.');
        }
        if ($grupoDestino->cuatrimestre <= $grupoOrigen->cuatrimestre) {
            return back()->with('error', 'El grupo destino debe ser de un periodo posterior.');
        }

        $alumnoIds = $request->input('alumnos');
        $resumen = ['promovidos' => 0, 'omitidos' => 0, 'errores' => []];

        try {
            DB::transaction(function () use ($alumnoIds, $grupoOrigen, $grupoDestino, &$resumen) {
                $alumnos = Alumno::whereIn('id_alumno', $alumnoIds)->get();
                $carrera = $grupoOrigen->carrera;
                $maxPeriodos = $carrera?->max_periodos ?? 10;

                foreach ($alumnos as $alumno) {
                    // Re-validar reglas en backend (defensa contra checkboxes manipulados)
                    if (in_array($alumno->estatus, ['baja_temporal', 'baja_definitiva'])) {
                        $resumen['omitidos']++;
                        $resumen['errores'][] = "{$alumno->nombre_completo}: dado de baja";
                        continue;
                    }
                    if ($alumno->cuatrimestre_actual >= $maxPeriodos) {
                        $resumen['omitidos']++;
                        $resumen['errores'][] = "{$alumno->nombre_completo}: en último periodo";
                        continue;
                    }

                    // Verificar que no exista ya inscripción en este ciclo (race condition)
                    $existe = Inscripcion::where('id_alumno', $alumno->id_alumno)
                        ->whereHas('grupo', fn($g) => $g->where('id_ciclo', $grupoDestino->id_ciclo))
                        ->exists();
                    if ($existe) {
                        $resumen['omitidos']++;
                        $resumen['errores'][] = "{$alumno->nombre_completo}: ya inscrito en el ciclo destino";
                        continue;
                    }

                    Inscripcion::create([
                        'id_alumno' => $alumno->id_alumno,
                        'id_grupo' => $grupoDestino->id_grupo,
                        'fecha_inscripcion' => today(),
                    ]);

                    $alumno->cuatrimestre_actual = $grupoDestino->cuatrimestre;
                    $alumno->save();

                    $resumen['promovidos']++;
                }
            });
        } catch (\Throwable $e) {
            \Log::error('Error en promoción masiva: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error durante la promoción. No se realizó ningún cambio.');
        }

        $msg = "Promoción completada: {$resumen['promovidos']} alumno(s) promovido(s) a {$grupoDestino->clave_grupo}.";
        if ($resumen['omitidos'] > 0) {
            $msg .= " {$resumen['omitidos']} omitido(s).";
        }

        return redirect()->route('gestor.inscripciones')->with('success', $msg);
    }
}
