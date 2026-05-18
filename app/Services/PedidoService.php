<?php

namespace App\Services;

use App\Models\MovimientoInventario;
use App\Models\Pedido;
use App\Models\PedidoEstadoHistorial;
use App\Models\PedidoItem;
use App\Models\ProductoVariante;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Logica central del flujo de pedidos.
 *
 * Encapsula: creacion, transicion de estados, reserva/liberacion de stock
 * y bitacora de movimientos. Todos los metodos operan dentro de transacciones
 * para garantizar consistencia entre pedido + items + stock.
 */
class PedidoService
{
    /**
     * Crea un pedido a partir de un carrito.
     *
     * @param int   $userId
     * @param array $carrito  [{id_variante, cantidad}, ...]
     * @param string $nivelEducativo
     * @return Pedido
     */
    public function crearPedido(int $userId, array $carrito, string $nivelEducativo): Pedido
    {
        if (empty($carrito)) {
            throw new RuntimeException('El carrito esta vacio.');
        }

        return DB::transaction(function () use ($userId, $carrito, $nivelEducativo) {
            // 1. Reservar stock y construir items
            $items = [];
            $total = 0;

            foreach ($carrito as $linea) {
                $variante = ProductoVariante::with('producto')
                    ->where('id_variante', $linea['id_variante'])
                    ->lockForUpdate()
                    ->first();

                if (!$variante) {
                    throw new RuntimeException("Variante {$linea['id_variante']} no encontrada.");
                }
                if (!$variante->producto->activo) {
                    throw new RuntimeException("El producto '{$variante->producto->nombre}' ya no esta disponible.");
                }
                if ($variante->stock < $linea['cantidad']) {
                    throw new RuntimeException(
                        "Stock insuficiente para '{$variante->producto->nombre}'" .
                        ($variante->talla ? " talla {$variante->talla}" : '') .
                        ". Disponible: {$variante->stock}."
                    );
                }

                $precio = (float) $variante->producto->precio;
                $sub    = $precio * $linea['cantidad'];
                $total += $sub;

                $items[] = [
                    'variante'        => $variante,
                    'cantidad'        => $linea['cantidad'],
                    'precio_unitario' => $precio,
                    'subtotal'        => $sub,
                ];
            }

            // 2. Crear pedido
            $pedido = Pedido::create([
                'user_id'         => $userId,
                'nivel_educativo' => $nivelEducativo,
                'total'           => $total,
                'estado'          => 'pendiente_pago',
            ]);

            // 3. Crear items + descontar stock + registrar movimiento
            foreach ($items as $i) {
                /** @var ProductoVariante $v */
                $v = $i['variante'];

                PedidoItem::create([
                    'id_pedido'        => $pedido->id_pedido,
                    'id_variante'      => $v->id_variante,
                    'nombre_snapshot'  => $v->producto->nombre,
                    'codigo_snapshot'  => $v->codigo_variante,
                    'talla_snapshot'   => $v->talla,
                    'cantidad'         => $i['cantidad'],
                    'precio_unitario'  => $i['precio_unitario'],
                    'subtotal'         => $i['subtotal'],
                ]);

                $v->decrement('stock', $i['cantidad']);

                MovimientoInventario::create([
                    'id_variante'      => $v->id_variante,
                    'tipo'             => 'pedido',
                    'cantidad'         => -$i['cantidad'],
                    'stock_resultante' => $v->fresh()->stock,
                    'id_pedido'        => $pedido->id_pedido,
                    'user_id'          => $userId,
                    'motivo'           => 'Reserva por pedido ' . $pedido->folio,
                ]);
            }

            // 4. Bitacora inicial
            $this->registrarHistorial($pedido, null, 'pendiente_pago', $userId, 'Pedido creado.');

            return $pedido->fresh('items');
        });
    }

    /**
     * Transiciona un pedido a un nuevo estado.
     * Valida que la transicion sea legal segun Pedido::TRANSICIONES.
     */
    public function transicionar(Pedido $pedido, string $nuevoEstado, int $userId, ?string $comentario = null): Pedido
    {
        if (!$pedido->puedeTransicionarA($nuevoEstado)) {
            throw new RuntimeException(
                "No se permite pasar de '{$pedido->estado}' a '{$nuevoEstado}'."
            );
        }

        return DB::transaction(function () use ($pedido, $nuevoEstado, $userId, $comentario) {
            $estadoAnterior = $pedido->estado;

            // Si se cancela, liberar stock reservado.
            if ($nuevoEstado === 'cancelado' && in_array($estadoAnterior, ['pendiente_pago', 'vaucher_enviado', 'aprobado'], true)) {
                $this->liberarStock($pedido, $userId);
            }

            // Campos especificos por transicion
            $cambios = ['estado' => $nuevoEstado];

            if ($nuevoEstado === 'aprobado') {
                $cambios['revisado_por'] = $userId;
                $cambios['revisado_en']  = now();
                $cambios['motivo_rechazo'] = null;
            }
            if ($nuevoEstado === 'pendiente_pago' && $estadoAnterior === 'vaucher_enviado') {
                // Rechazo: el alumno debe subir nuevo vaucher
                $cambios['motivo_rechazo'] = $comentario;
                $cambios['vaucher_path']   = null;
                $cambios['vaucher_subido_en'] = null;
            }
            if ($nuevoEstado === 'listo_recoger') {
                $cambios['fecha_listo_recoger'] = now();
            }
            if ($nuevoEstado === 'entregado') {
                $cambios['entregado_por'] = $userId;
                $cambios['entregado_en']  = now();
            }

            $pedido->update($cambios);
            $this->registrarHistorial($pedido, $estadoAnterior, $nuevoEstado, $userId, $comentario);

            return $pedido->fresh();
        });
    }

    /**
     * Asocia el vaucher subido al pedido y lo transiciona a 'vaucher_enviado'.
     */
    public function asignarVaucher(Pedido $pedido, string $vaucherPath, int $userId): Pedido
    {
        return DB::transaction(function () use ($pedido, $vaucherPath, $userId) {
            $pedido->update([
                'vaucher_path'     => $vaucherPath,
                'vaucher_subido_en' => now(),
            ]);
            return $this->transicionar($pedido, 'vaucher_enviado', $userId, 'Vaucher subido por el alumno.');
        });
    }

    private function liberarStock(Pedido $pedido, int $userId): void
    {
        foreach ($pedido->items as $item) {
            $variante = ProductoVariante::lockForUpdate()->find($item->id_variante);
            if (!$variante) continue;

            $variante->increment('stock', $item->cantidad);

            MovimientoInventario::create([
                'id_variante'      => $variante->id_variante,
                'tipo'             => 'reverso',
                'cantidad'         => $item->cantidad,
                'stock_resultante' => $variante->fresh()->stock,
                'id_pedido'        => $pedido->id_pedido,
                'user_id'          => $userId,
                'motivo'           => 'Cancelacion de pedido ' . $pedido->folio,
            ]);
        }
    }

    private function registrarHistorial(Pedido $pedido, ?string $anterior, string $nuevo, int $userId, ?string $comentario = null): void
    {
        PedidoEstadoHistorial::create([
            'id_pedido'       => $pedido->id_pedido,
            'estado_anterior' => $anterior,
            'estado_nuevo'    => $nuevo,
            'user_id'         => $userId,
            'comentario'      => $comentario,
            'created_at'      => now(),
        ]);
    }
}
