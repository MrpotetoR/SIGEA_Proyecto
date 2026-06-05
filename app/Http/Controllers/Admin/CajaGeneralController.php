<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FondoCajaChica;
use App\Models\IngresoCajaGeneral;
use App\Models\User;
use App\Models\ValeCajaChica;
use App\Services\IngresoCajaService;
use App\Support\NumeroALetras;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Dashboard y reportes de la Caja General.
 *
 * Lee desde `ingreso_caja_general` que recibe registros automáticos al
 * aprobar baucher de colegiatura, aprobar pedido de tienda, y al cobrar
 * un trámite manual. Excluye cancelados (scope `vigentes`).
 *
 * Funcionalidades:
 *  - Dashboard con tarjetas de totales (día/semana/mes/personalizado)
 *  - Desglose por tipo (colegiatura/producto/trámite/otro)
 *  - Tabla paginada de ingresos con filtros
 *  - Export PDF y CSV
 *  - Reporte consolidado (Caja General + Caja Chica = Saldo Neto)
 */
class CajaGeneralController extends Controller
{
    public function index(Request $request)
    {
        $filtros = $this->normalizarFiltros($request);
        $query   = $this->aplicarFiltros(IngresoCajaGeneral::vigentes(), $filtros);

        // Totales y desglose
        $resumen = $this->calcularResumen($filtros);

        // Tabla paginada
        $ingresos = $query->with(['alumno', 'usuario'])
            ->orderByDesc('fecha_cobro')
            ->paginate(20)
            ->withQueryString();

        // Lista de usuarios que han registrado ingresos (para filtro)
        $usuariosRegistradores = User::whereHas('roles',
            fn($q) => $q->whereIn('name', ['admin', 'gestor_escolar'])
        )->orderBy('name')->get(['id', 'name']);

        return view('admin.caja-general.index', compact(
            'ingresos', 'resumen', 'filtros', 'usuariosRegistradores'
        ));
    }

    public function exportPdf(Request $request)
    {
        $filtros = $this->normalizarFiltros($request);
        $ingresos = $this->aplicarFiltros(IngresoCajaGeneral::vigentes(), $filtros)
            ->with(['alumno', 'usuario'])
            ->orderByDesc('fecha_cobro')
            ->limit(500)
            ->get();

        $resumen = $this->calcularResumen($filtros);

        $pdf = Pdf::loadView('pdf.caja-general-reporte', compact('ingresos', 'resumen', 'filtros'))
            ->setPaper('letter', 'landscape');

        $nombre = 'Reporte-CajaGeneral-' . now()->format('Ymd-His') . '.pdf';
        return $pdf->stream($nombre);
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $filtros = $this->normalizarFiltros($request);
        $ingresos = $this->aplicarFiltros(IngresoCajaGeneral::vigentes(), $filtros)
            ->with(['alumno', 'usuario'])
            ->orderByDesc('fecha_cobro')
            ->get();

        $nombre = 'reporte-caja-general-' . now()->format('Ymd-His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$nombre}\"",
        ];

        return response()->stream(function () use ($ingresos) {
            $out = fopen('php://output', 'w');
            // BOM para que Excel detecte UTF-8 correctamente
            fwrite($out, "\xEF\xBB\xBF");

            fputcsv($out, [
                'Folio', 'Fecha', 'Tipo', 'Concepto', 'Alumno',
                'Monto', 'Método de pago', 'Registrado por', 'Referencia',
            ]);

            foreach ($ingresos as $i) {
                fputcsv($out, [
                    $i->folio,
                    $i->fecha_cobro?->format('Y-m-d H:i'),
                    $i->tipo_label,
                    $i->concepto,
                    $i->alumno?->nombre_completo ?? '—',
                    number_format((float) $i->monto, 2, '.', ''),
                    $i->metodo_pago_label,
                    $i->usuario?->name ?? '—',
                    $i->referencia_tipo
                        ? "{$i->referencia_tipo}#{$i->referencia_id}"
                        : 'manual',
                ]);
            }
            fclose($out);
        }, 200, $headers);
    }

