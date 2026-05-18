<?php

namespace App\Http\Controllers\Gestor;

use App\Http\Controllers\Controller;
use App\Models\DocumentoInstitucional;
use Illuminate\Http\Request;

class DocumentosController extends Controller
{
    public function index()
    {
        $documentos = DocumentoInstitucional::with('autor')->orderByDesc('fecha_publicacion')->paginate(15);
        return view('gestor.documentos.index', compact('documentos'));
    }

    public function create()
    {
        return view('gestor.documentos.create');
    }

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
        ]);

        return redirect()->route('gestor.documentos.index')->with('success', 'Documento publicado.');
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
        return redirect()->route('gestor.documentos.index')->with('success', 'Documento actualizado.');
    }

    public function destroy(DocumentoInstitucional $documento)
    {
        $documento->delete();
        return redirect()->route('gestor.documentos.index')->with('success', 'Documento eliminado.');
    }
}
