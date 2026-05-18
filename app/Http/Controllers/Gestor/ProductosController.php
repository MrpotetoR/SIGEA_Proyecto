<?php

namespace App\Http\Controllers\Gestor;

use App\Http\Controllers\Controller;
use App\Models\MovimientoInventario;
use App\Models\Producto;
use App\Models\ProductoImagen;
use App\Models\ProductoVariante;
use App\Support\ContextoEducativo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * CRUD de productos de la Tienda Institucional.
 *
 * Cada producto puede tener:
 *   - Una imagen principal (portada en catalogo)
 *   - Multiples imagenes secundarias (galeria)
 *   - 1+ variantes con stock independiente (por talla, o variante unica)
 *
 * El stock se administra desde aqui creando la variante; los movimientos
 * en bulk (entradas masivas, ajustes) viven en InventarioController.
 */
class ProductosController extends Controller
{
    public function index(Request $request)
    {
        $productos = Producto::with(['variantes', 'imagenes'])
            ->withSum('variantes as stock_total', 'stock')
            ->when($request->buscar, fn($q) =>
                $q->where(fn($w) =>
                    $w->where('nombre', 'like', "%{$request->buscar}%")
                      ->orWhere('codigo', 'like', "%{$request->buscar}%")
                )
            )
            ->when($request->categoria, fn($q) => $q->where('categoria', $request->categoria))
            ->when($request->activo !== null && $request->activo !== '', fn($q) => $q->where('activo', (bool) $request->activo))
            ->orderBy('nombre')
            ->paginate(20)->withQueryString();

        return view('gestor.productos.index', compact('productos'));
    }

    public function create()
    {
        return view('gestor.productos.create');
    }

    public function store(Request $request)
    {
        $contexto = ContextoEducativo::actual();

        $data = $request->validate([
            'codigo'         => 'required|string|max:30|unique:producto,codigo|regex:/^[A-Z0-9\-]+$/i',
            'nombre'         => 'required|string|max:150',
            'descripcion'    => 'nullable|string|max:2000',
            'categoria'      => 'required|in:' . implode(',', array_keys(Producto::CATEGORIAS)),
            'precio'         => 'required|numeric|min:0|max:999999.99',
            'tiene_tallas'   => 'nullable|boolean',
            'imagen_principal' => 'nullable|image|max:5120',
            'galeria.*'      => 'nullable|image|max:5120',
            'tallas'         => 'nullable|array',
            'tallas.*'       => 'string|max:10',
            'stocks'         => 'nullable|array',
            'stocks.*'       => 'integer|min:0',
        ], [
            'codigo.regex'   => 'El código solo admite letras, números y guion.',
        ]);

        DB::transaction(function () use ($request, $data, $contexto) {
            // 1. Subir imagen principal
            $imagenPath = null;
            if ($request->hasFile('imagen_principal')) {
                $imagenPath = $request->file('imagen_principal')->store('productos', 'public');
            }

            // 2. Crear producto
            $producto = Producto::create([
                'codigo'           => strtoupper($data['codigo']),
                'nombre'           => $data['nombre'],
                'descripcion'      => $data['descripcion'] ?? null,
                'categoria'        => $data['categoria'],
                'precio'           => $data['precio'],
                'nivel_educativo'  => $contexto,
                'tiene_tallas'     => $request->boolean('tiene_tallas'),
                'imagen_principal' => $imagenPath,
                'activo'           => true,
            ]);

            // 3. Crear variantes
            $tallas = $request->boolean('tiene_tallas') ? ($data['tallas'] ?? []) : [null];
            $stocks = $data['stocks'] ?? [];

            foreach ($tallas as $i => $talla) {
                $stock = (int) ($stocks[$i] ?? 0);
                $codigoVar = $producto->codigo . ($talla ? "-{$talla}" : '');

                $variante = ProductoVariante::create([
                    'id_producto'    => $producto->id_producto,
                    'codigo_variante' => $codigoVar,
                    'talla'          => $talla ?: null,
                    'stock'          => $stock,
                    'stock_minimo'   => 3,
                ]);

                if ($stock > 0) {
                    MovimientoInventario::create([
                        'id_variante'      => $variante->id_variante,
                        'tipo'             => 'entrada',
                        'cantidad'         => $stock,
                        'stock_resultante' => $stock,
                        'user_id'          => auth()->id(),
                        'motivo'           => 'Stock inicial al crear el producto',
                    ]);
                }
            }

            // 4. Subir galeria
            if ($request->hasFile('galeria')) {
                foreach ($request->file('galeria') as $i => $file) {
                    $path = $file->store('productos/galeria', 'public');
                    ProductoImagen::create([
                        'id_producto'  => $producto->id_producto,
                        'archivo_path' => $path,
                        'orden'        => $i + 1,
                    ]);
                }
            }
        });

        return redirect()->route('gestor.productos.index')->with('success', 'Producto creado correctamente.');
    }

