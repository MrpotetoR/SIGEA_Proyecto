<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 12px; color: #1a1a1a; padding: 40px; }

        .membrete { text-align: center; border-bottom: 3px solid #3730a3; padding-bottom: 16px; margin-bottom: 30px; }
        .membrete h1 { font-size: 16px; font-weight: bold; color: #3730a3; letter-spacing: 1px; }
        .membrete p { font-size: 10px; color: #555; margin-top: 4px; }

        .folio { text-align: right; font-size: 10px; color: #888; margin-bottom: 20px; }

        .titulo { text-align: center; font-size: 15px; font-weight: bold; text-transform: uppercase;
                  letter-spacing: 2px; margin: 30px 0; color: #1e1b4b; }

        .cuerpo { text-align: justify; line-height: 1.9; font-size: 12px; margin: 20px 0; }
        .cuerpo .dato { font-weight: bold; color: #1e1b4b; }

        .firma { margin-top: 70px; text-align: center; }
        .firma .linea { width: 220px; border-top: 1px solid #333; margin: 0 auto 6px; }
        .firma p { font-size: 11px; color: #333; }

        .pie { margin-top: 50px; text-align: center; font-size: 9px; color: #aaa;
               border-top: 1px solid #eee; padding-top: 10px; }

        .sello { float: right; margin-top: -60px; width: 80px; height: 80px;
                 border: 2px solid #3730a3; border-radius: 50%;
                 display: flex; align-items: center; justify-content: center; }
    </style>
</head>
<body>

    <div class="membrete">
        <h1>INSTITUTO TECNOLÓGICO</h1>
        <p>Sistema Integral de Gestión Escolar y Académica — SIGEA</p>
    </div>

    <div class="folio">
        Folio: CONST-{{ str_pad($alumno->id_alumno, 6, '0', STR_PAD_LEFT) }}-{{ date('Ymd') }}
        &nbsp;&nbsp;|&nbsp;&nbsp;
        Fecha: {{ now()->format('d \d\e F \d\e Y') }}
    </div>

    <div class="titulo">
        Constancia de
        @switch($tipo)
            @case('estudio') Estudios @break
            @case('calificaciones') Calificaciones @break
            @case('comportamiento') Buena Conducta @break
            @case('servicio_social') Servicio Social @break
            @case('cultural') Participación Cultural @break
            @default {{ ucfirst($tipo) }}
        @endswitch
    </div>

    <div class="cuerpo">
        <p>
            El que suscribe, Director de Servicios Escolares del Instituto Tecnológico,
            hace constar que el/la alumno/a:
        </p>
        <br>
        <p>
            <span class="dato">{{ strtoupper($alumno->nombre . ' ' . $alumno->apellidos) }}</span>,
            con matrícula <span class="dato">{{ $alumno->matricula }}</span>,
            se encuentra inscrito/a en la carrera de
            <span class="dato">{{ $alumno->carrera->nombre_carrera ?? 'No especificada' }}</span>,
            cursando actualmente el <span class="dato">{{ $alumno->cuatrimestre_actual }}° cuatrimestre</span>.
        </p>
        <br>

        @switch($tipo)
            @case('estudio')
                <p>
                    Se expide la presente constancia de <strong>estudios en curso</strong>
                    para los fines que el interesado estime convenientes, haciendo constar que
                    el alumno se encuentra activo en el presente ciclo escolar con estatus
                    <span class="dato">{{ strtoupper($alumno->estatus) }}</span>.
                </p>
            @break

            @case('calificaciones')
                <p>
                    Se expide la presente constancia de <strong>calificaciones</strong>
                    haciendo constar que el alumno ha cumplido con las evaluaciones
                    correspondientes al período académico en curso, conforme a los registros
                    del sistema institucional.
                </p>
            @break

            @case('comportamiento')
                <p>
                    Se expide la presente constancia de <strong>buena conducta</strong>
                    haciendo constar que el alumno no cuenta con reportes disciplinarios
                    en su expediente durante su trayectoria en esta institución.
                </p>
            @break

            @case('servicio_social')
                <p>
                    Se expide la presente constancia de <strong>servicio social</strong>
                    haciendo constar que el alumno ha cumplido con los requisitos establecidos
                    por la institución para la realización del servicio social obligatorio.
                </p>
            @break

            @case('cultural')
                <p>
                    Se expide la presente constancia de <strong>participación en actividades
                    culturales y deportivas</strong>, en reconocimiento a la participación
                    activa del alumno en las actividades extracurriculares institucionales.
                </p>
            @break
        @endswitch

        <br>
        <p>
            La presente se expide a petición del interesado, en la ciudad,
            a los <span class="dato">{{ now()->format('d') }}</span> días del mes de
            <span class="dato">{{ now()->format('F') }}</span> de
            <span class="dato">{{ now()->format('Y') }}</span>.
        </p>
    </div>

    <div class="firma">
        <div class="linea"></div>
        <p><strong>Jefatura de Servicios Escolares</strong></p>
        <p>Instituto Tecnológico — SIGEA</p>
    </div>

    <div class="pie">
        Documento generado electrónicamente por SIGEA. Válido sin firma autógrafa con sello institucional.
        Generado el {{ now()->format('d/m/Y H:i') }}
    </div>

</body>
</html>
