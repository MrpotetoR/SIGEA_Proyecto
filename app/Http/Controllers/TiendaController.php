<?php

namespace App\Http\Controllers;

use App\Models\ConfiguracionInstitucional;
use App\Models\Pedido;
use App\Models\Producto;
use App\Models\ProductoVariante;
use App\Models\User;
use App\Services\PedidoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

/**
 * Tienda pública — alumnos y docentes pueden navegar el catálogo,
 * armar carrito y generar pedidos. El gestor escolar valida y entrega.
 *
 * Productos se filtran por nivel educativo del usuario (universidad o
 * bachillerato) — un alumno de bachi solo ve productos bachi.
 */
class TiendaController extends Controller
{
    public function __construct(private PedidoService $pedidoService) {}

    // ─────────────────────────────────────────────
    //  CATÁLOGO
    // ─────────────────────────────────────────────

    public function catalogo(Request $request)
    {
        $nivel = $this->nivelDelUsuario($request->user());

        $productos = Producto::sinFiltroNivel()
            ->where('nivel_educativo', $nivel)
            ->where('activo', true)
            ->withSum('variantes as stock_total', 'stock')
            ->with('variantes')
            ->when($request->buscar, fn($q) => $q->where('nombre', 'like', "%{$request->buscar}%"))
            ->when($request->categoria, fn($q) => $q->where('categoria', $request->categoria))
            ->orderBy('nombre')
            ->paginate(12)->withQueryString();

        $carrito = $this->resumenCarrito();

        return view('tienda.catalogo', compact('productos', 'carrito', 'nivel'));
    }

    public function detalle(Request $request, int $producto)
    {
        $nivel = $this->nivelDelUsuario($request->user());

        $producto = Producto::sinFiltroNivel()
            ->where('id_producto', $producto)
            ->where('nivel_educativo', $nivel)
            ->where('activo', true)
            ->with(['variantes', 'imagenes'])
            ->firstOrFail();

        $carrito = $this->resumenCarrito();

        return view('tienda.detalle', compact('producto', 'carrito'));
    }

    // ─────────────────────────────────────────────
    //  CARRITO (en sesión)
    // ─────────────────────────────────────────────

    public function carrito()
    {
        $items = $this->itemsCarrito();
        $total = $items->sum('subtotal');

        return view('tienda.carrito', compact('items', 'total'));
    }

    public function agregarAlCarrito(Request $request)
    {
        $data = $request->validate([
            'id_variante' => 'required|exists:producto_variante,id_variante',
            'cantidad'    => 'required|integer|min:1|max:10',
        ]);

        $variante = ProductoVariante::with('producto')->findOrFail($data['id_variante']);
        $nivel = $this->nivelDelUsuario($request->user());

        if ($variante->producto->nivel_educativo !== $nivel || !$variante->producto->activo) {
            return back()->with('error', 'Este producto no está disponible.');
        }

        if ($variante->stock < $data['cantidad']) {
            return back()->with('error', "Solo quedan {$variante->stock} unidades disponibles.");
        }

        $carrito = session('tienda_carrito', []);
        $key = (string) $variante->id_variante;

        $cantidadActual = $carrito[$key]['cantidad'] ?? 0;
        $nuevaCantidad = min($cantidadActual + $data['cantidad'], $variante->stock, 10);

        $carrito[$key] = [
            'id_variante' => $variante->id_variante,
            'cantidad'    => $nuevaCantidad,
        ];
        session(['tienda_carrito' => $carrito]);

        return back()->with('success', "Agregado al carrito: {$variante->producto->nombre}" .
            ($variante->talla ? " (talla {$variante->talla})" : ''));
    }

    public function actualizarCarrito(Request $request, int $idVariante)
    {
        $data = $request->validate([
            'cantidad' => 'required|integer|min:0|max:10',
        ]);

        $carrito = session('tienda_carrito', []);
        $key = (string) $idVariante;

        if ($data['cantidad'] === 0) {
            unset($carrito[$key]);
        } else {
            $variante = ProductoVariante::find($idVariante);
            if (!$variante) {
                unset($carrito[$key]);
            } else {
                $carrito[$key] = [
                    'id_variante' => $idVariante,
                    'cantidad'    => min($data['cantidad'], $variante->stock),
                ];
            }
        }
        session(['tienda_carrito' => $carrito]);

        return back();
    }

