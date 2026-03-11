<?php

namespace App\Http\Controllers\Servicios;

use App\Http\Controllers\Controller;
use App\Models\DocumentoInstitucional;
use Illuminate\Http\Request;

class DocumentosController extends Controller
{
    public function index()
    {
        $documentos = DocumentoInstitucional::with('autor')->orderByDesc('fecha_publicacion')->paginate(15);
        return view('servicios.documentos.index', compact('documentos'));
    }

    public function create() { return view('servicios.documentos.create'); }

    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:200',
            'tipo' => 'required|string|max:80',
            'fecha_publicacion' => 'required|date',
            'archivo' => 'required|file|mimes:pdf,doc,docx|max:10240',
        ]);

        $path = $request->file('archivo')->store('documentos', 'public');

        DocumentoInstitucional::create([
            'user_id' => auth()->id(),
            'titulo' => $request->titulo,
            'tipo' => $request->tipo,
            'archivo_url' => $path,
            'fecha_publicacion' => $request->fecha_publicacion,
            'activo' => true,
        ]);

        return redirect()->route('servicios.documentos.index')->with('success', 'Documento publicado.');
    }

    public function show(DocumentoInstitucional $documento) { return view('servicios.documentos.show', compact('documento')); }
    public function edit(DocumentoInstitucional $documento) { return view('servicios.documentos.edit', compact('documento')); }

    public function update(Request $request, DocumentoInstitucional $documento)
    {
        $request->validate(['titulo' => 'required|string|max:200', 'tipo' => 'required|string|max:80']);
        $documento->update($request->only('titulo', 'tipo', 'activo'));
        return redirect()->route('servicios.documentos.index')->with('success', 'Documento actualizado.');
    }

    public function destroy(DocumentoInstitucional $documento)
    {
        $documento->delete();
        return redirect()->route('servicios.documentos.index')->with('success', 'Documento eliminado.');
    }
}
