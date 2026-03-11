<?php

namespace App\Http\Controllers\Servicios;

use App\Http\Controllers\Controller;
use App\Models\Alumno;
use App\Models\Constancia;
use App\Services\PDFService;
use Illuminate\Http\Request;

class ConstanciasController extends Controller
{
    public function __construct(private PDFService $pdfService) {}

    public function index(Request $request)
    {
        $constancias = Constancia::with('alumno', 'generadaPor')
            ->when($request->buscar, fn($q) =>
                $q->whereHas('alumno', fn($a) => $a->where('matricula', 'like', "%{$request->buscar}%"))
            )
            ->orderByDesc('created_at')
            ->paginate(20)->withQueryString();

        $alumnos = Alumno::activos()->orderBy('apellidos')->get();

        return view('servicios.constancias.index', compact('constancias', 'alumnos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_alumno' => 'required|exists:alumno,id_alumno',
            'tipo' => 'required|in:estudio,calificaciones,comportamiento,servicio_social,cultural',
        ]);

        $alumno = Alumno::findOrFail($request->id_alumno);
        $path = $this->pdfService->generarConstancia($alumno, $request->tipo);

        Constancia::create([
            'id_alumno' => $alumno->id_alumno,
            'generada_por' => auth()->id(),
            'tipo' => $request->tipo,
            'archivo_url' => $path,
            'fecha_emision' => today(),
        ]);

        return back()->with('success', 'Constancia generada.');
    }

    public function pdf(Constancia $constancia)
    {
        if (!$constancia->archivo_url || !file_exists($constancia->archivo_url)) {
            return back()->with('error', 'Archivo no encontrado.');
        }
        return response()->download($constancia->archivo_url);
    }
}