    public function vaciarCarrito()
    {
        session()->forget('tienda_carrito');
        return back()->with('success', 'Carrito vaciado.');
    }

    // ─────────────────────────────────────────────
    //  CHECKOUT / GENERAR PEDIDO
    // ─────────────────────────────────────────────

    public function checkout(Request $request)
    {
        $items = $this->itemsCarrito();
        if ($items->isEmpty()) {
            return redirect()->route('tienda.catalogo')->with('error', 'Tu carrito está vacío.');
        }

        $total = $items->sum('subtotal');
        $cuenta = ConfiguracionInstitucional::cuentaBancaria();
        $ubicacion = ConfiguracionInstitucional::get('tienda.ubicacion_entrega');
        $horario = ConfiguracionInstitucional::get('tienda.horario_entrega');
        $instrucciones = ConfiguracionInstitucional::get('tienda.instrucciones_pago');

        return view('tienda.checkout', compact('items', 'total', 'cuenta', 'ubicacion', 'horario', 'instrucciones'));
    }

    public function confirmarPedido(Request $request)
    {
        $items = $this->itemsCarrito();
        if ($items->isEmpty()) {
            return redirect()->route('tienda.catalogo')->with('error', 'Tu carrito está vacío.');
        }

        $nivel = $this->nivelDelUsuario($request->user());

        $carrito = array_map(
            fn($it) => ['id_variante' => $it['id_variante'], 'cantidad' => $it['cantidad']],
            session('tienda_carrito', [])
        );
        $carrito = array_values($carrito);

        try {
            $pedido = $this->pedidoService->crearPedido($request->user()->id, $carrito, $nivel);
        } catch (RuntimeException $e) {
            return redirect()->route('tienda.checkout')->with('error', $e->getMessage());
        }

        session()->forget('tienda_carrito');

        return redirect()->route('tienda.pedido.show', $pedido)
            ->with('success', "Pedido {$pedido->folio} generado. Sube tu váucher para continuar.");
    }

    // ─────────────────────────────────────────────
    //  PEDIDOS DEL USUARIO
    // ─────────────────────────────────────────────

    public function pedidos(Request $request)
    {
        $pedidos = Pedido::with('items')
            ->where('user_id', $request->user()->id)
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('tienda.pedidos.index', compact('pedidos'));
    }

    public function verPedido(Request $request, Pedido $pedido)
    {
        abort_unless($pedido->user_id === $request->user()->id, 403);

        $pedido->load('items', 'historial.usuario');
        $cuenta = ConfiguracionInstitucional::cuentaBancaria();
        $ubicacion = ConfiguracionInstitucional::get('tienda.ubicacion_entrega');
        $horario = ConfiguracionInstitucional::get('tienda.horario_entrega');

        return view('tienda.pedidos.show', compact('pedido', 'cuenta', 'ubicacion', 'horario'));
    }

    public function subirVaucher(Request $request, Pedido $pedido)
    {
        abort_unless($pedido->user_id === $request->user()->id, 403);

        if (!in_array($pedido->estado, ['pendiente_pago', 'vaucher_enviado'], true)) {
            return back()->with('error', 'Este pedido ya no admite cambios de váucher.');
        }

        $request->validate([
            'vaucher' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ], [
            'vaucher.required' => 'Debes seleccionar un archivo.',
            'vaucher.mimes'    => 'El váucher debe ser PDF o imagen (JPG/PNG).',
            'vaucher.max'      => 'El archivo no debe pesar más de 5 MB.',
        ]);

        // Borra el váucher anterior si existía (caso rechazo + reenvío)
        if ($pedido->vaucher_path && Storage::disk('public')->exists($pedido->vaucher_path)) {
            Storage::disk('public')->delete($pedido->vaucher_path);
        }

        $path = $request->file('vaucher')->store('vauchers/pedidos', 'public');

        $this->pedidoService->asignarVaucher($pedido, $path, $request->user()->id);

        return back()->with('success', 'Váucher enviado. El Gestor Escolar lo revisará en las próximas horas.');
    }