    public function show(Producto $producto)
    {
        $producto->load(['variantes', 'imagenes']);

        $movimientos = MovimientoInventario::whereIn('id_variante', $producto->variantes->pluck('id_variante'))
            ->with('variante', 'usuario', 'pedido')
            ->orderByDesc('created_at')
            ->take(50)
            ->get();

        return view('gestor.productos.show', compact('producto', 'movimientos'));
    }

    public function edit(Producto $producto)
    {
        $producto->load(['variantes', 'imagenes']);
        return view('gestor.productos.edit', compact('producto'));
    }

    public function update(Request $request, Producto $producto)
    {
        $data = $request->validate([
            'nombre'         => 'required|string|max:150',
            'descripcion'    => 'nullable|string|max:2000',
            'categoria'      => 'required|in:' . implode(',', array_keys(Producto::CATEGORIAS)),
            'precio'         => 'required|numeric|min:0|max:999999.99',
            'activo'         => 'nullable|boolean',
            'imagen_principal' => 'nullable|image|max:5120',
            'galeria.*'      => 'nullable|image|max:5120',
        ]);

        DB::transaction(function () use ($request, $data, $producto) {
            // Imagen principal nueva
            if ($request->hasFile('imagen_principal')) {
                if ($producto->imagen_principal) {
                    Storage::disk('public')->delete($producto->imagen_principal);
                }
                $data['imagen_principal'] = $request->file('imagen_principal')->store('productos', 'public');
            }

            $data['activo'] = $request->boolean('activo');
            $producto->update($data);

            // Galeria adicional
            if ($request->hasFile('galeria')) {
                $orden = $producto->imagenes()->max('orden') ?? 0;
                foreach ($request->file('galeria') as $file) {
                    $path = $file->store('productos/galeria', 'public');
                    ProductoImagen::create([
                        'id_producto'  => $producto->id_producto,
                        'archivo_path' => $path,
                        'orden'        => ++$orden,
                    ]);
                }
            }
        });

        return redirect()->route('gestor.productos.show', $producto)->with('success', 'Producto actualizado.');
    }

    public function destroy(Producto $producto)
    {
        // Soft-delete logico: solo desactiva. Productos con historial de
        // pedidos NO deben borrarse fisicamente para no romper comprobantes.
        $producto->update(['activo' => false]);

        return redirect()->route('gestor.productos.show', $producto)
            ->with('success', 'Producto desactivado del catálogo. El historial de pedidos se conserva.');
    }

    /** Reactiva un producto previamente desactivado. Vuelve a aparecer en el catalogo publico. */
    public function reactivar(Producto $producto)
    {
        $producto->update(['activo' => true]);

        return redirect()->route('gestor.productos.show', $producto)
            ->with('success', 'Producto reactivado. Volverá a aparecer en el catálogo público.');
    }