    /**
     * Reporte consolidado: Caja General (ingresos) vs Caja Chica (egresos).
     * Sirve para ver el saldo neto del periodo.
     */
    public function consolidado(Request $request)
    {
        $filtros = $this->normalizarFiltros($request);

        // Ingresos (Caja General)
        $ingresosQuery = $this->aplicarFiltros(IngresoCajaGeneral::vigentes(), $filtros);
        $ingresosResumen = [
            'total'        => (float) $ingresosQuery->clone()->sum('monto'),
            'colegiaturas' => (float) $ingresosQuery->clone()->where('tipo', 'colegiatura')->sum('monto'),
            'productos'    => (float) $ingresosQuery->clone()->where('tipo', 'producto')->sum('monto'),
            'tramites'     => (float) $ingresosQuery->clone()->where('tipo', 'tramite')->sum('monto'),
            'otros'        => (float) $ingresosQuery->clone()->where('tipo', 'otro')->sum('monto'),
            'conteo'       => $ingresosQuery->clone()->count(),
        ];

        // Egresos (Caja Chica - vales autorizados/comprobados, no cancelados)
        $egresosQuery = ValeCajaChica::whereIn('estatus', ['autorizada', 'comprobada']);
        if ($filtros['desde']) $egresosQuery->whereDate('autorizado_en', '>=', $filtros['desde']);
        if ($filtros['hasta']) $egresosQuery->whereDate('autorizado_en', '<=', $filtros['hasta']);

        $egresosResumen = [
            'total'   => (float) $egresosQuery->clone()->sum('monto'),
            'conteo'  => $egresosQuery->clone()->count(),
        ];

        // Saldo del fondo (info actual, no del periodo)
        $fondo = FondoCajaChica::actual();

        // Diferencia real de la caja chica:
        //   saldo_actual - monto_base
        //     < 0  → faltante  (la caja debe MENOS de lo objetivo, hay un hueco por reponer)
        //     > 0  → sobrante  (la caja tiene MÁS de lo objetivo, caso poco común)
        //     = 0  → caja cuadrada
        // La caja chica es un fondo objetivo (no un flujo de egresos del periodo),
        // por eso lo correcto es mostrar la diferencia respecto al monto base.
        $diferenciaCaja = (float) $fondo->saldo_actual - (float) $fondo->monto_base;

        // Saldo neto del periodo: ingresos del periodo + diferencia de caja chica.
        // Un faltante resta al neto; un sobrante suma.
        $saldoNeto = $ingresosResumen['total'] + $diferenciaCaja;

        return view('admin.caja-general.consolidado', compact(
            'ingresosResumen', 'egresosResumen', 'fondo', 'saldoNeto', 'diferenciaCaja', 'filtros'
        ));
    }

    // ─── Helpers privados ────────────────────────────────

    /**
     * Normaliza los filtros del request en un array con valores por defecto.
     * Si no se especifican desde/hasta, usa "hoy".
     */
    private function normalizarFiltros(Request $request): array
    {
        $rango = $request->input('rango', 'mes'); // hoy | semana | mes | personalizado

        $desde = null;
        $hasta = null;

        switch ($rango) {
            case 'hoy':
                $desde = $hasta = Carbon::today()->format('Y-m-d');
                break;
            case 'semana':
                $desde = Carbon::now()->startOfWeek()->format('Y-m-d');
                $hasta = Carbon::now()->endOfWeek()->format('Y-m-d');
                break;
            case 'mes':
                $desde = Carbon::now()->startOfMonth()->format('Y-m-d');
                $hasta = Carbon::now()->endOfMonth()->format('Y-m-d');
                break;
            case 'personalizado':
                $desde = $request->input('desde') ?: Carbon::today()->format('Y-m-d');
                $hasta = $request->input('hasta') ?: Carbon::today()->format('Y-m-d');
                break;
        }

        return [
            'rango'       => $rango,
            'desde'       => $desde,
            'hasta'       => $hasta,
            'tipo'        => $request->input('tipo'),
            'user_id'     => $request->input('user_id'),
            'buscar'      => $request->input('buscar'),
        ];
    }

    private function aplicarFiltros($query, array $f)
    {
        if ($f['desde']) $query->whereDate('fecha_cobro', '>=', $f['desde']);
        if ($f['hasta']) $query->whereDate('fecha_cobro', '<=', $f['hasta']);
        if ($f['tipo'])    $query->where('tipo', $f['tipo']);
        if ($f['user_id']) $query->where('user_id', $f['user_id']);
        if ($f['buscar']) {
            $b = $f['buscar'];
            $query->where(fn($q) => $q
                ->where('folio', 'like', "%{$b}%")
                ->orWhere('concepto', 'like', "%{$b}%")
            );
        }
        return $query;
    }

    /**
     * Calcula totales del rango filtrado + tarjetas rápidas (hoy/semana/mes).
     */
    private function calcularResumen(array $filtros): array
    {
        $query = $this->aplicarFiltros(IngresoCajaGeneral::vigentes(), $filtros);

        $total = (float) $query->clone()->sum('monto');
        $conteo = $query->clone()->count();

        // Desglose por tipo dentro del rango filtrado
        $porTipo = [
            'colegiatura' => (float) $query->clone()->where('tipo', 'colegiatura')->sum('monto'),
            'producto'    => (float) $query->clone()->where('tipo', 'producto')->sum('monto'),
            'tramite'     => (float) $query->clone()->where('tipo', 'tramite')->sum('monto'),
            'otro'        => (float) $query->clone()->where('tipo', 'otro')->sum('monto'),
        ];

        // Tarjetas rápidas (ignoran filtros, son siempre hoy/semana/mes actuales)
        $rapidas = [
            'hoy'    => (float) IngresoCajaGeneral::vigentes()->deHoy()->sum('monto'),
            'semana' => (float) IngresoCajaGeneral::vigentes()->deLaSemana()->sum('monto'),
            'mes'    => (float) IngresoCajaGeneral::vigentes()->delMes()->sum('monto'),
        ];

        return [
            'total'      => $total,
            'conteo'     => $conteo,
            'por_tipo'   => $porTipo,
            'rapidas'    => $rapidas,
        ];
    }
}
