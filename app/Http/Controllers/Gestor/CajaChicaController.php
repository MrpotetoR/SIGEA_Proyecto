<?php

namespace App\Http\Controllers\Gestor;

use App\Http\Controllers\Admin\ReauthController;
use App\Http\Controllers\Controller;
use App\Models\CajaChicaLog;
use App\Models\CajaSolicitante;
use App\Models\FondoCajaChica;
use App\Models\ValeCajaChica;
use App\Services\CajaChicaService;
use App\Support\NumeroALetras;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/**
 * Gestión de vales de Caja Chica desde el panel del Gestor Escolar.
 *
 * Acceso restringido a gestores con flag puede_gestionar_caja_chica = true
 * (validado en el middleware de la ruta via gate o directamente aquí).
 *
 * Acciones que requieren reauth (ver ReauthController):
 *   - autorizar_vale
 *   - rechazar_vale
 *   - cancelar_vale
 *   - subir_factura
 */
class CajaChicaController extends Controller
{
    public function __construct(private CajaChicaService $service) {}

    /**
     * Listado de vales con filtros por estatus / búsqueda.
     */
    public function index(Request $request)
    {
        $this->verificarPermiso($request);

        $vales = ValeCajaChica::with(['solicitante', 'autorizador'])
            ->when($request->estatus, fn($q, $v) => $q->where('estatus', $v))
            ->when($request->buscar, fn($q, $v) =>
                $q->where(fn($qq) => $qq
                    ->where('folio', 'like', "%{$v}%")
                    ->orWhere('solicitante_nombre', 'like', "%{$v}%")
                    ->orWhere('concepto', 'like', "%{$v}%")
                )
            )
            ->orderByDesc('created_at')
            ->paginate(20)->withQueryString();

        $fondo = FondoCajaChica::actual();

        // Conteo de pendientes para el badge del sidebar (futuro).
        $pendientes = ValeCajaChica::where('estatus', 'solicitada')->count();

        return view('gestor.caja-chica.index', compact('vales', 'fondo', 'pendientes'));
    }

    /**
     * Formulario para crear un nuevo vale (solicitud).
     */
    public function create(Request $request)
    {
        $this->verificarPermiso($request);

        $fondo = FondoCajaChica::actual();

        return view('gestor.caja-chica.create', compact('fondo'));
    }

    /**
     * Persiste un vale nuevo (estado solicitada).
     */
    public function store(Request $request)
    {
        $this->verificarPermiso($request);

        $request->validate([
            'solicitante_nombre'   => 'required|string|max:150|min:2',
            'concepto'             => 'required|string|max:255|min:3',
            'monto'                => 'required|numeric|min:0.01|max:9999999.99',
            'motivo'               => 'required|string|in:' . implode(',', array_keys(CajaChicaLog::MOTIVOS)),
            'motivo_personalizado' => 'nullable|string|max:100|required_if:motivo,otro',
        ], [
            'motivo_personalizado.required_if' => 'Especifica el motivo cuando seleccionas "Otro".',
        ]);

        $vale = $this->service->crearVale($request->all(), $request->user(), $request);

        return redirect()->route('gestor.caja-chica.show', $vale)
            ->with('success', "Vale {$vale->folio} creado en estado 'solicitada'.");
    }

    public function show(Request $request, ValeCajaChica $vale)
    {
        $this->verificarPermiso($request);

        $vale->load([
            'fondo', 'solicitante', 'autorizador', 'facturaSubidaPor', 'cierre', 'cancelador',
            'logs.usuario',
        ]);

        $fondo = $vale->fondo;
        return view('gestor.caja-chica.show', compact('vale', 'fondo'));
    }

    /**
     * Actualiza un vale (solo en estado "solicitada").
     */
    public function update(Request $request, ValeCajaChica $vale)
    {
        $this->verificarPermiso($request);

        $request->validate([
            'solicitante_nombre'   => 'required|string|max:150|min:2',
            'concepto'             => 'required|string|max:255|min:3',
            'monto'                => 'required|numeric|min:0.01|max:9999999.99',
            'motivo'               => 'required|string|in:' . implode(',', array_keys(CajaChicaLog::MOTIVOS)),
            'motivo_personalizado' => 'nullable|string|max:100|required_if:motivo,otro',
        ]);

        $this->service->editarVale($vale, $request->all(), $request->user(), $request);

        return redirect()->route('gestor.caja-chica.show', $vale)
            ->with('success', 'Vale actualizado.');
    }

    // ─── Acciones de transición de estado ────────────────────────