    /**
     * Elimina permanentemente el producto, sus variantes, imágenes y movimientos.
     *
     * Salvaguarda: NO permite eliminar productos con historial de pedidos
     * (para preservar comprobantes pasados y trazabilidad fiscal). En ese
     * caso solo se puede desactivar (soft delete via destroy()).
     */
    public function eliminarPermanente(Producto $producto)
    {
        $variantesIds = $producto->variantes->pluck('id_variante')->all();
        $tienePedidos = \App\Models\PedidoItem::whereIn('id_variante', $variantesIds)->exists();

        if ($tienePedidos) {
            return back()->with('error',
                'No se puede eliminar este producto: tiene pedidos asociados. ' .
                'Para retirarlo del catálogo usa "Desactivar producto" — el historial se conserva.'
            );
        }

        DB::transaction(function () use ($producto, $variantesIds) {
            // 1. Eliminar archivos físicos del storage
            if ($producto->imagen_principal) {
                Storage::disk('public')->delete($producto->imagen_principal);
            }
            foreach ($producto->imagenes as $img) {
                if ($img->archivo_path) {
                    Storage::disk('public')->delete($img->archivo_path);
                }
            }

            // 2. Eliminar movimientos de inventario (no tienen cascade automático)
            if (!empty($variantesIds)) {
                MovimientoInventario::whereIn('id_variante', $variantesIds)->delete();
            }

            // 3. Eliminar el producto. Cascades automáticos:
            //    - producto_variante (cascadeOnDelete)
            //    - producto_imagen (cascadeOnDelete)
            $producto->delete();
        });

        return redirect()->route('gestor.productos.index')
            ->with('success', 'Producto eliminado permanentemente.');
    }

    // ─────────────────────────────────────────────
    //  GESTIÓN DE STOCK / VARIANTES
    // ─────────────────────────────────────────────

    /**
     * Guarda en batch los cambios de stock y disponibilidad de todas
     * las variantes de un producto. Se invoca al salir del modo edición.
     *
     * Por cada variante con cambio de stock se registra un MovimientoInventario.
     * El motivo es opcional; si no se especifica se usa "Ajuste manual de stock".
     */
    public function guardarCambiosBatch(Request $request, Producto $producto)
    {
        $request->validate([
            'cambios'                => 'nullable|array',
            'cambios.*.stock'        => ['required', 'integer', 'min:0', 'max:' . self::STOCK_MAXIMO],
            'cambios.*.stock_minimo' => 'nullable|integer|min:0|max:9999',
            'cambios.*.disponible'   => 'nullable|in:0,1',
            'cambios.*.motivo'       => 'nullable|string|max:500',
        ], [
            'cambios.*.stock.max' => 'El stock máximo por variante es de ' . self::STOCK_MAXIMO . ' unidades.',
        ]);

        $cambios = $request->input('cambios', []);
        $afectadas = 0;

        DB::transaction(function () use ($cambios, $producto, $request, &$afectadas) {
            foreach ($cambios as $id => $cambio) {
                $variante = ProductoVariante::where('id_producto', $producto->id_producto)
                    ->where('id_variante', $id)
                    ->lockForUpdate()
                    ->first();
                if (!$variante) continue;

                $stockNuevo      = (int) $cambio['stock'];
                $minimoNuevo     = isset($cambio['stock_minimo']) ? (int) $cambio['stock_minimo'] : (int) $variante->stock_minimo;
                $disponibleNuevo = isset($cambio['disponible']) ? (bool) $cambio['disponible'] : $variante->disponible;
                $diff            = $stockNuevo - $variante->stock;

                $cambioStock      = $diff !== 0;
                $cambioMinimo     = $minimoNuevo !== (int) $variante->stock_minimo;
                $cambioDisponible = $disponibleNuevo !== (bool) $variante->disponible;

                if (!$cambioStock && !$cambioMinimo && !$cambioDisponible) continue;

                $variante->update([
                    'stock'        => $stockNuevo,
                    'stock_minimo' => $minimoNuevo,
                    'disponible'   => $disponibleNuevo,
                ]);

                if ($cambioStock) {
                    $tipo   = $diff > 0 ? 'entrada' : 'salida';
                    $motivo = !empty($cambio['motivo']) ? trim($cambio['motivo']) : 'Ajuste manual de stock';

                    MovimientoInventario::create([
                        'id_variante'      => $variante->id_variante,
                        'tipo'             => $tipo,
                        'cantidad'         => $diff,
                        'stock_resultante' => $stockNuevo,
                        'user_id'          => $request->user()->id,
                        'motivo'           => $motivo,
                    ]);
                }

                $afectadas++;
            }
        });

        if ($afectadas === 0) {
            return redirect()->route('gestor.productos.show', $producto)
                ->with('success', 'Modo edición cerrado. No hubo cambios pendientes.');
        }

        return redirect()->route('gestor.productos.show', $producto)
            ->with('success', "Cambios guardados ({$afectadas} variante" . ($afectadas === 1 ? '' : 's') . " modificada" . ($afectadas === 1 ? '' : 's') . ").");
    }

