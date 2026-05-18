<?php

namespace App\Http\Controllers\Gestor;

use App\Http\Controllers\Controller;
use App\Mail\PedidoListoParaRecogerMail;
use App\Models\ConfiguracionInstitucional;
use App\Models\Pedido;
use App\Services\PedidoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use RuntimeException;

/**
 * Bandeja de pedidos para el Gestor Escolar.
 *
 * Acciones disponibles:
 *   - aprobar:        valida vaucher_enviado → aprobado
 *   - rechazar:       valida vaucher_enviado → pendiente_pago (con motivo)
 *   - listoRecoger:   aprobado → listo_recoger (DISPARA correo automatico)
 *   - entregar:       listo_recoger → entregado
 *   - cancelar:       cualquier estado pre-entrega → cancelado
 */
class PedidosController extends Controller
{
    public function __construct(private PedidoService $pedidoService) {}

    public function index(Request $request)
    {
        $estado = $request->input('estado', 'vaucher_enviado');
        $contexto = \App\Support\ContextoEducativo::actual();

        $base = Pedido::with('usuario', 'items')
            ->where('nivel_educativo', $contexto)
            ->when($request->buscar, fn($q) =>
                $q->where(fn($w) =>
                    $w->where('folio', 'like', "%{$request->buscar}%")
                      ->orWhereHas('usuario', fn($u) => $u->where('name', 'like', "%{$request->buscar}%")->orWhere('email', 'like', "%{$request->buscar}%"))
                )
            );

        $pedidos = (clone $base)
            ->when($estado !== 'todos', fn($q) => $q->where('estado', $estado))
            ->orderByDesc('created_at')
            ->paginate(20)->withQueryString();

        // Conteos por tab
        $conteos = [
            'pendiente_pago'  => (clone $base)->where('estado', 'pendiente_pago')->count(),
            'vaucher_enviado' => (clone $base)->where('estado', 'vaucher_enviado')->count(),
            'aprobado'        => (clone $base)->where('estado', 'aprobado')->count(),
            'listo_recoger'   => (clone $base)->where('estado', 'listo_recoger')->count(),
            'entregado'       => (clone $base)->where('estado', 'entregado')->count(),
            'cancelado'       => (clone $base)->where('estado', 'cancelado')->count(),
            'todos'           => (clone $base)->count(),
        ];

        return view('gestor.pedidos.index', compact('pedidos', 'estado', 'conteos'));
    }

    public function show(Pedido $pedido)
    {
        $pedido->load('usuario', 'items.variante.producto', 'historial.usuario', 'revisor', 'entregador');
        return view('gestor.pedidos.show', compact('pedido'));
    }

    public function aprobar(Request $request, Pedido $pedido)
    {
        try {
            $this->pedidoService->transicionar($pedido, 'aprobado', $request->user()->id, 'Pago validado por el Gestor Escolar.');
        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }
        return back()->with('success', "Pedido {$pedido->folio} aprobado.");
    }

    public function rechazar(Request $request, Pedido $pedido)
    {
        $request->validate([
            'motivo' => 'required|string|min:10|max:500',
        ], [
            'motivo.required' => 'Debes indicar el motivo del rechazo.',
            'motivo.min'      => 'El motivo debe tener al menos 10 caracteres.',
        ]);

        try {
            $this->pedidoService->transicionar($pedido, 'pendiente_pago', $request->user()->id, $request->motivo);
        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }
        return back()->with('success', "Pedido {$pedido->folio} regresado al alumno con el motivo de rechazo.");
    }

    public function listoRecoger(Request $request, Pedido $pedido)
    {
        try {
            $this->pedidoService->transicionar($pedido, 'listo_recoger', $request->user()->id, 'Producto preparado y disponible para entrega.');
        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        // Disparar correo automatico al alumno/docente
        try {
            $ubicacion = ConfiguracionInstitucional::get('tienda.ubicacion_entrega', 'Oficinas de Gestor Escolar');
            $horario   = ConfiguracionInstitucional::get('tienda.horario_entrega', 'Lunes a Viernes');

            Mail::to($pedido->usuario->email)->send(
                new PedidoListoParaRecogerMail($pedido->fresh(), $ubicacion, $horario)
            );
        } catch (\Throwable $e) {
            Log::warning("Pedido {$pedido->folio}: marcado como listo_recoger pero fallo el envio de correo. " . $e->getMessage());
            return back()->with('warning', "Pedido marcado como listo, pero el correo no se pudo enviar. Notifica manualmente al alumno.");
        }

        return back()->with('success', "Pedido {$pedido->folio} marcado como listo. Se notificó por correo al alumno.");
    }

    public function entregar(Request $request, Pedido $pedido)
    {
        try {
            $this->pedidoService->transicionar($pedido, 'entregado', $request->user()->id, 'Producto recogido por el alumno.');
        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }
        return back()->with('success', "Pedido {$pedido->folio} marcado como entregado.");
    }

    public function cancelar(Request $request, Pedido $pedido)
    {
        $request->validate(['motivo' => 'nullable|string|max:500']);

        try {
            $this->pedidoService->transicionar($pedido, 'cancelado', $request->user()->id, $request->motivo ?? 'Cancelado por el Gestor Escolar.');
        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }
        return back()->with('success', "Pedido {$pedido->folio} cancelado.");
    }
}
