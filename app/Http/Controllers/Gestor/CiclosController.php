<?php

namespace App\Http\Controllers\Gestor;

use App\Http\Controllers\Controller;
use App\Models\CicloEscolar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CiclosController extends Controller
{
    public function index()
    {
        $ciclos = CicloEscolar::orderByDesc('fecha_inicio')->get();
        return view('gestor.ciclos.index', compact('ciclos'));
    }

    public function create()
    {
        $aniosUsados = CicloEscolar::pluck('fecha_inicio')
            ->map(fn($f) => \Carbon\Carbon::parse($f)->year)
            ->unique()->values();
        return view('gestor.ciclos.create', compact('aniosUsados'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'fecha_inicio' => 'required|date',
        ]);

        // Regla fija del sistema: ciclo = 3 años 4 meses. Nombre = rango de años "YYYY–YYYY".
        $fechaFin = CicloEscolar::calcularFechaFin($request->fecha_inicio);
        $nombre   = CicloEscolar::generarNombre($request->fecha_inicio);

        if ($error = $this->validarDuplicado($request->fecha_inicio, $fechaFin, $nombre)) {
            return back()->withInput()->withErrors(['fecha_inicio' => $error]);
        }

        CicloEscolar::create([
            'nombre'       => $nombre,
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin'    => $fechaFin,
        ]);
        return redirect()->route('gestor.ciclos.index')->with('success', "Ciclo escolar \"$nombre\" creado (3 años 4 meses).");
    }

    public function show(CicloEscolar $ciclo) { return view('gestor.ciclos.show', compact('ciclo')); }

    public function edit(CicloEscolar $ciclo)
    {
        // Solo se permite editar ciclos FUTUROS (que aún no han iniciado).
        if (!$ciclo->fecha_inicio->isFuture()) {
            $estado = $ciclo->fecha_fin->isPast() ? 'finalizado' : 'en curso';
            return redirect()->route('gestor.ciclos.index')
                ->with('error', "No se puede modificar el ciclo \"{$ciclo->nombre}\" porque está {$estado}.");
        }

        $aniosUsados = CicloEscolar::where('id_ciclo', '!=', $ciclo->id_ciclo)
            ->pluck('fecha_inicio')
            ->map(fn($f) => \Carbon\Carbon::parse($f)->year)
            ->unique()->values();
        return view('gestor.ciclos.edit', compact('ciclo', 'aniosUsados'));
    }

    public function update(Request $request, CicloEscolar $ciclo)
    {
        // Defensa en backend: blindar contra POST directo a un ciclo en curso o finalizado.
        if (!$ciclo->fecha_inicio->isFuture()) {
            $estado = $ciclo->fecha_fin->isPast() ? 'finalizado' : 'en curso';
            return redirect()->route('gestor.ciclos.index')
                ->with('error', "No se puede modificar el ciclo \"{$ciclo->nombre}\" porque está {$estado}.");
        }

        $request->validate([
            'fecha_inicio' => 'required|date',
        ]);

        // La fecha de fin se recalcula siempre a partir del inicio (regla fija: +3 años 4 meses).
        $fechaFin = CicloEscolar::calcularFechaFin($request->fecha_inicio);
        $nombre   = CicloEscolar::generarNombre($request->fecha_inicio);

        if ($error = $this->validarDuplicado($request->fecha_inicio, $fechaFin, $nombre, $ciclo->id_ciclo)) {
            return back()->withInput()->withErrors(['fecha_inicio' => $error]);
        }

        $ciclo->update([
            'nombre'       => $nombre,
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin'    => $fechaFin,
        ]);
        return redirect()->route('gestor.ciclos.index')->with('success', "Ciclo actualizado a \"$nombre\".");
    }

    /**
     * Valida consistencia de un ciclo.
     * UDEA opera con 3 cohortes por año (bloques A/B/C según mes de ingreso),
     * por lo que se permite hasta 3 ciclos por año. Se impide:
     *   - Duplicado por nombre completo (mismo año+bloque, ej. "2026A–2029A").
     *   - Duplicado exacto por fecha de inicio.
     */
    private function validarDuplicado(string $fechaInicio, $fechaFin, string $nombre, ?int $excluirId = null): ?string
    {
        $q = CicloEscolar::query();
        if ($excluirId) $q->where('id_ciclo', '!=', $excluirId);

        // 1) Duplicado por nombre completo (incluye el sufijo de bloque A/B/C).
        if ((clone $q)->where('nombre', $nombre)->exists()) {
            return "Ya existe un ciclo escolar con el nombre \"$nombre\". Verifica el año y bloque cuatrimestral.";
        }

        // 2) Duplicado exacto por fecha de inicio.
        if ((clone $q)->whereDate('fecha_inicio', $fechaInicio)->exists()) {
            return "Ya existe un ciclo escolar que inicia el " . \Carbon\Carbon::parse($fechaInicio)->format('d/m/Y') . ".";
        }

        return null;
    }

    /**
     * Genera de un solo clic los ciclos del año indicado según el tipo:
     *
     * Cuatrimestral (3 cohortes/año):
     *   - Bloque A: 15 enero      → "YYYYA–(YYYY+3)A"
     *   - Bloque B: 15 mayo       → "YYYYB–(YYYY+3)B"
     *   - Bloque C: 15 septiembre → "YYYYC–(YYYY+3)C" (termina al año siguiente)
     *
     * Semestral (2 cohortes/año, para área salud y psicología):
     *   - Bloque 1: 15 enero  → "YYYY-1–(YYYY+3)-1"
     *   - Bloque 2: 15 agosto → "YYYY-2–(YYYY+3)-2"
     *
     * Idempotente: si alguno ya existe (por nombre o fecha), lo salta sin error.
     */
    public function crearAnio(Request $request)
    {
        $request->validate([
            'anio'      => 'required|integer|min:2020|max:2100',
            'tipo'      => 'required|in:cuatrimestre,semestre',
            'fechas'    => 'nullable|array',
            'fechas.*'  => 'nullable|date',
        ]);
        $anio   = (int) $request->anio;
        $tipo   = $request->tipo;
        $fechas = $request->input('fechas', []);

        $bloques = $tipo === 'semestre'
            ? CicloEscolar::FECHAS_DEFAULT_POR_BLOQUE_SEMESTRAL
            : CicloEscolar::FECHAS_DEFAULT_POR_BLOQUE;

        $creados = [];
        $omitidos = [];

        DB::transaction(function () use ($anio, $tipo, $bloques, $fechas, &$creados, &$omitidos) {
            foreach ($bloques as $bloque => $f) {
                // Usar la fecha personalizada del gestor si la mandó, sino el default del bloque.
                $fechaInicio = !empty($fechas[$bloque])
                    ? $fechas[$bloque]
                    : sprintf('%04d-%02d-%02d', $anio, $f['mes'], $f['dia']);
                $fechaFin = CicloEscolar::calcularFechaFin($fechaInicio, $tipo);
                $nombre   = CicloEscolar::generarNombre($fechaInicio, $tipo);

                if ($this->validarDuplicado($fechaInicio, $fechaFin, $nombre) !== null) {
                    $omitidos[] = "{$nombre} (ya existía)";
                    continue;
                }

                CicloEscolar::create([
                    'nombre'       => $nombre,
                    'fecha_inicio' => $fechaInicio,
                    'fecha_fin'    => $fechaFin,
                ]);
                $creados[] = $nombre;
            }
        });

        $tipoLabel = $tipo === 'semestre' ? 'semestral(es)' : 'cuatrimestral(es)';
        $msg = '';
        if (count($creados) > 0) {
            $msg = count($creados) . " ciclo(s) {$tipoLabel} creado(s): " . implode(', ', $creados) . '.';
        }
        if (count($omitidos) > 0) {
            $msg .= ($msg ? ' ' : '') . count($omitidos) . ' omitido(s): ' . implode(', ', $omitidos) . '.';
        }
        if (!$msg) {
            $msg = 'Sin cambios.';
        }

        return redirect()->route('gestor.ciclos.index')->with(
            count($creados) > 0 ? 'success' : 'warning',
            $msg
        );
    }

    public function destroy(CicloEscolar $ciclo)
    {
        $dependientes = $this->contarDependientes($ciclo->id_ciclo);

        if (!empty($dependientes)) {
            $detalle = collect($dependientes)
                ->map(fn($n, $label) => "$n $label")
                ->implode(', ');
            return redirect()->route('gestor.ciclos.index')
                ->with('error', "No se puede eliminar el ciclo \"{$ciclo->nombre}\" porque tiene registros asociados: {$detalle}.");
        }

        $ciclo->delete();
        return redirect()->route('gestor.ciclos.index')->with('success', 'Ciclo eliminado.');
    }

    /**
     * Cuenta registros en tablas que referencian este ciclo vía FK.
     * Devuelve sólo las tablas con count > 0, mapeadas a etiquetas legibles.
     */
    private function contarDependientes(int $idCiclo): array
    {
        $tablas = [
            'calificacion'       => 'calificaciones',
            'grupo'              => 'grupos',
            'semaforo_academico' => 'registros de semáforo académico',
            'evaluacion_docente' => 'evaluaciones docentes',
        ];

        $resultado = [];
        foreach ($tablas as $tabla => $etiqueta) {
            $n = DB::table($tabla)->where('id_ciclo', $idCiclo)->count();
            if ($n > 0) $resultado[$etiqueta] = $n;
        }
        return $resultado;
    }
}