    /**
     * Suma o resta stock a una variante con motivo explícito.
     * Cada movimiento queda registrado en movimiento_inventario.
     */
    public function ajustarStock(Request $request, ProductoVariante $variante)
    {
        $data = $request->validate([
            'tipo'     => 'required|in:entrada,salida',
            'cantidad' => 'required|integer|min:1|max:9999',
            'motivo'   => 'required|string|min:3|max:200',
        ], [
            'motivo.required' => 'Debes capturar un motivo para el movimiento.',
            'motivo.min'      => 'El motivo es muy corto (mínimo 3 caracteres).',
        ]);

        $diff = $data['tipo'] === 'salida' ? -$data['cantidad'] : $data['cantidad'];

        if ($variante->stock + $diff < 0) {
            return back()->with('error',
                "No puedes restar {$data['cantidad']} unidades; el stock actual es de {$variante->stock}."
            );
        }

        DB::transaction(function () use ($data, $diff, $variante, $request) {
            $variante->increment('stock', $diff);

            MovimientoInventario::create([
                'id_variante'      => $variante->id_variante,
                'tipo'             => $data['tipo'],
                'cantidad'         => $diff,
                'stock_resultante' => $variante->fresh()->stock,
                'user_id'          => $request->user()->id,
                'motivo'           => $data['motivo'],
            ]);
        });

        $accion = $data['tipo'] === 'entrada' ? 'agregadas' : 'restadas';
        return back()->with('success',
            "{$data['cantidad']} unidades {$accion}. Stock actual: " . $variante->fresh()->stock . '.'
        );
    }

    /** Actualiza solo el stock minimo (umbral de alerta) — sin movimiento. */
    public function actualizarStockMinimo(Request $request, ProductoVariante $variante)
    {
        $data = $request->validate([
            'stock_minimo' => 'required|integer|min:0|max:9999',
        ]);
        $variante->update($data);

        return back()->with('success', 'Stock mínimo actualizado.');
    }

    /** Tallas predefinidas para productos tipo uniforme. */
    public const TALLAS_DISPONIBLES = ['XS', 'S', 'M', 'L', 'XL', 'XXL'];

    /** Categorías que aceptan tallas. */
    public const CATEGORIAS_CON_TALLA = ['uniforme'];

    /** Motivos predefinidos para movimientos de inventario. */
    public const MOTIVOS_PREDEFINIDOS = [
        'Entrada de nuevo inventario',
        'Producto dañado',
        'Ajuste manual de stock',
        'Corrección administrativa',
        'Entrega de producto',
        'Pérdida o merma',
    ];

    /** Stock máximo permitido por variante (límite de seguridad de captura). */
    public const STOCK_MAXIMO = 2000;

