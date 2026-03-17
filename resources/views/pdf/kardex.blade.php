<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #1a1a1a; padding: 36px; }

        .membrete { text-align: center; border-bottom: 3px solid #3730a3; padding-bottom: 14px; margin-bottom: 24px; }
        .membrete h1 { font-size: 15px; font-weight: bold; color: #3730a3; letter-spacing: 1px; }
        .membrete p  { font-size: 10px; color: #555; margin-top: 3px; }

        .titulo { text-align: center; font-size: 14px; font-weight: bold;
                  text-transform: uppercase; letter-spacing: 2px;
                  margin: 20px 0; color: #1e1b4b; }

        .datos-alumno { background: #f0f4ff; border: 1px solid #c7d2fe; border-radius: 6px;
                        padding: 12px 16px; margin-bottom: 24px; }
        .datos-alumno table { width: 100%; }
        .datos-alumno td { padding: 3px 8px; font-size: 11px; }
        .datos-alumno .lbl { font-weight: bold; color: #3730a3; width: 120px; }

        .ciclo-header { background: #312e81; color: #fff; padding: 6px 12px;
                        font-size: 11px; font-weight: bold; text-transform: uppercase;
                        letter-spacing: 1px; margin-bottom: 0; border-radius: 4px 4px 0 0; }

        table.calificaciones { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table.calificaciones th {
            background: #e0e7ff; color: #3730a3; font-size: 10px;
            text-transform: uppercase; padding: 6px 8px; text-align: center;
            border: 1px solid #c7d2fe;
        }
        table.calificaciones th:first-child { text-align: left; }
        table.calificaciones td {
            padding: 5px 8px; border: 1px solid #e5e7eb;
            font-size: 11px; text-align: center;
        }
        table.calificaciones td:first-child { text-align: left; }
        table.calificaciones tr:nth-child(even) td { background: #f9fafb; }
        .reprobado { color: #dc2626; font-weight: bold; }
        .aprobado  { color: #16a34a; font-weight: bold; }

        .resumen { background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 6px;
                   padding: 10px 16px; margin-top: 10px; text-align: right; }
        .resumen .prom { font-size: 16px; font-weight: bold; color: #15803d; }

        .firma { margin-top: 60px; text-align: center; }
        .firma .linea { width: 220px; border-top: 1px solid #333; margin: 0 auto 6px; }
        .firma p { font-size: 10px; color: #444; }

        .pie { margin-top: 40px; text-align: center; font-size: 9px; color: #aaa;
               border-top: 1px solid #eee; padding-top: 8px; }
    </style>
</head>
<body>

    <div class="membrete">
        <h1>INSTITUTO TECNOLÓGICO</h1>
        <p>Sistema Integral de Gestión Escolar y Académica — SIGEA</p>
    </div>

    <div class="titulo">Kárdex Académico</div>

    <div class="datos-alumno">
        <table>
            <tr>
                <td class="lbl">Nombre:</td>
                <td>{{ strtoupper($alumno->nombre . ' ' . $alumno->apellidos) }}</td>
                <td class="lbl">Matrícula:</td>
                <td>{{ $alumno->matricula }}</td>
            </tr>
            <tr>
                <td class="lbl">Carrera:</td>
                <td>{{ $alumno->carrera?->nombre_carrera ?? '—' }}</td>
                <td class="lbl">Cuatrimestre:</td>
                <td>{{ $alumno->cuatrimestre_actual }}°</td>
            </tr>
            <tr>
                <td class="lbl">Estatus:</td>
                <td>{{ ucwords(str_replace('_', ' ', $alumno->estatus)) }}</td>
                <td class="lbl">Fecha de emisión:</td>
                <td>{{ now()->format('d/m/Y') }}</td>
            </tr>
        </table>
    </div>

    @forelse($historial as $cicloNombre => $calificaciones)
        <div class="ciclo-header">{{ $cicloNombre }}</div>
        <table class="calificaciones">
            <thead>
                <tr>
                    <th style="width:40%">Materia</th>
                    <th>Parcial 1</th>
                    <th>Parcial 2</th>
                    <th>Parcial 3</th>
                    <th>Promedio</th>
                </tr>
            </thead>
            <tbody>
                @foreach($calificaciones->groupBy('id_materia') as $parciales)
                    @php
                        $materia = $parciales->first()->materia;
                        $p1      = $parciales->where('parcial', 1)->first()?->calificacion;
                        $p2      = $parciales->where('parcial', 2)->first()?->calificacion;
                        $p3      = $parciales->where('parcial', 3)->first()?->calificacion;
                        $prom    = round($parciales->avg('calificacion'), 2);
                    @endphp
                    <tr>
                        <td>{{ $materia?->nombre_materia ?? '—' }}</td>
                        <td class="{{ ($p1 !== null && $p1 < 7) ? 'reprobado' : '' }}">{{ $p1 ?? '—' }}</td>
                        <td class="{{ ($p2 !== null && $p2 < 7) ? 'reprobado' : '' }}">{{ $p2 ?? '—' }}</td>
                        <td class="{{ ($p3 !== null && $p3 < 7) ? 'reprobado' : '' }}">{{ $p3 ?? '—' }}</td>
                        <td class="{{ $prom >= 7 ? 'aprobado' : 'reprobado' }}">{{ $prom }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @empty
        <p style="text-align:center; color:#999; margin:30px 0;">Sin historial académico registrado.</p>
    @endforelse

    <div class="resumen">
        Promedio General Acumulado:
        <span class="prom">{{ $promedio }}</span>
    </div>

    <div class="firma">
        <div class="linea"></div>
        <p><strong>Control Escolar — SIGEA</strong></p>
        <p>Instituto Tecnológico</p>
    </div>

    <div class="pie">
        Documento generado electrónicamente por SIGEA el {{ now()->format('d/m/Y H:i') }}.
        Válido sin firma autógrafa con sello institucional.
    </div>

</body>
</html>
