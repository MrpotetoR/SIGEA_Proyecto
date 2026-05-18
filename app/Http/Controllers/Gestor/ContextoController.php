<?php

namespace App\Http\Controllers\Gestor;

use App\Http\Controllers\Controller;
use App\Models\Alumno;
use App\Models\Carrera;
use App\Models\Docente;
use App\Models\Grupo;
use App\Support\ContextoEducativo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Gestiona la seleccion y el cambio de "contexto educativo"
 * (Universidad / Bachillerato) para el Gestor Escolar.
 */
class ContextoController extends Controller
{
    /**
     * Pantalla de seleccion inicial (post-login).
     * Solo se muestra cuando el usuario tiene acceso a ambos niveles.
     */
    public function seleccionar(): View|RedirectResponse
    {
        $disponibles = ContextoEducativo::nivelesDisponiblesParaUsuario();

        // Si solo tiene un nivel, lo establece y redirige al dashboard.
        if (count($disponibles) === 1) {
            ContextoEducativo::establecer($disponibles[0]);
            return redirect()->route('gestor.dashboard');
        }

        // Estadisticas para mostrar en las tarjetas de seleccion.
        $stats = [
            ContextoEducativo::UNIVERSIDAD => [
                'alumnos'  => Alumno::sinFiltroNivel()->where('nivel_educativo', 'universidad')->count(),
                'extra'    => Carrera::count(),
                'extraLbl' => 'carreras',
            ],
            ContextoEducativo::BACHILLERATO => [
                'alumnos'  => Alumno::sinFiltroNivel()->where('nivel_educativo', 'bachillerato')->count(),
                'extra'    => Grupo::sinFiltroNivel()->where('nivel_educativo', 'bachillerato')->count(),
                'extraLbl' => 'grupos',
            ],
        ];

        return view('gestor.contexto.seleccionar', compact('disponibles', 'stats'));
    }

    /**
     * Cambio de contexto desde el sidebar o el header.
     */
    public function cambiar(Request $request): RedirectResponse
    {
        $request->validate([
            'nivel' => 'required|in:universidad,bachillerato',
        ]);

        $disponibles = ContextoEducativo::nivelesDisponiblesParaUsuario();
        if (!in_array($request->nivel, $disponibles, true)) {
            abort(403, 'No tienes acceso a esa area operativa.');
        }

        ContextoEducativo::establecer($request->nivel);

        return redirect()->route('gestor.dashboard')
            ->with('success', 'Ahora estas en ' . ContextoEducativo::color($request->nivel)['label']);
    }
}
