<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Saldo bajo en Caja Chica — UDEA</title>
</head>
<body style="font-family: Arial, sans-serif; background:#f4f4f4; padding:20px; margin:0;">

    @php
        $semColor = match($nivelSemaforo) {
            'rojo'     => ['bg' => '#dc2626', 'badge_bg' => '#fee2e2', 'badge_fg' => '#991b1b', 'label' => 'CRÍTICO'],
            'amarillo' => ['bg' => '#f59e0b', 'badge_bg' => '#fef3c7', 'badge_fg' => '#92400e', 'label' => 'INTERMEDIO'],
            default    => ['bg' => '#16a34a', 'badge_bg' => '#dcfce7', 'badge_fg' => '#166534', 'label' => 'SALUDABLE'],
        };
    @endphp

    <div style="max-width:600px; margin:auto; background:white; border-radius:10px; overflow:hidden; box-shadow:0 2px 8px rgba(0,0,0,0.06);">

        {{-- Header --}}
        <div style="background:#0606F0; color:white; padding:24px; text-align:center;">
            <h1 style="margin:0; font-size:22px;">Reposición pendiente</h1>
            <p style="margin:6px 0 0; font-size:14px; opacity:0.9;">
                Universidad de Los Ángeles · UDEA — Caja Chica
            </p>
        </div>

        <div style="padding:30px;">
            <p style="font-size:15px; margin-top:0;">
                Hola <strong>{{ $nombreDestinatario }}</strong>,
            </p>

            <p style="font-size:14px; color:#444;">
                @if($diasParaFinMes <= 0)
                    El mes termina <strong>hoy</strong> y el fondo de Caja Chica todavía
                    no se ha restablecido al monto base. Te pedimos reponer
                    <strong style="color:#dc2626;">${{ $faltante }}</strong>
                    para conservar el saldo objetivo de la institución.
                @else
                    Faltan <strong style="color:#dc2626;">{{ $diasParaFinMes }} día{{ $diasParaFinMes === 1 ? '' : 's' }}</strong>
                    para el cierre de mes y el fondo de Caja Chica está por debajo del monto base.
                    Te pedimos reponer
                    <strong style="color:#dc2626;">${{ $faltante }}</strong>
                    antes del cierre.
                @endif
            </p>

            {{-- Tarjeta con estado del fondo --}}
            <div style="background:#f8f9fa; padding:18px; border-radius:8px; margin:24px 0; border-left:4px solid {{ $semColor['bg'] }};">
                <p style="margin:0 0 12px; font-size:11px; text-transform:uppercase; color:#888; font-weight:bold; letter-spacing:0.05em;">
                    Estado actual del fondo
                </p>

                <table style="width:100%; font-size:13px; color:#333;">
                    <tr>
                        <td style="padding:6px 0; color:#666;">Saldo actual:</td>
                        <td style="padding:6px 0; font-weight:bold; font-family:monospace; text-align:right;">
                            ${{ $saldoActual }} MXN
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:6px 0; color:#666;">Monto base configurado:</td>
                        <td style="padding:6px 0; font-family:monospace; text-align:right;">
                            ${{ $montoBase }} MXN
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:6px 0; color:#666;">Falta reponer:</td>
                        <td style="padding:6px 0; font-weight:bold; color:#dc2626; font-family:monospace; text-align:right;">
                            ${{ $faltante }} MXN
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:6px 0; color:#666;">% disponible:</td>
                        <td style="padding:6px 0; text-align:right;">{{ $porcentaje }}%</td>
                    </tr>
                    <tr>
                        <td style="padding:6px 0; color:#666;">Semáforo:</td>
                        <td style="padding:6px 0; text-align:right;">
                            <span style="background:{{ $semColor['badge_bg'] }}; color:{{ $semColor['badge_fg'] }}; padding:3px 10px; border-radius:999px; font-size:11px; font-weight:bold;">
                                {{ $semColor['label'] }}
                            </span>
                        </td>
                    </tr>
                </table>

                {{-- Barra de progreso visual --}}
                <div style="margin-top:14px; background:#e5e7eb; border-radius:4px; height:8px; overflow:hidden;">
                    <div style="background:{{ $semColor['bg'] }}; width:{{ min(100, max(2, $porcentaje)) }}%; height:8px;"></div>
                </div>
            </div>

            {{-- CTA --}}
            <div style="text-align:center; margin:30px 0 10px;">
                <a href="{{ url(route('admin.caja-chica.fondo.edit', [], false)) }}"
                   style="display:inline-block; background:#0606F0; color:white; text-decoration:none; padding:12px 28px; border-radius:8px; font-size:14px; font-weight:bold;">
                    Reponer fondo ahora →
                </a>
            </div>

            <p style="font-size:12px; color:#888; margin-top:30px; line-height:1.6;">
                Recuerda que cualquier movimiento sobre el fondo requiere validación con tu
                contraseña de administrador y queda registrado en el historial de auditoría.
                Este es el {{ $diasParaFinMes <= 0 ? 'último' : ($diasParaFinMes === 1 ? 'penúltimo' : 'aviso anticipado') }} antes del cierre de mes.
            </p>
        </div>

        {{-- Footer --}}
        <div style="background:#f4f4f4; padding:14px; text-align:center; font-size:11px; color:#888;">
            Este es un correo automático generado por el sistema UDEA.<br>
            No respondas a esta dirección.
        </div>
    </div>
</body>
</html>
