<?php
// =============================================
// NOTA: Estos son 4 modelos en un solo archivo
// para referencia. En tu proyecto, sepáralos
// en archivos individuales dentro de app/Models/
// =============================================

// ─────────────────────────────────────────────
// app/Models/Tutor.php
// ─────────────────────────────────────────────

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tutor extends Model
{
    use HasFactory;

    protected $table = 'tutores';

    protected $fillable = ['nombre', 'telefono', 'email', 'direccion'];

    public function alumnos()
    {
        return $this->hasMany(Alumno::class);
    }
}

// ─────────────────────────────────────────────
// app/Models/Materia.php
// ─────────────────────────────────────────────

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Materia extends Model
{
    use HasFactory;

    protected $fillable = ['carrera_id', 'nombre_materia', 'cuatrimestre', 'horas_semana', 'creditos'];

    public function carrera()
    {
        return $this->belongsTo(Carrera::class);
    }

    public function horarios()
    {
        return $this->hasMany(Horario::class);
    }

    public function calificaciones()
    {
        return $this->hasMany(Calificacion::class);
    }
}

// ─────────────────────────────────────────────
// app/Models/Grupo.php
// ─────────────────────────────────────────────

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grupo extends Model
{
    use HasFactory;

    protected $fillable = ['carrera_id', 'ciclo_id', 'cuatrimestre', 'clave_grupo', 'tutor_docente_id'];

    public function carrera()
    {
        return $this->belongsTo(Carrera::class);
    }

    public function ciclo()
    {
        return $this->belongsTo(CicloEscolar::class, 'ciclo_id');
    }

    public function tutorDocente()
    {
        return $this->belongsTo(Docente::class, 'tutor_docente_id');
    }

    public function horarios()
    {
        return $this->hasMany(Horario::class);
    }

    public function inscripciones()
    {
        return $this->hasMany(Inscripcion::class);
    }

    /**
     * Alumnos inscritos en este grupo.
     */
    public function alumnos()
    {
        return $this->hasManyThrough(Alumno::class, Inscripcion::class, 'grupo_id', 'id', 'id', 'alumno_id');
    }
}

// ─────────────────────────────────────────────
// app/Models/Inscripcion.php
// ─────────────────────────────────────────────

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inscripcion extends Model
{
    use HasFactory;

    protected $table = 'inscripciones';

    protected $fillable = ['alumno_id', 'grupo_id', 'fecha_inscripcion'];

    protected $casts = [
        'fecha_inscripcion' => 'date',
    ];

    public function alumno()
    {
        return $this->belongsTo(Alumno::class);
    }

    public function grupo()
    {
        return $this->belongsTo(Grupo::class);
    }
}
