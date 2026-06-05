<?php

namespace App\Http\Controllers\Gestor;

use App\Http\Controllers\Controller;
use App\Models\CarpetaDocumento;
use App\Models\DocumentoInstitucional;
use Illuminate\Http\Request;

class DocumentosController extends Controller
{
    public function create(Request $request)
    {
        $carpetaActual = null;
        if ($request->filled('carpeta')) {
            $carpetaActual = CarpetaDocumento::findOrFail($request->carpeta);
            if ($carpetaActual->esPrivada() && $carpetaActual->user_id !== auth()->id()) {
                abort(403, 'No tienes acceso a esta carpeta privada.');
            }
        }

        return view('gestor.documentos.create', compact('carpetaActual'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:200',
            'tipo' => 'required|string|max:80',
            'fecha_publicacion' => 'required|date',
            'archivo' => 'required|file|mimes:pdf,doc,docx|max:10240',
            'carpeta_id' => 'nullable|integer|exists:carpeta_documento,id_carpeta',
        ]);

        if ($request->filled('carpeta_id')) {
            $carpeta = CarpetaDocumento::findOrFail($request->carpeta_id);
            if ($carpeta->esPrivada() && $carpeta->user_id !== auth()->id()) {
                abort(403, 'No tienes acceso a esta carpeta privada.');
            }
        }

        $path = $request->file('archivo')->store('documentos', 'public');

        DocumentoInstitucional::create([
            'user_id' => auth()->id(),
            'carpeta_id' => $request->carpeta_id ?: null,
            'titulo' => $request->titulo,
            'tipo' => $request->tipo,
            'archivo_url' => $path,
            'fecha_publicacion' => $request->fecha_publicacion,
        ]);

        return redirect()->route('gestor.documentacion-reportes', array_filter([
            'tab' => 'documentos',
            'carpeta' => $request->carpeta_id ?: null,
        ]))->with('success', 'Documento publicado.');
    }

    public function show(DocumentoInstitucional $documento)
    {
        return view('gestor.documentos.show', compact('documento'));
    }

    public function edit(DocumentoInstitucional $documento)
    {
        return view('gestor.documentos.edit', compact('documento'));
    }

    public function update(Request $request, DocumentoInstitucional $documento)
    {
        $request->validate(['titulo' => 'required|string|max:200', 'tipo' => 'required|string|max:80']);
        $documento->update($request->only('titulo', 'tipo'));

        return redirect()->route('gestor.documentacion-reportes', array_filter([
            'tab' => 'documentos',
            'carpeta' => $documento->carpeta_id,
        ]))->with('success', 'Documento actualizado.');
    }

    public function destroy(DocumentoInstitucional $documento)
    {
        $carpetaId = $documento->carpeta_id;
        $documento->delete();

        return redirect()->route('gestor.documentacion-reportes', array_filter([
            'tab' => 'documentos',
            'carpeta' => $carpetaId,
        ]))->with('success', 'Documento eliminado.');
    }
}
