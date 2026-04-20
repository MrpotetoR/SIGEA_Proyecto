<?php

namespace App\Http\Controllers\Servicios;

use App\Http\Controllers\Controller;
use App\Models\CicloEscolar;
use Illuminate\Http\Request;

class CiclosController extends Controller
{
    public function index()
    {
        $ciclos = CicloEscolar::orderByDesc('fecha_inicio')->get();
        return view('servicios.ciclos.index', compact('ciclos'));
    }

    public function create()
    {
        $aniosUsados = CicloEscolar::pluck('fecha_inicio')
            ->map(fn($f) => \Carbon\Carbon::parse($f)->year)
            ->unique()->values();
        return view('servicios.ciclos.create', compact('aniosUsados'));
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
        return redirect()->route('servicios.ciclos.index')->with('success', "Ciclo escolar \"$nombre\" creado (3 años 4 meses).");
    }

    public function show(CicloEscolar $ciclo) { return view('servicios.ciclos.show', compact('ciclo')); }

    public function edit(CicloEscolar $ciclo)
    {
        $aniosUsados = CicloEscolar::where('id_ciclo', '!=', $ciclo->id_ciclo)
            ->pluck('fecha_inicio')
            ->map(fn($f) => \Carbon\Carbon::parse($f)->year)
            ->unique()->values();
        return view('servicios.ciclos.edit', compact('ciclo', 'aniosUsados'));
    }

    public function update(Request $request, CicloEscolar $ciclo)
    {
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
        return redirect()->route('servicios.ciclos.index')->with('success', "Ciclo actualizado a \"$nombre\".");
    }

    /**
     * Valida consistencia de un ciclo.
     * Los ciclos pueden coexistir (cada año se crea uno nuevo mientras los anteriores siguen vigentes),
     * por lo que NO se bloquean rangos solapados. Solo se impide:
     *   - Duplicado por nombre (rango de años YYYY–YYYY → implica un ciclo por año de inicio).
     *   - Duplicado exacto por fecha de inicio.
     * Devuelve un mensaje de error o null si todo está OK.
     */
    private function validarDuplicado(string $fechaInicio, $fechaFin, string $nombre, ?int $excluirId = null): ?string
    {
        $q = CicloEscolar::query();
        if ($excluirId) $q->where('id_ciclo', '!=', $excluirId);

        // 1) Duplicado por nombre (rango de años YYYY–YYYY). Como fin = inicio + 3a 4m,
        //    esto equivale a "sólo un ciclo por año de inicio", que es la regla operativa real.
        if ((clone $q)->where('nombre', $nombre)->exists()) {
            $anio = \Carbon\Carbon::parse($fechaInicio)->year;
            return "Ya existe un ciclo escolar que inicia en {$anio} (\"$nombre\"). Sólo se permite un ciclo por año de inicio.";
        }

        // 2) Duplicado exacto por fecha de inicio.
        if ((clone $q)->whereDate('fecha_inicio', $fechaInicio)->exists()) {
            return "Ya existe un ciclo escolar que inicia el " . \Carbon\Carbon::parse($fechaInicio)->format('d/m/Y') . ".";
        }

        return null;
    }

    public function destroy(CicloEscolar $ciclo)
    {
        $ciclo->delete();
        return redirect()->route('servicios.ciclos.index')->with('success', 'Ciclo eliminado.');
    }
}
