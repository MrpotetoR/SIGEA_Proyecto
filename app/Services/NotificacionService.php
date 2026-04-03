<?php

namespace App\Services;

use App\Models\Notificacion;
use App\Models\User;

class NotificacionService
{
    /**
     * Enviar notificación a un usuario específico.
     */
    public function enviar(User $user, string $tipo, string $titulo, string $mensaje, array $opciones = []): Notificacion
    {
        return Notificacion::create([
            'user_id' => $user->id,
            'tipo'    => $tipo,
            'titulo'  => $titulo,
            'mensaje' => $mensaje,
            'icono'   => $opciones['icono'] ?? 'bell',
            'color'   => $opciones['color'] ?? 'blue',
            'url'     => $opciones['url'] ?? null,
        ]);
    }

    /**
     * Enviar notificación a múltiples usuarios.
     */
    public function enviarMasivo(iterable $users, string $tipo, string $titulo, string $mensaje, array $opciones = []): int
    {
        $count = 0;
        $now = now();
        $registros = [];

        foreach ($users as $user) {
            $registros[] = [
                'user_id'    => $user->id,
                'tipo'       => $tipo,
                'titulo'     => $titulo,
                'mensaje'    => $mensaje,
                'icono'      => $opciones['icono'] ?? 'bell',
                'color'      => $opciones['color'] ?? 'blue',
                'url'        => $opciones['url'] ?? null,
                'created_at' => $now,
                'updated_at' => $now,
            ];
            $count++;
        }

        // Insertar en lotes de 500
        foreach (array_chunk($registros, 500) as $chunk) {
            Notificacion::insert($chunk);
        }

        return $count;
    }

    /**
     * Notificar nueva noticia a usuarios activos.
     * Si se pasan roles, solo se notifica a usuarios con esos roles.
     */
    public function notificarNuevaNoticia(string $titulo, ?string $url = null, ?array $roles = null): int
    {
        $query = User::where('activo', true);

        if (!empty($roles)) {
            $query->role($roles);
        }

        $users = $query->get();

        return $this->enviarMasivo($users, 'noticia', 'Nueva noticia publicada', $titulo, [
            'icono' => 'newspaper',
            'color' => 'blue',
            'url'   => $url,
        ]);
    }

    /**
     * Notificar calificación registrada al alumno.
     */
    public function notificarCalificacion(int $userId, string $materia, int $parcial, float $calificacion, ?string $url = null): Notificacion
    {
        $color = $calificacion >= 7 ? 'green' : 'red';

        return $this->enviar(
            User::find($userId),
            'calificacion',
            'Calificación registrada',
            "Se registró tu calificación del Parcial {$parcial} en {$materia}: {$calificacion}",
            [
                'icono' => 'academic-cap',
                'color' => $color,
                'url'   => $url,
            ]
        );
    }
}
