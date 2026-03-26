<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use App\Models\Docente;
use App\Models\Grupo;
use Illuminate\Http\Request;

class AjaxSearchController extends Controller
{
    public function alumnos(Request $request)
    {
        $query = Alumno::activos()->with('carrera');

        if ($request->filled('carrera')) {
            $query->where('id_carrera', $request->carrera);
        }

        if ($request->filled('q')) {
            $termino = $request->q;
            $query->where(fn($q) =>
                $q->where('matricula', 'like', "%{$termino}%")
                  ->orWhere('nombre', 'like', "%{$termino}%")
                  ->orWhere('apellidos', 'like', "%{$termino}%")
            );
        }

        return response()->json(
            $query->orderBy('apellidos')->limit(20)->get()->map(fn($a) => [
                'id' => $a->id_alumno,
                'texto' => "{$a->apellidos}, {$a->nombre} — {$a->matricula}",
                'extra' => $a->carrera?->clave_carrera,
            ])
        );
    }

    public function docentes(Request $request)
    {
        $query = Docente::query();

        if ($request->filled('q')) {
            $termino = $request->q;
            $query->where(fn($q) =>
                $q->where('nombre', 'like', "%{$termino}%")
                  ->orWhere('apellidos', 'like', "%{$termino}%")
                  ->orWhere('especialidad', 'like', "%{$termino}%")
            );
        }

        if ($request->filled('tutor')) {
            $query->where('es_tutor', true);
        }

        return response()->json(
            $query->orderBy('apellidos')->limit(20)->get()->map(fn($d) => [
                'id' => $d->id_docente,
                'texto' => "{$d->nombre_completo}",
                'extra' => $d->especialidad,
            ])
        );
    }

    public function grupos(Request $request)
    {
        $query = Grupo::with('cicloEscolar', 'carrera');

        if ($request->filled('carrera')) {
            $query->where('id_carrera', $request->carrera);
        }
        if ($request->filled('ciclo')) {
            $query->where('id_ciclo', $request->ciclo);
        }

        return response()->json(
            $query->orderBy('clave_grupo')->get()->map(fn($g) => [
                'id' => $g->id_grupo,
                'texto' => "{$g->clave_grupo} — {$g->cicloEscolar?->nombre}",
            ])
        );
    }
}