    /** Agrega una nueva variante a un producto existente. */
    public function agregarVariante(Request $request, Producto $producto)
    {
        $aceptaTallas = in_array($producto->categoria, self::CATEGORIAS_CON_TALLA, true);

        $data = $request->validate([
            'talla'        => $aceptaTallas
                ? ['required', \Illuminate\Validation\Rule::in(self::TALLAS_DISPONIBLES)]
                : 'nullable',
            'stock'        => 'required|integer|min:0|max:9999',
            'stock_minimo' => 'nullable|integer|min:0|max:9999',
        ], [
            'talla.required' => 'Debes seleccionar una talla.',
            'talla.in'       => 'La talla seleccionada no es válida.',
        ]);

        $talla = $aceptaTallas ? strtoupper(trim($data['talla'])) : null;

        // Anti-duplicado: si el producto ya tiene esa talla, no permitir
        if ($aceptaTallas) {
            $yaExiste = ProductoVariante::where('id_producto', $producto->id_producto)
                ->where('talla', $talla)
                ->exists();
            if ($yaExiste) {
                return back()->with('error',
                    "La talla {$talla} ya está registrada en este producto. " .
                    "Usa los botones de Sumar / Restar / Eliminar sobre la variante existente."
                );
            }
        } else {
            // Productos sin talla: solo permitir UNA variante única
            if ($producto->variantes()->exists()) {
                return back()->with('error',
                    'Este producto no usa tallas y ya tiene su variante registrada. ' .
                    'Usa Sumar / Restar para ajustar su stock.'
                );
            }
        }

        $codigoVar = $producto->codigo . ($talla ? "-{$talla}" : '');

        DB::transaction(function () use ($producto, $talla, $codigoVar, $data, $request) {
            $variante = ProductoVariante::create([
                'id_producto'     => $producto->id_producto,
                'codigo_variante' => $codigoVar,
                'talla'           => $talla,
                'stock'           => $data['stock'],
                'stock_minimo'    => $data['stock_minimo'] ?? 3,
            ]);

            if ($data['stock'] > 0) {
                MovimientoInventario::create([
                    'id_variante'      => $variante->id_variante,
                    'tipo'             => 'entrada',
                    'cantidad'         => $data['stock'],
                    'stock_resultante' => $data['stock'],
                    'user_id'          => $request->user()->id,
                    'motivo'           => 'Stock inicial al agregar variante',
                ]);
            }
        });

        return back()->with('success', "Variante {$codigoVar} agregada.");
    }

    /**
     * Endpoint para subir imágenes adicionales a la galería desde la vista de detalle.
     * Permite multi-upload (drag and drop).
     */
    public function agregarImagenes(Request $request, Producto $producto)
    {
        $request->validate([
            'imagenes'   => 'required|array|max:10',
            'imagenes.*' => 'image|max:5120',
        ]);

        $orden = $producto->imagenes()->max('orden') ?? 0;
        foreach ($request->file('imagenes') as $file) {
            $path = $file->store('productos/galeria', 'public');
            ProductoImagen::create([
                'id_producto'  => $producto->id_producto,
                'archivo_path' => $path,
                'orden'        => ++$orden,
            ]);
        }

        return back()->with('success', count($request->file('imagenes')) . ' imagen(es) agregada(s) a la galería.');
    }

    /**
     * Elimina una variante. Solo se permite si no tiene pedidos históricos
     * (para no romper comprobantes pasados).
     */
    public function eliminarVariante(ProductoVariante $variante)
    {
        $tieneHistorial = \App\Models\PedidoItem::where('id_variante', $variante->id_variante)->exists();
        if ($tieneHistorial) {
            return back()->with('error',
                'No se puede eliminar: esta variante tiene pedidos asociados. Considera dejarla con stock 0.'
            );
        }

        $codigo = $variante->codigo_variante;
        $variante->delete();

        return back()->with('success', "Variante {$codigo} eliminada.");
    }

    /** Borra una imagen de galeria. */
    public function eliminarImagen(ProductoImagen $imagen)
    {
        if ($imagen->archivo_path) {
            Storage::disk('public')->delete($imagen->archivo_path);
        }
        $producto = $imagen->id_producto;
        $imagen->delete();

        return back()->with('success', 'Imagen eliminada.');
    }
}