    public function autorizar(Request $request, ValeCajaChica $vale)
    {
        $this->verificarPermiso($request);
        $this->exigirReauth('autorizar_vale');

        $request->validate([
            'motivo'               => 'required|string|in:' . implode(',', array_keys(CajaChicaLog::MOTIVOS)),
            'motivo_personalizado' => 'nullable|string|max:100|required_if:motivo,otro',
            'evidencia'            => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $datos = $request->only(['motivo', 'motivo_personalizado']);
        if ($request->hasFile('evidencia')) {
            $datos['evidencia_path'] = $request->file('evidencia')->store(
                'caja-chica/evidencias/' . date('Y/m'),
                'public'
            );
        }

        $this->service->autorizarVale($vale, $request->user(), $datos, $request);

        return redirect()->route('gestor.caja-chica.show', $vale)
            ->with('success', "Vale {$vale->folio} autorizado. Saldo descontado del fondo.");
    }

    public function rechazar(Request $request, ValeCajaChica $vale)
    {
        $this->verificarPermiso($request);
        $this->exigirReauth('rechazar_vale');

        $request->validate([
            'motivo_rechazo'       => 'required|string|max:500|min:5',
            'motivo'               => 'required|string|in:' . implode(',', array_keys(CajaChicaLog::MOTIVOS)),
            'motivo_personalizado' => 'nullable|string|max:100|required_if:motivo,otro',
            'evidencia'            => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $datos = $request->only(['motivo_rechazo', 'motivo', 'motivo_personalizado']);
        if ($request->hasFile('evidencia')) {
            $datos['evidencia_path'] = $request->file('evidencia')->store(
                'caja-chica/evidencias/' . date('Y/m'),
                'public'
            );
        }

        $this->service->rechazarVale($vale, $request->user(), $datos, $request);

        return redirect()->route('gestor.caja-chica.show', $vale)
            ->with('success', "Vale {$vale->folio} rechazado.");
    }

    public function cancelar(Request $request, ValeCajaChica $vale)
    {
        $this->verificarPermiso($request);
        $this->exigirReauth('cancelar_vale');

        $request->validate([
            'motivo'               => 'required|string|in:' . implode(',', array_keys(CajaChicaLog::MOTIVOS)),
            'motivo_personalizado' => 'nullable|string|max:100|required_if:motivo,otro',
            'evidencia'            => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $datos = $request->only(['motivo', 'motivo_personalizado']);
        if ($request->hasFile('evidencia')) {
            $datos['evidencia_path'] = $request->file('evidencia')->store(
                'caja-chica/evidencias/' . date('Y/m'),
                'public'
            );
        }

        $estabaAutorizada = in_array($vale->estatus, ['autorizada', 'comprobada'], true);
        $this->service->cancelarVale($vale, $request->user(), $datos, $request);

        $msg = $estabaAutorizada
            ? "Vale {$vale->folio} cancelado. El monto fue devuelto al fondo."
            : "Vale {$vale->folio} cancelado.";

        return redirect()->route('gestor.caja-chica.show', $vale)->with('success', $msg);
    }

    /**
     * Genera y devuelve el ticket PDF del vale.
     *
     * Disponible para cualquier estatus EXCEPTO `solicitada` (un vale sin
     * autorizar aún no debe imprimirse como comprobante).
     */
    public function imprimir(Request $request, ValeCajaChica $vale)
    {
        $this->verificarPermiso($request);

        if ($vale->estatus === 'solicitada') {
            abort(403, 'No se puede imprimir el ticket de un vale aún no autorizado.');
        }

        $vale->load(['fondo', 'solicitante', 'autorizador', 'facturaSubidaPor', 'cierre', 'cancelador']);
        $montoEnLetras = NumeroALetras::montoMxn((float) $vale->monto);

        $pdf = Pdf::loadView('pdf.vale-caja-chica', compact('vale', 'montoEnLetras'))
            ->setPaper('letter', 'portrait');

        $nombre = "Vale-{$vale->folio}.pdf";

        // 'stream' abre en el navegador. Cambiar a 'download' para forzar descarga.
        return $pdf->stream($nombre);
    }

    public function subirFactura(Request $request, ValeCajaChica $vale)
    {
        $this->verificarPermiso($request);
        $this->exigirReauth('subir_factura');

        $request->validate([
            'factura'              => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'motivo'               => 'required|string|in:' . implode(',', array_keys(CajaChicaLog::MOTIVOS)),
            'motivo_personalizado' => 'nullable|string|max:100|required_if:motivo,otro',
        ]);

        $this->service->subirFactura(
            $vale,
            $request->user(),
            $request->file('factura'),
            $request->only(['motivo', 'motivo_personalizado']),
            $request
        );

        return redirect()->route('gestor.caja-chica.show', $vale)
            ->with('success', "Factura cargada. Vale {$vale->folio} marcado como comprobado.");
    }

    // ─── Helpers privados ────────────────────────────────────────

    /**
     * Lanza 403 si el usuario actual no tiene permiso para gestionar Caja Chica.
     * Admin SIEMPRE tiene acceso. Gestor solo si su flag puede_gestionar_caja_chica = true.
     */
    private function verificarPermiso(Request $request): void
    {
        $user = $request->user();
        if ($user->hasRole('admin')) {
            return;
        }
        $gestor = $user->gestorEscolar;
        if (!$gestor || !$gestor->puede_gestionar_caja_chica) {
            abort(403, 'No tienes permiso para administrar la Caja Chica.');
        }
    }

    /**
     * Lanza ValidationException si no hay grace period activo para la acción.
     */
    private function exigirReauth(string $accion): void
    {
        if (!ReauthController::tieneGracePeriod($accion)) {
            throw ValidationException::withMessages([
                'reauth' => 'Necesitas confirmar tu contraseña antes de esta acción.',
            ]);
        }
    }
}
