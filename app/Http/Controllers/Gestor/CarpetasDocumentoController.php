<?php

namespace App\Http\Controllers\Gestor;

use App\Http\Controllers\Controller;
use App\Models\CarpetaDocumento;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CarpetasDocumentoController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:120',
            'parent_id' => 'nullable|integer|exists:carpeta_documento,id_carpeta',
            'visibilidad' => ['nullable', Rule::in(['publica', 'privada'])],
        ]);

        $parent = null;
        if (!empty($data['parent_id'])) {
            $parent = CarpetaDocumento::findOrFail($data['parent_id']);
            // Solo el creador puede deposit dentro de una carpeta privada.
            if ($parent->esPrivada() && $parent->user_id !== auth()->id()) {
                abort(403, 'No tienes acceso a esta carpeta privada.');
            }
            // La subcarpeta hereda la visibilidad del padre.
            $visibilidad = $parent->visibilidad;
        } else {
            $visibilidad = $data['visibilidad'] ?? 'publica';
        }

        CarpetaDocumento::create([
            'nombre' => $data['nombre'],
            'parent_id' => $parent?->id_carpeta,
            'visibilidad' => $visibilidad,
            'user_id' => auth()->id(),
        ]);

        return redirect()
            ->route('gestor.documentacion-reportes', array_filter([
                'tab' => 'documentos',
                'carpeta' => $parent?->id_carpeta,
            ]))
            ->with('success', 'Carpeta creada.');
    }

    public function update(Request $request, CarpetaDocumento $carpeta)
    {
        $this->autorizarPropietario($carpeta);

        $data = $request->validate(['nombre' => 'required|string|max:120']);
        $carpeta->update(['nombre' => $data['nombre']]);

        return redirect()
            ->route('gestor.documentacion-reportes', array_filter([
                'tab' => 'documentos',
                'carpeta' => $carpeta->parent_id,
            ]))
            ->with('success', 'Carpeta renombrada.');
    }

    public function destroy(CarpetaDocumento $carpeta)
    {
        $this->autorizarPropietario($carpeta);

        if (!$carpeta->esVacia()) {
            return back()->with('error', 'La carpeta contiene documentos o subcarpetas. Vacíala antes de eliminar.');
        }

        $parentId = $carpeta->parent_id;
        $carpeta->delete();

        return redirect()
            ->route('gestor.documentacion-reportes', array_filter([
                'tab' => 'documentos',
                'carpeta' => $parentId,
            ]))
            ->with('success', 'Carpeta eliminada.');
    }

    private function autorizarPropietario(CarpetaDocumento $carpeta): void
    {
        if ($carpeta->user_id !== auth()->id()) {
            abort(403, 'Solo el creador puede modificar esta carpeta.');
        }
    }
}
