<?php

namespace Database\Seeders;

use App\Models\ConfiguracionInstitucional;
use App\Models\MovimientoInventario;
use App\Models\Producto;
use App\Models\ProductoVariante;
use Illuminate\Database\Seeder;

/**
 * Datos demo del modulo de Tienda Institucional:
 *
 *  - Configuracion bancaria placeholder (admin la cambia desde UI).
 *  - 5 productos: 3 universidad + 2 bachillerato.
 *  - Variantes por talla donde aplica.
 *  - Stock inicial cargado con movimiento "entrada".
 *
 * Ejecutar con: `php artisan db:seed --class=TiendaDemoSeeder`
 */
class TiendaDemoSeeder extends Seeder
{
    public function run(): void
    {
        // ─── 1. Configuracion bancaria placeholder ───────────────
        ConfiguracionInstitucional::set(
            'tienda.cuenta_banco',
            'BBVA',
            'Banco institucional', 'tienda'
        );
        ConfiguracionInstitucional::set(
            'tienda.cuenta_titular',
            'Universidad de Los Angeles UDEA SA de CV',
            'Titular registrado en el banco', 'tienda'
        );
        ConfiguracionInstitucional::set(
            'tienda.cuenta_numero',
            '0123 4567 8901',
            'Numero de cuenta de 16 digitos', 'tienda'
        );
        ConfiguracionInstitucional::set(
            'tienda.cuenta_clabe',
            '012180001234567890',
            'CLABE de 18 digitos para transferencias SPEI', 'tienda'
        );
        ConfiguracionInstitucional::set(
            'tienda.referencia_prefijo',
            'UDEA-',
            'Prefijo de referencia bancaria. Se concatena con el folio del pedido.',
            'tienda'
        );
        ConfiguracionInstitucional::set(
            'tienda.ubicacion_entrega',
            'Oficinas de Gestor Escolar — Edificio A, Planta Baja, Universidad de Los Angeles',
            'Ubicacion fisica donde se entregan los pedidos', 'tienda'
        );
        ConfiguracionInstitucional::set(
            'tienda.horario_entrega',
            'Lunes a Viernes de 9:00 a 17:00 hrs',
            'Horario de atencion para entrega de productos', 'tienda'
        );
        ConfiguracionInstitucional::set(
            'tienda.instrucciones_pago',
            'Realiza el deposito o transferencia por el monto exacto. Sube el comprobante en formato PDF o imagen. Tu pedido sera validado en un plazo maximo de 24 horas habiles.',
            'Texto que vera el alumno al generar pedido', 'tienda'
        );

        $this->command->info('Configuracion bancaria placeholder cargada.');

        // ─── 2. Productos Universidad ─────────────────────────────
        $playeraUni = $this->crearProducto([
            'codigo'          => 'PLAY-UNI',
            'nombre'          => 'Playera Polo UDEA Universidad',
            'descripcion'     => 'Playera tipo polo color azul marino con logo institucional bordado. Manga corta, tejido pique 100% algodon.',
            'categoria'       => 'uniforme',
            'precio'          => 350.00,
            'nivel_educativo' => 'universidad',
            'tiene_tallas'    => true,
        ], ['XS', 'S', 'M', 'L', 'XL'], [10, 15, 20, 15, 8]);

        $sudaderaUni = $this->crearProducto([
            'codigo'          => 'SUDA-UNI',
            'nombre'          => 'Sudadera UDEA Universidad',
            'descripcion'     => 'Sudadera con capucha color azul, logo institucional. Tejido 80% algodon / 20% poliester.',
            'categoria'       => 'uniforme',
            'precio'          => 580.00,
            'nivel_educativo' => 'universidad',
            'tiene_tallas'    => true,
        ], ['S', 'M', 'L', 'XL'], [8, 12, 12, 6]);

        $credencialUni = $this->crearProducto([
            'codigo'          => 'CRED-UNI',
            'nombre'          => 'Reposicion de Credencial Universitaria',
            'descripcion'     => 'Reposicion oficial de credencial estudiantil con foto, codigo QR y firma del Gestor Escolar.',
            'categoria'       => 'credencial',
            'precio'          => 150.00,
            'nivel_educativo' => 'universidad',
            'tiene_tallas'    => false,
        ], [null], [50]);

        // ─── 3. Productos Bachillerato ────────────────────────────
        $playeraBachi = $this->crearProducto([
            'codigo'          => 'PLAY-BACH',
            'nombre'          => 'Playera Polo UDEA Bachillerato',
            'descripcion'     => 'Playera tipo polo color blanco con logo de Bachillerato UDEA. Manga corta.',
            'categoria'       => 'uniforme',
            'precio'          => 290.00,
            'nivel_educativo' => 'bachillerato',
            'tiene_tallas'    => true,
        ], ['XS', 'S', 'M', 'L'], [12, 18, 18, 10]);

        $credencialBachi = $this->crearProducto([
            'codigo'          => 'CRED-BACH',
            'nombre'          => 'Reposicion de Credencial Bachillerato',
            'descripcion'     => 'Reposicion oficial de credencial de bachillerato.',
            'categoria'       => 'credencial',
            'precio'          => 130.00,
            'nivel_educativo' => 'bachillerato',
            'tiene_tallas'    => false,
        ], [null], [40]);

        $this->command->info('');
        $this->command->info('═══════════════════════════════════════════════════');
        $this->command->info(' Tienda Institucional inicializada con datos demo');
        $this->command->info('═══════════════════════════════════════════════════');
        $this->command->line('  Universidad: 3 productos');
        $this->command->line('  Bachillerato: 2 productos');
        $this->command->line('  Cuenta bancaria placeholder cargada (admin debe actualizar)');
        $this->command->info('═══════════════════════════════════════════════════');
    }

    /**
     * Crea producto + variantes + movimiento de entrada inicial.
     *
     * @param array $data
     * @param array $tallas    Lista de tallas (null para producto sin tallas)
     * @param array $stocks    Stock inicial por talla (mismo orden que $tallas)
     */
    private function crearProducto(array $data, array $tallas, array $stocks): Producto
    {
        $producto = Producto::sinFiltroNivel()->updateOrCreate(
            ['codigo' => $data['codigo']],
            $data
        );

        foreach ($tallas as $i => $talla) {
            $stock = $stocks[$i] ?? 0;
            $codigoVar = $producto->codigo . ($talla ? "-{$talla}" : '');

            $variante = ProductoVariante::updateOrCreate(
                ['codigo_variante' => $codigoVar],
                [
                    'id_producto'   => $producto->id_producto,
                    'talla'         => $talla,
                    'stock'         => $stock,
                    'stock_minimo'  => 3,
                ]
            );

            // Movimiento de entrada inicial (idempotente: solo si no existe ya)
            $yaCargado = MovimientoInventario::where('id_variante', $variante->id_variante)
                ->where('tipo', 'entrada')
                ->where('motivo', 'Stock inicial del seeder')
                ->exists();

            if (!$yaCargado && $stock > 0) {
                MovimientoInventario::create([
                    'id_variante'      => $variante->id_variante,
                    'tipo'             => 'entrada',
                    'cantidad'         => $stock,
                    'stock_resultante' => $stock,
                    'user_id'          => null,
                    'motivo'           => 'Stock inicial del seeder',
                ]);
            }
        }

        return $producto;
    }
}
