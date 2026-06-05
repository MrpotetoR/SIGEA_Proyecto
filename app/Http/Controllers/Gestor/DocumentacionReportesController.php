<?php

namespace App\Http\Controllers\Gestor;

use App\Http\Controllers\Controller;
use App\Models\Carrera;
use App\Models\CarpetaDocumento;
use App\Models\CicloEscolar;
use App\Models\DocumentoInstitucional;
use App\Services\EstadisticasCarreraService;
use Illuminate\Http\Request;

class DocumentacionReportesController extends Controller
{
    public function __construct(private EstadisticasCarreraService $estadisticas) {}

    public function index(Request $request)
    {
        $tab = in_array($request->get('tab'), ['reportes', 'documentos'], true)
            ? $request->get('tab')
            : 'reportes';

        // ============================================================
        // Datos del tab REPORTES
        // ============================================================
        $carreras = Carrera::misCarreras()->orderBy('nombre_carrera')->get();
        $ciclos = CicloEscolar::orderByDesc('fecha_inicio')->get();

        $reporte = null;
        if ($request->filled('carrera_id') && $request->filled('ciclo_id')) {
            $carrera = Carrera::find($request->carrera_id);
            $ciclo = CicloEscolar::find($request->ciclo_id);

            if ($carrera && $ciclo) {
                $reporte = [
                    'carrera' => $carrera,
                    'ciclo' => $ciclo,
                    'aprobacion' => $this->estadisticas->indiceAprobacion($carrera, $ciclo),
                    'semaforo' => $this->estadisticas->distribucionSemaforo($carrera, $ciclo),
                    'evaluacion_docentes' => $this->estadisticas->promedioEvaluacionDocente($carrera, $ciclo),
                ];
            }
        }

        // ============================================================
        // Datos del tab DOCUMENTOS — navegación por carpetas.
        // ============================================================
        $userId = auth()->id();
        $carpetaActual = null;
        $breadcrumb = [];

        if ($request->filled('carpeta')) {
            $carpetaActual = CarpetaDocumento::find($request->carpeta);
            if (!$carpetaActual) {
                return redirect()->route('gestor.documentacion-reportes', ['tab' => 'documentos'])
                    ->with('error', 'La carpeta no existe.');
            }
            if ($carpetaActual->esPrivada() && $carpetaActual->user_id !== $userId) {
                abort(403, 'No tienes acceso a esta carpeta privada.');
            }
            $breadcrumb = $carpetaActual->breadcrumb();
        }

        $carpetas = CarpetaDocumento::query()
            ->where('parent_id', $carpetaActual?->id_carpeta)
            ->visiblesPara($userId)
            ->orderBy('nombre')
            ->get();

        $documentosQ = DocumentoInstitucional::with('autor')
            ->where('carpeta_id', $carpetaActual?->id_carpeta)
            ->orderByDesc('fecha_publicacion');

        $documentos = $documentosQ->paginate(15)->withQueryString();

        return view('gestor.documentacion-reportes', compact(
            'tab', 'carreras', 'ciclos', 'reporte',
            'carpetas', 'documentos', 'carpetaActual', 'breadcrumb'
        ));
    }
}
