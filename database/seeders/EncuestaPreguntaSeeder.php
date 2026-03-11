<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EncuestaPregunta;

class EncuestaPreguntaSeeder extends Seeder
{
    public function run(): void
    {
        $preguntas = [
            ['texto_pregunta' => 'El docente domina los temas de la materia.', 'orden' => 1],
            ['texto_pregunta' => 'El docente explica con claridad los contenidos.', 'orden' => 2],
            ['texto_pregunta' => 'El docente llega puntual y cumple con el horario.', 'orden' => 3],
            ['texto_pregunta' => 'El docente fomenta la participación de los alumnos.', 'orden' => 4],
            ['texto_pregunta' => 'El docente proporciona retroalimentación oportuna.', 'orden' => 5],
            ['texto_pregunta' => 'El docente utiliza materiales y recursos adecuados.', 'orden' => 6],
            ['texto_pregunta' => 'El docente muestra respeto hacia los estudiantes.', 'orden' => 7],
        ];

        foreach ($preguntas as $pregunta) {
            EncuestaPregunta::firstOrCreate(
                ['orden' => $pregunta['orden']],
                array_merge($pregunta, ['activa' => true])
            );
        }
    }
}
