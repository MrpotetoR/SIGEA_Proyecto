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

    public function index()
    {
        return view('servicios.constancias.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_alumno' => 'required|exists:alumno,id_alumno',
            'tipo' => 'required|in:estudio,calificaciones,comportamiento,servicio_social,cultural',
        ]);

        $alumno = Alumno::findOrFail($request->id_alumno);

        Constancia::create([
            'id_alumno' => $alumno->id_alumno,
            'generada_por' => auth()->id(),
            'tipo' => $request->tipo,
            'archivo_url' => null,
            'fecha_emision' => today(),
        ]);

        $pdf = $this->pdfService->crearConstanciaPdf($alumno, $request->tipo);
        $nombre = "constancia_{$alumno->matricula}_{$request->tipo}.pdf";

        return $pdf->download($nombre);
    }

    public function pdf(Constancia $constancia)
    {
        $constancia->load('alumno');

        if (!$constancia->alumno) {
            return back()->with('error', 'Alumno no encontrado.');
        }

        $pdf = $this->pdfService->crearConstanciaPdf($constancia->alumno, $constancia->tipo);
        $nombre = "constancia_{$constancia->alumno->matricula}_{$constancia->tipo}.pdf";

        return $pdf->download($nombre);
    }
}
