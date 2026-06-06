<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Codigo de recuperacion - UDEA</title>
</head>
<body style="margin:0; padding:0; background-color:#f3f4f6; font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color:#f3f4f6; padding:32px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="max-width:520px; background-color:#ffffff; border-radius:16px; overflow:hidden; box-shadow:0 4px 24px rgba(15, 32, 96, 0.08);">

                    {{-- Header con gradiente --}}
                    <tr>
                        <td style="background: linear-gradient(135deg, #0f2b7a 0%, #1a4fc7 50%, #2563eb 100%); padding:32px 32px 28px;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                <tr>
                                    <td>
                                        <div style="display:inline-block; width:42px; height:42px; background:rgba(255,255,255,0.18); border:1px solid rgba(255,255,255,0.28); border-radius:12px; line-height:42px; text-align:center; color:#ffffff; font-weight:800; font-size:16px;">
                                            SG
                                        </div>
                                    </td>
                                    <td align="right" style="color:rgba(255,255,255,0.85); font-size:12px; font-weight:600; letter-spacing:0.08em; text-transform:uppercase;">
                                        UDEA
                                    </td>
                                </tr>
                            </table>
                            <h1 style="margin:20px 0 6px; color:#ffffff; font-size:22px; font-weight:800; letter-spacing:-0.01em;">
                                Recupera tu contrasena
                            </h1>
                            <p style="margin:0; color:rgba(255,255,255,0.8); font-size:13px;">
                                Sistema de Gestion Academica
                            </p>
                        </td>
                    </tr>

                    {{-- Cuerpo --}}
                    <tr>
                        <td style="padding:32px 32px 16px;">
                            <p style="margin:0 0 12px; color:#111827; font-size:15px; font-weight:600;">
                                Hola {{ $nombreUsuario }},
                            </p>
                            <p style="margin:0 0 24px; color:#4b5563; font-size:14px; line-height:1.6;">
                                Recibimos una solicitud para restablecer la contrasena de tu cuenta en UDEA.
                                Usa el siguiente codigo de verificacion para continuar:
                            </p>

                            {{-- Codigo --}}
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin:0 0 24px;">
                                <tr>
                                    <td align="center" style="background:#f8faff; border:1.5px dashed #3B6CF6; border-radius:14px; padding:22px 16px;">
                                        <p style="margin:0 0 8px; color:#6b7280; font-size:11px; font-weight:600; letter-spacing:0.18em; text-transform:uppercase;">
                                            Tu codigo de verificacion
                                        </p>
                                        <p style="margin:0; color:#0f2b7a; font-size:36px; font-weight:800; letter-spacing:0.35em; font-family: 'Courier New', Courier, monospace;">
                                            {{ $codigo }}
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:0 0 16px; color:#4b5563; font-size:13.5px; line-height:1.6;">
                                Ingresa este codigo en la pagina de verificacion para crear una nueva contrasena.
                                El codigo expira en <strong style="color:#0f2b7a;">{{ $minutosExpiracion }} minutos</strong>.
                            </p>

                            <div style="background:#fffbeb; border-left:4px solid #f59e0b; padding:12px 14px; border-radius:6px; margin:20px 0 4px;">
                                <p style="margin:0; color:#92400e; font-size:12.5px; line-height:1.5;">
                                    <strong>Si no solicitaste este cambio</strong>, ignora este correo.
                                    Tu contrasena actual seguira funcionando con normalidad.
                                </p>
                            </div>
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="padding:20px 32px 28px; border-top:1px solid #f1f5f9;">
                            <p style="margin:0 0 6px; color:#9ca3af; font-size:11.5px; line-height:1.5;">
                                Este correo fue enviado automaticamente, por favor no respondas a esta direccion.
                            </p>
                            <p style="margin:0; color:#9ca3af; font-size:11.5px;">
                                &copy; {{ date('Y') }} UDEA - Sistema de Gestion Academica.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
