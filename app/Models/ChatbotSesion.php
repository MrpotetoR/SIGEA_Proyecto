<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatbotSesion extends Model
{
    protected $table = 'chatbot_sesion';
    protected $primaryKey = 'id_sesion';
    public $timestamps = false;

    protected $fillable = ['user_id', 'fecha_hora', 'pregunta', 'respuesta'];

    protected function casts(): array
    {
        return ['fecha_hora' => 'datetime'];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
