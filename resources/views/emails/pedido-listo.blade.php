<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Pedido listo para recoger</title>
</head>
<body style="font-family: Arial, sans-serif; background:#f4f4f4; padding:20px; margin:0;">
    <div style="max-width:600px; margin:auto; background:white; border-radius:10px; overflow:hidden; box-shadow:0 2px 8px rgba(0,0,0,0.06);">

        <div style="background:#0606F0; color:white; padding:24px; text-align:center;">
            <h1 style="margin:0; font-size:22px;">¡Tu pedido está listo!</h1>
            <p style="margin:6px 0 0; font-size:14px; opacity:0.9;">Universidad de Los Ángeles · UDEA</p>
        </div>

        <div style="padding:30px;">
            <p style="font-size:15px; margin-top:0;">
                Hola <strong>{{ $nombre }}</strong>,
            </p>

            <p style="font-size:14px; color:#444;">
                Te informamos que tu pedido <strong style="color:#0606F0;">{{ $pedido->folio }}</strong>
                ya fue preparado y se encuentra disponible para que lo recojas
                en las oficinas de Gestor Escolar de UDEA.
            </p>

            <div style="background:#f8f9fa; padding:18px; border-radius:8px; margin:24px 0; border-left:4px solid #0606F0;">
                <p style="margin:0 0 12px; font-size:11px; text-transform:uppercase; color:#888; font-weight:bold; letter-spacing:0.05em;">
                    Detalles del pedido
                </p>

                <table style="width:100%; font-size:13px; color:#333;">
                    <tr>
                        <td style="padding:4px 0; color:#666;">Folio:</td>
                        <td style="padding:4px 0; font-weight:bold; font-family:monospace;">{{ $pedido->folio }}</td>
                    </tr>
                    <tr>
                        <td style="padding:4px 0; color:#666;">Producto(s):</td>
                        <td style="padding:4px 0;">
                            @foreach($pedido->items as $item)
                                <div>• {{ $item->descripcion }} <span style="color:#888;">×{{ $item->cantidad }}</span></div>
                            @endforeach
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:4px 0; color:#666;">Total pagado:</td>
                        <td style="padding:4px 0; font-weight:bold; color:#0606F0;">${{ number_format($pedido->total, 2) }} MXN</td>
                    </tr>
                    <tr>
                        <td style="padding:4px 0; color:#666;">Estado actual:</td>
                        <td style="padding:4px 0;">
                            <span style="background:#dcfce7; color:#166534; padding:3px 10px; border-radius:999px; font-size:11px; font-weight:bold;">
                                Listo para recoger
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:4px 0; color:#666;">Disponible desde:</td>
                        <td style="padding:4px 0;">{{ $pedido->fecha_listo_recoger?->format('d/m/Y H:i') }}</td>
                    </tr>
                </table>
            </div>

            <div style="background:#fef3c7; padding:18px; border-radius:8px; margin:24px 0;">
                <p style="margin:0 0 8px; font-size:13px; font-weight:bold; color:#92400e;">
                    📍 ¿Dónde recogerlo?
                </p>
                <p style="margin:0 0 6px; font-size:13px; color:#92400e;">
                    {{ $ubicacion }}
                </p>
                <p style="margin:0; font-size:12px; color:#92400e;">
                    <strong>Horario:</strong> {{ $horario }}
                </p>
            </div>

            <p style="font-size:13px; color:#666; margin-top:24px;">
                Por favor lleva una identificación oficial (credencial UDEA o INE) al momento de pasar por tu pedido.
                Menciona el folio <strong>{{ $pedido->folio }}</strong> al personal de Gestor Escolar.
            </p>

            <p style="font-size:13px; color:#666; margin-top:20px;">
                Saludos,<br>
                <strong>Equipo de Gestor Escolar — UDEA</strong>
            </p>
        </div>

        <div style="background:#f4f4f4; padding:15px; text-align:center; font-size:11px; color:#888;">
            Este es un correo automático del sistema SIGEA. Por favor no respondas a esta dirección.
            <br>
            Universidad de Los Ángeles · &copy; {{ date('Y') }}
        </div>
    </div>
</body>
</html>
