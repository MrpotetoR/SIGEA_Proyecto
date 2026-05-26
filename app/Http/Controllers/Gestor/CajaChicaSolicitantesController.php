<?php

namespace App\Http\Controllers\Gestor;

use App\Http\Controllers\Controller;
use App\Models\CajaSolicitante;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Endpoint AJAX para autocompletado de solicitantes del módulo Caja Chica.
 *
 * Usado por la vista gestor.caja-chica.create al teclear en el campo de
 * "Solicitante". Devuelve top 5 nombres por relevancia (más usados,
 * más recientes).
 */
class CajaChicaSolicitantesController extends Controller
{
    public function buscar(Request $request): JsonResponse
    {
        $q = trim((string) $request->input('q', ''));

        // No buscar con menos de 2 caracteres (evita devolver todo).
        if (mb_strlen($q) < 2) {
            return response()->json(['sugerencias' => []]);
        }

        // Verificar permiso (mismo criterio que CajaChicaController).
        $user = $request->user();
        if (!$user->hasRole('admin')) {
            $gestor = $user->gestorEscolar;
            if (!$gestor || !$gestor->puede_gestionar_caja_chica) {
                return response()->json(['sugerencias' => []], 403);
            }
        }

        return response()->json([
            'sugerencias' => CajaSolicitante::buscar($q, 5),
        ]);
    }
}
