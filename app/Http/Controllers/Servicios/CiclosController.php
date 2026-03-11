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

    public function create() { return view('servicios.ciclos.create'); }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:50|unique:ciclo_escolar,nombre',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after:fecha_inicio',
        ]);
        CicloEscolar::create($request->only('nombre', 'fecha_inicio', 'fecha_fin'));
        return redirect()->route('servicios.ciclos.index')->with('success', 'Ciclo escolar creado.');
    }

    public function show(CicloEscolar $ciclo) { return view('servicios.ciclos.show', compact('ciclo')); }

    public function edit(CicloEscolar $ciclo) { return view('servicios.ciclos.edit', compact('ciclo')); }

    public function update(Request $request, CicloEscolar $ciclo)
    {
        $request->validate([
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after:fecha_inicio',
        ]);
        $ciclo->update($request->only('fecha_inicio', 'fecha_fin'));
        return redirect()->route('servicios.ciclos.index')->with('success', 'Ciclo actualizado.');
    }

    public function destroy(CicloEscolar $ciclo)
    {
        $ciclo->delete();
        return redirect()->route('servicios.ciclos.index')->with('success', 'Ciclo eliminado.');
    }
}
