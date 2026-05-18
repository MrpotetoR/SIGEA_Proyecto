<?php

namespace App\Http\Controllers\Gestor;

use App\Http\Controllers\Controller;
use App\Models\BachilleratoPlan;
use App\Support\ContextoEducativo;
use Illuminate\Http\Request;

/**
 * CRUD de Planes de Bachillerato — solo accesible cuando el contexto
 * activo del Gestor Escolar es "bachillerato".
 */
class BachilleratoPlanesController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (ContextoEducativo::actual() !== ContextoEducativo::BACHILLERATO) {
                abort(403, 'Solo disponible en el area Bachillerato.');
            }
            return $next($request);
        });
    }

    public function index()
    {
        $planes = BachilleratoPlan::withCount(['materias', 'grupos', 'alumnos'])
            ->orderByDesc('vigente')
            ->orderBy('nombre_plan')
            ->paginate(15);

        return view('gestor.planes-bachillerato.index', compact('planes'));
    }

    public function create()
    {
        return view('gestor.planes-bachillerato.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'clave_plan'    => 'required|string|max:20|unique:bachillerato_plan,clave_plan',
            'nombre_plan'   => 'required|string|max:150',
            'num_semestres' => 'required|integer|min:2|max:12',
            'tipo_periodo'  => 'required|in:semestre,cuatrimestre',
            'vigente'       => 'nullable|boolean',
            'descripcion'   => 'nullable|string|max:1000',
        ]);
        $data['vigente'] = $request->boolean('vigente');

        BachilleratoPlan::create($data);

        return redirect()->route('gestor.planes-bachillerato.index')
            ->with('success', 'Plan creado correctamente.');
    }

    public function edit(BachilleratoPlan $plan)
    {
        return view('gestor.planes-bachillerato.edit', compact('plan'));
    }

    public function update(Request $request, BachilleratoPlan $plan)
    {
        $data = $request->validate([
            'clave_plan'    => 'required|string|max:20|unique:bachillerato_plan,clave_plan,' . $plan->id_plan_bachillerato . ',id_plan_bachillerato',
            'nombre_plan'   => 'required|string|max:150',
            'num_semestres' => 'required|integer|min:2|max:12',
            'tipo_periodo'  => 'required|in:semestre,cuatrimestre',
            'vigente'       => 'nullable|boolean',
            'descripcion'   => 'nullable|string|max:1000',
        ]);
        $data['vigente'] = $request->boolean('vigente');

        $plan->update($data);

        return redirect()->route('gestor.planes-bachillerato.index')
            ->with('success', 'Plan actualizado.');
    }

    public function destroy(BachilleratoPlan $plan)
    {
        if ($plan->alumnos()->exists() || $plan->grupos()->exists()) {
            return back()->with('error', 'No se puede eliminar: el plan tiene alumnos o grupos asociados.');
        }
        $plan->materias()->update(['id_plan_bachillerato' => null]);
        $plan->delete();

        return redirect()->route('gestor.planes-bachillerato.index')
            ->with('success', 'Plan eliminado.');
    }
}
