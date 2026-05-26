<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Vale {{ $vale->folio }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #1a1a1a; padding: 32px 40px; }

        .membrete { text-align: center; border-bottom: 3px solid #3730a3; padding-bottom: 14px; margin-bottom: 22px; }
        .membrete h1 { font-size: 16px; font-weight: bold; color: #3730a3; letter-spacing: 1px; }
        .membrete p  { font-size: 10px; color: #555; margin-top: 3px; }

        .titulo-doc {
            text-align: center; font-size: 14px; font-weight: bold;
            text-transform: uppercase; letter-spacing: 2px;
            margin: 18px 0 6px; color: #1e1b4b;
        }
        .subtitulo { text-align: center; font-size: 10px; color: #666; margin-bottom: 18px; }

        /* Header con folio + fecha */
        .folio-box {
            background: #312e81; color: #fff;
            padding: 10px 14px; border-radius: 6px;
            display: table; width: 100%;
            margin-bottom: 20px;
        }
        .folio-box .col { display: table-cell; vertical-align: middle; }
        .folio-box .col-right { text-align: right; }
        .folio-box .folio-num { font-size: 18px; font-weight: bold; font-family: 'DejaVu Sans Mono', monospace; }
        .folio-box .label-sm { font-size: 9px; text-transform: uppercase; letter-spacing: 1px; opacity: .8; }

        /* Tarjeta de datos */
        .datos {
            border: 1px solid #c7d2fe; border-radius: 6px;
            padding: 12px 16px; margin-bottom: 18px;
            background: #f8faff;
        }
        .datos table { width: 100%; }
        .datos td { padding: 5px 8px; font-size: 11px; vertical-align: top; }
        .datos .lbl { font-weight: bold; color: #3730a3; width: 30%; }

        /* Monto destacado */
        .monto-box {
            background: #f0fdf4; border: 2px solid #16a34a; border-radius: 8px;
            padding: 14px 18px; margin-bottom: 18px; text-align: center;
        }
        .monto-box .label { font-size: 10px; color: #15803d; text-transform: uppercase; letter-spacing: 1px; }
        .monto-box .cifra { font-size: 22px; font-weight: bold; color: #15803d; margin: 4px 0; font-family: 'DejaVu Sans Mono', monospace; }
        .monto-box .letras { font-size: 10px; color: #166534; font-style: italic; text-transform: uppercase; }

        /* Concepto */
        .concepto {
            border-left: 3px solid #3730a3; padding: 8px 14px;
            background: #eef2ff; margin-bottom: 18px;
        }
        .concepto .lbl { font-size: 9px; color: #3730a3; text-transform: uppercase; letter-spacing: 1px; font-weight: bold; }
        .concepto p { font-size: 12px; color: #1e1b4b; margin-top: 2px; }

        /* Badge de estado */
        .estado {
            display: inline-block;
            font-size: 10px; font-weight: bold;
            padding: 4px 10px; border-radius: 4px;
            text-transform: uppercase; letter-spacing: 1px;
        }
        .estado-autorizada { background: #dbeafe; color: #1e40af; }
        .estado-comprobada { background: #dcfce7; color: #166534; }
        .estado-cancelada  { background: #f1f5f9; color: #475569; }
        .estado-rechazada  { background: #fee2e2; color: #991b1b; }
        .estado-solicitada { background: #f3f4f6; color: #4b5563; }

        /* Firmas */
        .firmas { margin-top: 50px; }
        .firmas table { width: 100%; }
        .firmas td { width: 50%; padding: 0 20px; text-align: center; vertical-align: top; }
        .firmas .linea { border-top: 1px solid #333; margin: 0 auto 6px; padding-top: 36px; }
        .firmas .rol { font-size: 9px; color: #666; text-transform: uppercase; letter-spacing: 1px; }
        .firmas .nombre { font-size: 11px; color: #1e1b4b; font-weight: bold; margin-top: 3px; }
        .firmas .meta { font-size: 9px; color: #888; margin-top: 2px; }

        /* Pie de auditoría */
        .pie {
            margin-top: 30px; padding-top: 10px;
            border-top: 1px solid #e5e7eb;
            font-size: 9px; color: #888;
        }
        .pie table { width: 100%; }
        .pie td { padding: 2px 0; }
        .pie .lbl { color: #555; font-weight: bold; }

        .nota-cancelado {
            background: #fef2f2; border: 1px solid #fecaca;
            padding: 8px 14px; border-radius: 6px;
            color: #991b1b; font-size: 10px;
            margin-bottom: 18px; text-align: center;
        }
    </style>
</head>
<body>

    {{-- Membrete --}}
    <div class="membrete">
        <h1>UDEA — UNIVERSIDAD DE LOS ÁNGELES</h1>
        <p>Sistema Integral de Gestión Escolar y Académica</p>
    </div>

    {{-- Título del documento --}}
    <div class="titulo-doc">Vale de Salida — Caja Chica</div>
    <div class="subtitulo">Fondo de Emergencia · Comprobante institucional</div>

    {{-- Folio + fecha --}}
    <div class="folio-box">
        <div class="col">
            <div class="label-sm">Folio</div>
            <div class="folio-num">{{ $vale->folio }}</div>
        </div>
        <div class="col col-right">
            <div class="label-sm">Fecha de emisión</div>
            <div style="font-size:14px; font-weight:bold;">{{ $vale->created_at->translatedFormat('d \d\e F \d\e Y') }}</div>
            <div style="font-size:10px; opacity:.85;">{{ $vale->created_at->format('H:i') }} hrs</div>
        </div>
    </div>

    {{-- Aviso si está cancelado/rechazado --}}
    @if($vale->estatus === 'cancelada')
        <div class="nota-cancelado">
            <strong>ESTE VALE FUE CANCELADO</strong>
            @if($vale->cancelado_en)
                · {{ $vale->cancelado_en->format('d/m/Y H:i') }}
            @endif
        </div>
    @elseif($vale->estatus === 'rechazada')
        <div class="nota-cancelado">
            <strong>ESTE VALE FUE RECHAZADO</strong>
            @if($vale->motivo_rechazo)
                <div style="margin-top:4px; font-style:italic;">{{ $vale->motivo_rechazo }}</div>
            @endif
        </div>
    @endif

    {{-- Datos principales --}}
    <div class="datos">
        <table>
            <tr>
                <td class="lbl">Solicitante:</td>
                <td>{{ strtoupper($vale->solicitante_nombre) }}</td>
            </tr>
            <tr>
                <td class="lbl">Estatus:</td>
                <td>
                    <span class="estado estado-{{ $vale->estatus }}">{{ $vale->estado_label }}</span>
                </td>
            </tr>
            <tr>
                <td class="lbl">Solicitado por:</td>
                <td>
                    {{ $vale->solicitante?->name ?? '—' }}
                    <span style="font-size:9px;color:#888;">· {{ $vale->created_at->format('d/m/Y H:i') }}</span>
                </td>
            </tr>
            @if($vale->autorizado_en)
                <tr>
                    <td class="lbl">{{ $vale->estatus === 'rechazada' ? 'Rechazado por:' : 'Autorizado por:' }}</td>
                    <td>
                        {{ $vale->autorizador?->name ?? '—' }}
                        <span style="font-size:9px;color:#888;">· {{ $vale->autorizado_en->format('d/m/Y H:i') }}</span>
                    </td>
                </tr>
            @endif
            @if($vale->tiene_factura)
                <tr>
                    <td class="lbl">Factura comprobada:</td>
                    <td>
                        <span style="color:#15803d; font-weight:bold;">Sí</span>
                        <span style="font-size:9px;color:#888;">
                            · {{ $vale->facturaSubidaPor?->name ?? '—' }}
                            · {{ $vale->factura_subida_en?->format('d/m/Y H:i') }}
                        </span>
                    </td>
                </tr>
            @endif
        </table>
    </div>

    {{-- Concepto --}}
    <div class="concepto">
        <div class="lbl">Concepto del gasto</div>
        <p>{{ $vale->concepto }}</p>
    </div>

    {{-- Monto destacado --}}
    <div class="monto-box">
        <div class="label">Monto del vale</div>
        <div class="cifra">$ {{ number_format((float) $vale->monto, 2) }} MXN</div>
        <div class="letras">{{ $montoEnLetras }}</div>
    </div>

    {{-- Firmas --}}
    @if(in_array($vale->estatus, ['autorizada', 'comprobada']))
        <div class="firmas">
            <table>
                <tr>
                    <td>
                        <div class="linea"></div>
                        <div class="rol">Recibí conforme</div>
                        <div class="nombre">{{ strtoupper($vale->solicitante_nombre) }}</div>
                        <div class="meta">Solicitante</div>
                    </td>
                    <td>
                        <div class="linea"></div>
                        <div class="rol">Autorizó</div>
                        <div class="nombre">{{ strtoupper($vale->autorizador?->name ?? '—') }}</div>
                        <div class="meta">Caja Chica · UDEA</div>
                    </td>
                </tr>
            </table>
        </div>
    @endif

    {{-- Pie de auditoría --}}
    <div class="pie">
        <table>
            <tr>
                <td class="lbl">Documento generado:</td>
                <td>{{ now()->format('d/m/Y H:i:s') }}</td>
                <td class="lbl" style="text-align:right;">Sistema:</td>
                <td style="text-align:right;">UDEA · Módulo Caja Chica</td>
            </tr>
            <tr>
                <td colspan="4" style="padding-top:6px; text-align:center; font-size:8px; color:#999;">
                    Este documento es un comprobante institucional. Su autenticidad se verifica por el folio {{ $vale->folio }}
                    en el sistema interno UDEA.
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