    /**
     * Descarga el comprobante PDF del pedido (alumno/docente, o gestor).
     * Solo disponible cuando el pedido esta aprobado/listo/entregado.
     */
    public function comprobantePdf(Request $request, Pedido $pedido)
    {
        $esDueno  = $pedido->user_id === $request->user()->id;
        $esGestor = $request->user()->hasRole('gestor_escolar');
        abort_unless($esDueno || $esGestor, 403);

        if (!in_array($pedido->estado, ['aprobado', 'listo_recoger', 'entregado'], true)) {
            return back()->with('error', 'El comprobante se genera una vez aprobado el pago.');
        }

        $pedido->load('usuario', 'items', 'revisor');

        $ubicacion = ConfiguracionInstitucional::get('tienda.ubicacion_entrega', 'Oficinas de Gestor Escolar');
        $horario   = ConfiguracionInstitucional::get('tienda.horario_entrega', 'Lunes a Viernes');

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.comprobante-pedido', [
            'pedido'    => $pedido,
            'ubicacion' => $ubicacion,
            'horario'   => $horario,
        ]);

        return $pdf->stream("comprobante_{$pedido->folio}.pdf");
    }

    public function cancelarPedido(Request $request, Pedido $pedido)
    {
        abort_unless($pedido->user_id === $request->user()->id, 403);

        if (!in_array($pedido->estado, ['pendiente_pago', 'vaucher_enviado'], true)) {
            return back()->with('error', 'Este pedido ya no se puede cancelar.');
        }

        try {
            $this->pedidoService->transicionar($pedido, 'cancelado', $request->user()->id, 'Cancelado por el usuario.');
        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Pedido cancelado. El stock se liberó automáticamente.');
    }

    // ─────────────────────────────────────────────
    //  HELPERS PRIVADOS
    // ─────────────────────────────────────────────

    /** Determina el nivel educativo del usuario para filtrar el catálogo. */
    private function nivelDelUsuario(User $user): string
    {
        if ($user->alumno) {
            return $user->alumno->nivel_educativo ?? 'universidad';
        }
        if ($user->docente) {
            return $user->docente->nivel_educativo ?? 'universidad';
        }
        return 'universidad';
    }

    /** Resumen del carrito (cantidad total + total) para mostrar en el header. */
    private function resumenCarrito(): array
    {
        $items = $this->itemsCarrito();
        return [
            'count' => $items->sum('cantidad'),
            'total' => $items->sum('subtotal'),
        ];
    }

    /**
     * Convierte el carrito de sesión a colección hidratada con datos del producto.
     * Limpia automáticamente items con variantes que ya no existen.
     */
    private function itemsCarrito(): \Illuminate\Support\Collection
    {
        $carrito = session('tienda_carrito', []);
        if (empty($carrito)) return collect();

        $ids = array_keys($carrito);
        $variantes = ProductoVariante::with('producto')
            ->whereIn('id_variante', $ids)
            ->get()->keyBy('id_variante');

        $items = collect();
        $carritoLimpio = [];

        foreach ($carrito as $key => $linea) {
            $v = $variantes->get($linea['id_variante']);
            if (!$v || !$v->producto->activo) continue;

            $cantidad = min($linea['cantidad'], $v->stock);
            if ($cantidad <= 0) continue;

            $items->push((object) [
                'id_variante' => $v->id_variante,
                'variante'    => $v,
                'producto'    => $v->producto,
                'cantidad'    => $cantidad,
                'precio'      => (float) $v->producto->precio,
                'subtotal'    => $cantidad * (float) $v->producto->precio,
            ]);
            $carritoLimpio[$key] = ['id_variante' => $v->id_variante, 'cantidad' => $cantidad];
        }

        // Si el carrito cambió (productos eliminados o stock reducido), guarda la versión limpia.
        if (count($carritoLimpio) !== count($carrito)) {
            session(['tienda_carrito' => $carritoLimpio]);
        }

        return $items;
    }
}
