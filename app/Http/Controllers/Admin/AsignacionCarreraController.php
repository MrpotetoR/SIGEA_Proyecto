<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Carrera;
use App\Models\GestorEscolar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AsignacionCarreraController extends Controller
{
    public function index()
    {
        $personal = GestorEscolar::with('carreras', 'user')
            ->orderBy('apellidos')
            ->get();

        $carrerasSinAsignar = Carrera::doesntHave('personalAsignado')
            ->orderBy('nombre_carrera')
            ->get();

        $carrerasAsignadas = Carrera::has('personalAsignado')
            ->with(['personalAsignado.user'])
            ->orderBy('nombre_carrera')
            ->get();

        return view('admin.asignaciones.index', compact(
            'personal', 'carrerasSinAsignar', 'carrerasAsignadas'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_personal' => 'required|exists:gestores_escolares,id_personal',
            'id_carrera'  => 'required|exists:carrera,id_carrera',
        ]);

        $personal = GestorEscolar::findOrFail($request->id_personal);
        $carrera  = Carrera::findOrFail($request->id_carrera);

        // Regla 1: la carrera no debe estar ya asignada.
        if ($carrera->tieneAsignacion()) {
            return back()->with('error', "La carrera \"{$carrera->nombre_carrera}\" ya está asignada a otro personal.");
        }

        // Regla 2: el personal no debe exceder el máximo.
        if (!$personal->puedeAgregarCarrera()) {
            $max = GestorEscolar::MAX_CARRERAS;
            return back()->with('error', "Este personal ya tiene {$max} carreras (límite máximo).");
        }

        $personal->carreras()->attach($carrera->id_carrera);

        return back()->with('success', "Carrera \"{$carrera->nombre_carrera}\" asignada a {$personal->nombre_completo}.");
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'id_personal' => 'required|exists:gestores_escolares,id_personal',
            'id_carrera'  => 'required|exists:carrera,id_carrera',
        ]);

        $personal = GestorEscolar::findOrFail($request->id_personal);
        $carrera  = Carrera::findOrFail($request->id_carrera);

        $personal->carreras()->detach($carrera->id_carrera);

        return back()->with('success', "Carrera \"{$carrera->nombre_carrera}\" desasignada. Debe asignarse a un nuevo personal.");
    }

    public function transfer(Request $request)
    {
        $request->validate([
            'id_personal_destino' => 'required|exists:gestores_escolares,id_personal',
            'id_carrera'          => 'required|exists:carrera,id_carrera',
        ]);

        $destino = GestorEscolar::findOrFail($request->id_personal_destino);
        $carrera = Carrera::findOrFail($request->id_carrera);

        if (!$destino->puedeAgregarCarrera()) {
            return back()->with('error', 'El personal destino ya alcanzó el límite de 4 carreras.');
        }

        DB::transaction(function () use ($destino, $carrera) {
            // Detach de cualquier personal previo (UNIQUE constraint asegura solo 1).
            DB::table('personal_carrera')->where('id_carrera', $carrera->id_carrera)->delete();
            $destino->carreras()->attach($carrera->id_carrera);
        });

        return back()->with('success', "Carrera transferida a {$destino->nombre_completo}.");
    }
}
