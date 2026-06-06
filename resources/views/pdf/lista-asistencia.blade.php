<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #1a1a1a; padding: 32px; }

        .membrete { text-align: center; border-bottom: 3px solid #04276B; padding-bottom: 12px; margin-bottom: 20px; }
        .membrete h1 { font-size: 16px; font-weight: bold; color: #04276B; letter-spacing: 1px; }
        .membrete p  { font-size: 10px; color: #555; margin-top: 3px; }

        .titulo { text-align: center; font-size: 14px; font-weight: bold;
                  text-transform: uppercase; letter-spacing: 2px;
                  margin: 16px 0; color: #0606F0; }

        .datos { background: #f0f4ff; border: 1px solid #c7d2fe; border-radius: 6px;
                 padding: 10px 14px; margin-bottom: 16px; }
        .datos table { width: 100%; }
        .datos td { padding: 3px 8px; font-size: 11px; }
        .datos .lbl { font-weight: bold; color: #04276B; width: 100px; }

        table.lista { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        table.lista th {
            background: #04276B; color: #fff; font-size: 10px;
            text-transform: uppercase; padding: 6px 4px; text-align: center;
            border: 1px solid #04276B;
        }
        table.lista td {
            padding: 6px 6px; border: 1px solid #cbd5e1;
            font-size: 11px; text-align: center; height: 26px;
        }
        table.lista td.nombre { text-align: left; font-weight: 500; }
        table.lista tr:nth-child(even) td { background: #f8fafc; }

        .col-num     { width: 28px; }
        .col-id      { width: 75px; }
        .col-nombre  { width: auto; }
        .col-asist   { width: 36px; }

        .firma { margin-top: 40px; display: table; width: 100%; }
        .firma .cel { display: table-cell; text-align: center; width: 50%; padding: 0 20px; }
        .firma .linea { width: 200px; border-top: 1px solid #333; margin: 0 auto 6px; padding-top: 30px; }
        .firma p { font-size: 10px; color: #444; }

        .pie { position: fixed; bottom: 20px; left: 32px; right: 32px;
               text-align: center; font-size: 9px; color: #94a3b8;
               border-top: 1px solid #e5e7eb; padding-top: 6px; }
    </style>
</head>
<body>

    <div class="membrete">
        <h1>UNIVERSIDAD DE LOS ÁNGELES</h1>
        <p>Sistema Integral de Gestión Académica — UDEA</p>
    </div>

    <div class="titulo">Lista de Asistencia</div>

    <div class="datos">
        <table>
            <tr>
                <td class="lbl">Grupo:</td>
                <td>{{ $grupo->clave_grupo }}</td>
                <td class="lbl">Cuatrimestre:</td>
                <td>{{ $grupo->cuatrimestre }}°</td>
            </tr>
            <tr>
                <td class="lbl">Materia:</td>
                <td>{{ $materia->nombre_materia ?? '—' }}</td>
                <td class="lbl">Carrera:</td>
                <td>{{ $grupo->carrera?->nombre_carrera ?? '—' }}</td>
            </tr>
            <tr>
                <td class="lbl">Mes / Año:</td>
                <td>{{ ucfirst(now()->locale('es')->translatedFormat('F Y')) }}</td>
                <td class="lbl">Total alumnos:</td>
                <td>{{ $alumnos->count() }}</td>
            </tr>
        </table>
    </div>

    <table class="lista">
        <thead>
            <tr>
                <th class="col-num">#</th>
                <th class="col-id">ID</th>
                <th class="col-nombre">Alumno</th>
                @for ($i = 1; $i <= 10; $i++)
                    <th class="col-asist">{{ $i }}</th>
                @endfor
            </tr>
        </thead>
        <tbody>
            @foreach ($alumnos as $i => $alumno)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $alumno->id_alumno_publico }}</td>
                    <td class="nombre">{{ strtoupper($alumno->apellidos . ', ' . $alumno->nombre) }}</td>
                    @for ($j = 1; $j <= 10; $j++)
                        <td></td>
                    @endfor
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="firma">
        <div class="cel">
            <div class="linea"></div>
            <p>Firma del Docente</p>
        </div>
        <div class="cel">
            <div class="linea"></div>
            <p>Sello / Vo. Bo.</p>
        </div>
    </div>

    <div class="pie">
        UDEA — Lista generada el {{ now()->locale('es')->translatedFormat('d \\d\\e F \\d\\e Y, H:i') }}
    </div>

</body>
</html>
