<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte Caja General — UDEA</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 10px; color: #1a1a1a; padding: 24px 30px; }

        .membrete { text-align: center; border-bottom: 3px solid #3730a3; padding-bottom: 10px; margin-bottom: 16px; }
        .membrete h1 { font-size: 14px; font-weight: bold; color: #3730a3; letter-spacing: 1px; }
        .membrete p  { font-size: 9px; color: #555; margin-top: 2px; }

        .titulo-doc {
            text-align: center; font-size: 13px; font-weight: bold;
            text-transform: uppercase; letter-spacing: 2px;
            margin: 14px 0 4px; color: #1e1b4b;
        }
        .subtitulo { text-align: center; font-size: 9px; color: #666; margin-bottom: 14px; }

        .resumen-row { display: table; width: 100%; margin-bottom: 14px; }
        .resumen-card {
            display: table-cell; padding: 10px;
            background: #f0f4ff; border: 1px solid #c7d2fe;
            border-radius: 5px; width: 25%;
            vertical-align: top;
        }
        .resumen-card + .resumen-card { margin-left: 8px; }
        .resumen-card .label { font-size: 8px; color: #3730a3; text-transform: uppercase; font-weight: bold; }
        .resumen-card .valor { font-size: 14px; font-weight: bold; color: #1e1b4b; margin-top: 2px; }
        .resumen-card .sub   { font-size: 9px; color: #666; margin-top: 1px; }

        .info-rango {
            background: #f9fafb; padding: 8px 12px;
            border-left: 3px solid #3730a3; margin-bottom: 12px;
            font-size: 9px; color: #444;
        }

        table.ingresos { width: 100%; border-collapse: collapse; margin-top: 4px; }
        table.ingresos th {
            background: #312e81; color: #fff; font-size: 9px;
            text-transform: uppercase; padding: 6px 6px;
            text-align: left;
        }
        table.ingresos td {
            padding: 5px 6px; border: 1px solid #e5e7eb;
            font-size: 9px;
        }
        table.ingresos tr:nth-child(even) td { background: #f9fafb; }
        .monto { font-family: 'DejaVu Sans Mono', monospace; text-align: right; font-weight: bold; }
        .badge {
            display: inline-block; font-size: 8px; font-weight: bold;
            padding: 2px 6px; border-radius: 3px; text-transform: uppercase;
        }
        .b-colegiatura { background: #dbeafe; color: #1e40af; }
        .b-producto    { background: #f3e8ff; color: #6b21a8; }
        .b-tramite     { background: #fef3c7; color: #92400e; }
        .b-otro        { background: #f3f4f6; color: #4b5563; }

        .pie {
            margin-top: 16px; padding-top: 8px;
            border-top: 1px solid #e5e7eb;
            font-size: 8px; color: #888; text-align: center;
        }
    </style>
</head>
<body>

    <div class="membrete">
        <h1>UDEA — UNIVERSIDAD DE LOS ÁNGELES</h1>
        <p>Sistema Integral de Gestión Escolar y Académica</p>
    </div>

    <div class="titulo-doc">Reporte de Caja General</div>
    <div class="subtitulo">Ingresos institucionales · Generado el {{ now()->format('d/m/Y H:i') }}</div>

    {{-- Información del rango filtrado --}}
    <div class="info-rango">
        <strong>Periodo:</strong> {{ \Carbon\Carbon::parse($filtros['desde'])->translatedFormat('d \d\e F \d\e Y') }}
        — {{ \Carbon\Carbon::parse($filtros['hasta'])->translatedFormat('d \d\e F \d\e Y') }}
        @if($filtros['tipo'])
            · <strong>Tipo:</strong> {{ \App\Models\IngresoCajaGeneral::TIPOS[$filtros['tipo']]['label'] ?? $filtros['tipo'] }}
        @endif
        @if($filtros['buscar'])
            · <strong>Búsqueda:</strong> "{{ $filtros['buscar'] }}"
        @endif
    </div>

    {{-- Tarjetas de resumen --}}
    <div class="resumen-row">
        <div class="resumen-card">
            <div class="label">Total del periodo</div>
            <div class="valor">$ {{ number_format($resumen['total'], 2) }}</div>
            <div class="sub">{{ $resumen['conteo'] }} ingresos</div>
        </div>
        <div class="resumen-card" style="background:#eff6ff;">
            <div class="label">Colegiaturas</div>
            <div class="valor">$ {{ number_format($resumen['por_tipo']['colegiatura'], 2) }}</div>
        </div>
        <div class="resumen-card" style="background:#faf5ff;">
            <div class="label">Productos tienda</div>
            <div class="valor">$ {{ number_format($resumen['por_tipo']['producto'], 2) }}</div>
        </div>
        <div class="resumen-card" style="background:#fffbeb;">
            <div class="label">Trámites + Otros</div>
            <div class="valor">$ {{ number_format($resumen['por_tipo']['tramite'] + $resumen['por_tipo']['otro'], 2) }}</div>
        </div>
    </div>

    {{-- Tabla de ingresos --}}
    @if($ingresos->isEmpty())
        <p style="text-align:center; color:#888; padding:20px;">No hay ingresos en este periodo.</p>
    @else
        <table class="ingresos">
            <thead>
                <tr>
                    <th style="width:13%">Folio</th>
                    <th style="width:11%">Fecha</th>
                    <th style="width:10%">Tipo</th>
                    <th style="width:30%">Concepto</th>
                    <th style="width:18%">Alumno</th>
                    <th style="width:8%; text-align:right;">Monto</th>
                    <th style="width:10%">Por</th>
                </tr>
            </thead>
            <tbody>
                @foreach($ingresos as $i)
                    <tr>
                        <td style="font-family: 'DejaVu Sans Mono', monospace; font-weight:bold; color:#3730a3;">{{ $i->folio }}</td>
                        <td>{{ $i->fecha_cobro?->format('d/m/Y H:i') }}</td>
                        <td>
                            <span class="badge b-{{ $i->tipo }}">{{ $i->tipo_label }}</span>
                        </td>
                        <td>{{ $i->concepto }}</td>
                        <td>{{ $i->alumno?->nombre_completo ?? '—' }}</td>
                        <td class="monto">$ {{ number_format((float) $i->monto, 2) }}</td>
                        <td>{{ $i->usuario?->name ?? '—' }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="5" style="text-align:right; font-weight:bold; background:#312e81; color:#fff; padding:8px;">
                        TOTAL
                    </td>
                    <td class="monto" style="background:#312e81; color:#fff; font-size:11px;">
                        $ {{ number_format($resumen['total'], 2) }}
                    </td>
                    <td style="background:#312e81;"></td>
                </tr>
            </tbody>
        </table>
    @endif

    <div class="pie">
        Documento generado automáticamente · UDEA · Sistema Integral de Gestión Escolar<br>
        {{ now()->format('d/m/Y H:i:s') }}
    </div>

</body>
</html>
