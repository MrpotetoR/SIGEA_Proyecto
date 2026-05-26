<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Alumno;
use App\Models\CobroTramite;
use App\Models\ConfiguracionInstitucional;
use App\Services\IngresoCajaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * Cobros manuales de trámites administrativos (kárdex, constancias, etc.).
 *
 * Al guardar un cobro, automáticamente crea su registro en
 * ingreso_caja_general (vía IngresoCajaService) para que aparezca en los
 * reportes de Caja General.
 *
 * Las tarifas default vienen de ConfiguracionInstitucional. Si no están
 * configuradas, el form pide capturar el monto manualmente.
 */
class CobroTramiteController extends Controller
{
    public function __construct(private IngresoCajaService $ingresosCaja) {}

    public function index(Request $request)
    {
        $cobros = CobroTramite::with(['alumno', 'cobrador'])
            ->when($request->buscar, fn($q, $v) =>
                $q->where(fn($qq) => $qq
                    ->where('folio', 'like', "%{$v}%")
                    ->orWhereHas('alumno', fn($qa) =>
                        $qa->where('nombre', 'like', "%{$v}%")
                           ->orWhere('apellidos', 'like', "%{$v}%")
                           ->orWhere('id_alumno_publico', 'like', "%{$v}%")
                    )
                )
            )
            ->when($request->tipo,    fn($q, $v) => $q->where('tipo_tramite', $v))
            ->when($request->estatus, fn($q, $v) => $q->where('estatus', $v))
            ->orderByDesc('cobrado_en')
            ->paginate(20)
            ->withQueryString();

        return view('admin.caja-general.tramites.index', compact('cobros'));
    }

    public function create()
    {
        // Tarifas default desde configuracion_institucional
        $tarifas = [];
        foreach (CobroTramite::CLAVES_TARIFA as $tipo => $clave) {
            $tarifas[$tipo] = CobroTramite::tarifaDefault($tipo);
        }

        return view('admin.caja-general.tramites.create', compact('tarifas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'alumno_id'              => 'required|exists:alumno,id_alumno',
            'tipo_tramite'           => 'required|in:' . implode(',', array_keys(CobroTramite::TIPOS_TRAMITE)),
            'concepto_personalizado' => 'nullable|string|max:255|required_if:tipo_tramite,otro',
            'monto'                  => 'required|numeric|min:0.01|max:9999999.99',
            'metodo_pago'            => 'required|in:transferencia,efectivo,tarjeta,otro',
            'referencia_externa'     => 'nullable|string|max:100',
            'evidencia'              => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ], [
            'concepto_personalizado.required_if' => 'Describe el trámite cuando seleccionas "Otro".',
        ]);

        $evidenciaPath = null;
        if ($request->hasFile('evidencia')) {
            $evidenciaPath = $request->file('evidencia')->store(
                'caja-general/tramites/' . date('Y/m'),
                'public'
            );
        }

        $cobro = DB::transaction(function () use ($request, $evidenciaPath) {
            $cobro = CobroTramite::create([
                'tipo_tramite'           => $request->tipo_tramite,
                'concepto_personalizado' => $request->concepto_personalizado,
                'monto'                  => $request->monto,
                'alumno_id'              => $request->alumno_id,
                'cobrado_por'            => $request->user()->id,
                'metodo_pago'            => $request->metodo_pago,
                'referencia_externa'     => $request->referencia_externa,
                'evidencia_path'         => $evidenciaPath,
                'estatus'                => 'cobrado',
                'cobrado_en'             => now(),
            ]);

            // Crear el ingreso automáticamente en Caja General
            $this->ingresosCaja->registrarTramite($cobro, $request->user());

            return $cobro;
        });

        return redirect()->route('admin.caja-general.cobro-tramite.index')
            ->with('success', "Cobro {$cobro->folio} registrado por \${$cobro->monto}. Ingreso reflejado en Caja General.");
    }

    public function cancelar(Request $request, CobroTramite $cobro)
    {
        $request->validate([
            'motivo_cancelacion' => 'required|string|max:255|min:5',
        ]);

        if ($cobro->esta_cancelado) {
            return back()->with('error', 'Este cobro ya fue cancelado.');
        }

        DB::transaction(function () use ($request, $cobro) {
            $cobro->update([
                'estatus'            => 'cancelado',
                'cancelado_en'       => now(),
                'cancelado_por'      => $request->user()->id,
                'motivo_cancelacion' => $request->motivo_cancelacion,
            ]);

            // Reversa del ingreso en Caja General
            $this->ingresosCaja->cancelarPorReferencia(
                'cobro_tramite',
                $cobro->id_cobro,
                $request->user(),
                $request->motivo_cancelacion
            );
        });

        return back()->with('success', "Cobro {$cobro->folio} cancelado. El ingreso fue reversado.");
    }

    /**
     * Endpoint AJAX para buscar alumnos en el form de cobro (autocomplete).
     */
    public function buscarAlumnos(Request $request)
    {
        $q = trim((string) $request->input('q', ''));
        if (mb_strlen($q) < 2) {
            return response()->json(['sugerencias' => []]);
        }

        $alumnos = Alumno::where(function ($qq) use ($q) {
                $qq->where('nombre', 'like', "%{$q}%")
                   ->orWhere('apellidos', 'like', "%{$q}%")
                   ->orWhere('id_alumno_publico', 'like', "%{$q}%");
            })
            ->limit(8)
            ->get(['id_alumno', 'id_alumno_publico', 'nombre', 'apellidos']);

        return response()->json([
            'sugerencias' => $alumnos->map(fn($a) => [
                'id'      => $a->id_alumno,
                'codigo'  => $a->id_alumno_publico,
                'nombre'  => "{$a->nombre} {$a->apellidos}",
            ])->values(),
        ]);
    }
}
