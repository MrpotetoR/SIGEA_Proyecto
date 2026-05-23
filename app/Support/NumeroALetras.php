<?php

namespace App\Support;

/**
 * Convierte un número decimal a su representación en letras (español MX).
 *
 * Ejemplo:
 *   NumeroALetras::montoMxn(1234.56)
 *   // "(MIL DOSCIENTOS TREINTA Y CUATRO PESOS 56/100 M.N.)"
 *
 * Soporta montos enteros y con centavos. El resultado incluye paréntesis
 * y la abreviatura M.N. (Moneda Nacional) usada en comprobantes mexicanos.
 *
 * Rango soportado: 0.00 — 999,999,999.99.
 */
class NumeroALetras
{
    private const UNIDADES = [
        '', 'UNO', 'DOS', 'TRES', 'CUATRO', 'CINCO', 'SEIS', 'SIETE', 'OCHO', 'NUEVE',
        'DIEZ', 'ONCE', 'DOCE', 'TRECE', 'CATORCE', 'QUINCE',
        'DIECISÉIS', 'DIECISIETE', 'DIECIOCHO', 'DIECINUEVE',
    ];

    private const DECENAS = [
        '', '', 'VEINTI', 'TREINTA', 'CUARENTA', 'CINCUENTA',
        'SESENTA', 'SETENTA', 'OCHENTA', 'NOVENTA',
    ];

    private const CENTENAS = [
        '', 'CIENTO', 'DOSCIENTOS', 'TRESCIENTOS', 'CUATROCIENTOS',
        'QUINIENTOS', 'SEISCIENTOS', 'SETECIENTOS', 'OCHOCIENTOS', 'NOVECIENTOS',
    ];

    public static function montoMxn(float $monto): string
    {
        $entero   = (int) floor($monto);
        $centavos = (int) round(($monto - $entero) * 100);
        // round puede dar 100 si los decimales eran .996+
        if ($centavos === 100) {
            $entero += 1;
            $centavos = 0;
        }

        $palabraEntero = self::convertirEntero($entero);

        // Apocope mexicano antes de PESO(S): "UN PESO", "VEINTIÚN PESOS".
        // Cubre "UNO" final aislado y "VEINTIUNO" final.
        $palabraEntero = preg_replace('/VEINTIUNO$/', 'VEINTIÚN', $palabraEntero);
        $palabraEntero = preg_replace('/\bUNO$/', 'UN', $palabraEntero);

        $pesos       = $entero === 1 ? 'PESO' : 'PESOS';
        $centavosStr = str_pad((string) $centavos, 2, '0', STR_PAD_LEFT);

        return "({$palabraEntero} {$pesos} {$centavosStr}/100 M.N.)";
    }

    private static function convertirEntero(int $n): string
    {
        if ($n === 0) return 'CERO';
        if ($n < 0)  return 'MENOS ' . self::convertirEntero(-$n);

        // Hasta millones
        if ($n >= 1_000_000) {
            $millones = intdiv($n, 1_000_000);
            $resto    = $n % 1_000_000;
            $prefijo  = $millones === 1 ? 'UN MILLÓN' : self::convertirEntero($millones) . ' MILLONES';
            return $resto === 0 ? $prefijo : $prefijo . ' ' . self::convertirEntero($resto);
        }

        // Hasta miles
        if ($n >= 1000) {
            $miles = intdiv($n, 1000);
            $resto = $n % 1000;
            $prefijo = $miles === 1 ? 'MIL' : self::convertirEntero($miles) . ' MIL';
            return $resto === 0 ? $prefijo : $prefijo . ' ' . self::convertirEntero($resto);
        }

        // Centenas
        if ($n >= 100) {
            if ($n === 100) return 'CIEN';
            $cen = intdiv($n, 100);
            $resto = $n % 100;
            $prefijo = self::CENTENAS[$cen];
            return $resto === 0 ? $prefijo : $prefijo . ' ' . self::convertirEntero($resto);
        }

        // Decenas y unidades
        if ($n < 20) {
            return self::UNIDADES[$n];
        }
        if ($n < 30) {
            // 20 = "VEINTE", 21+ = "VEINTIUNO", "VEINTIDÓS", etc.
            if ($n === 20) return 'VEINTE';
            return self::DECENAS[2] . self::UNIDADES[$n - 20];
        }
        $dec = intdiv($n, 10);
        $uni = $n % 10;
        $prefijo = self::DECENAS[$dec];
        return $uni === 0 ? $prefijo : $prefijo . ' Y ' . self::UNIDADES[$uni];
    }
}
