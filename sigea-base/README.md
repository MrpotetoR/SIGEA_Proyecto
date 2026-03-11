# SIGEA — Guía de Instalación Rápida

## 1. Crear el proyecto

```bash
composer create-project laravel/laravel sigea "11.*"
cd sigea
```

## 2. Instalar dependencias

```bash
composer require spatie/laravel-permission
composer require laravel/sanctum
composer require barryvdh/laravel-dompdf

php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
```

## 3. Configurar .env

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sigea
DB_USERNAME=root
DB_PASSWORD=tu_password
```

```bash
# Crear la BD en MySQL
mysql -u root -p -e "CREATE DATABASE sigea CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

## 4. Copiar archivos del proyecto

Copia los archivos generados en estas ubicaciones:

```
Tu proyecto sigea/
│
├── bootstrap/
│   └── app.php                          ← bootstrap_app.php
│
├── app/
│   ├── Models/
│   │   ├── User.php                     ← models/User.php (REEMPLAZA el existente)
│   │   ├── Alumno.php                   ← models/Alumno.php
│   │   ├── Docente.php                  ← models/Docente.php
│   │   ├── Carrera.php                  ← models/Carrera.php
│   │   ├── CicloEscolar.php            ← models/CicloEscolar.php
│   │   ├── Tutor.php                    ← ⚡ Extraer de Tutor_Materia_Grupo_Inscripcion.php
│   │   ├── Materia.php                  ← ⚡ Extraer de Tutor_Materia_Grupo_Inscripcion.php
│   │   ├── Grupo.php                    ← ⚡ Extraer de Tutor_Materia_Grupo_Inscripcion.php
│   │   ├── Inscripcion.php              ← ⚡ Extraer de Tutor_Materia_Grupo_Inscripcion.php
│   │   ├── Horario.php                  ← ⚡ Extraer de Horario_Calificacion_Asistencia_Semaforo.php
│   │   ├── Calificacion.php             ← ⚡ Extraer de Horario_Calificacion_Asistencia_Semaforo.php
│   │   ├── Asistencia.php               ← ⚡ Extraer de Horario_Calificacion_Asistencia_Semaforo.php
│   │   ├── SemaforoAcademico.php        ← ⚡ Extraer de Horario_Calificacion_Asistencia_Semaforo.php
│   │   ├── HrsCulturalDeportiva.php     ← ⚡ Extraer de Remaining_Models.php
│   │   ├── ServicioSocial.php           ← ⚡ Extraer de Remaining_Models.php
│   │   ├── EvaluacionDocente.php        ← ⚡ Extraer de Remaining_Models.php
│   │   ├── EncuestaPregunta.php         ← ⚡ Extraer de Remaining_Models.php
│   │   ├── EncuestaRespuesta.php        ← ⚡ Extraer de Remaining_Models.php
│   │   ├── Noticia.php                  ← ⚡ Extraer de Remaining_Models.php
│   │   ├── Constancia.php               ← ⚡ Extraer de Remaining_Models.php
│   │   ├── HistorialBaja.php            ← ⚡ Extraer de Remaining_Models.php
│   │   └── ChatbotSesion.php            ← ⚡ Extraer de Remaining_Models.php
│   │
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/
│   │   │   │   └── LoginController.php  ← controllers/Auth/LoginController.php
│   │   │   ├── Admin/                   ← (Tú creas estos)
│   │   │   ├── Alumno/                  ← (Equipo crea estos)
│   │   │   ├── Docente/                 ← (Equipo crea estos)
│   │   │   ├── Director/               ← (Equipo crea estos)
│   │   │   └── Api/                     ← (Endpoints SOA)
│   │   │
│   │   └── Middleware/
│   │       ├── CheckRole.php            ← middleware/CheckRole.php
│   │       └── RedirectByRole.php       ← middleware/RedirectByRole.php
│   │
│   └── Services/                        ← (Se crean después)
│
├── database/
│   ├── migrations/
│   │   └── (copia todas las migraciones)← migrations/*.php
│   └── seeders/
│       ├── DatabaseSeeder.php           ← seeders/DatabaseSeeder.php (REEMPLAZA)
│       ├── RolesAndPermissionsSeeder.php← seeders/RolesAndPermissionsSeeder.php
│       └── TestDataSeeder.php           ← seeders/TestDataSeeder.php
│
├── resources/views/
│   ├── layouts/
│   │   └── app.blade.php               ← views/layouts/app.blade.php
│   ├── auth/
│   │   └── login.blade.php             ← views/auth/login.blade.php
│   └── partials/
│       └── (sidebars)                   ← views/partials/sidebars.blade.php (separar)
│
└── routes/
    ├── web.php                          ← routes/web.php (REEMPLAZA)
    └── api.php                          ← routes/api.php (REEMPLAZA)
```

> **⚡ NOTA:** Los archivos marcados con ⚡ están agrupados en archivos combinados.
> Sepáralos en archivos individuales — cada clase en su propio `.php`.

## 5. Correr migraciones y seeders

```bash
php artisan migrate --seed
```

## 6. Compilar assets

```bash
npm install
npm run dev
```

## 7. Levantar el servidor

```bash
php artisan serve
```

Accede a `http://localhost:8000` y prueba con:
- **Admin:** admin@sigea.uttecam.edu.mx / password
- **Docente:** carlos1@sigea.uttecam.edu.mx / password  
- **Alumno:** erickdaniel@alumnos.uttecam.edu.mx / password

## 8. ¿Qué sigue?

### Tu parte (Login + Servicios Escolares):
1. ✅ Login ya está listo — personaliza la vista
2. Crea `Admin/DashboardController` con estadísticas generales
3. Crea CRUDs en `Admin/` para: Alumnos, Docentes, Carreras, Materias, Ciclos, Grupos, Noticias, Constancias, Bajas
4. Para cada CRUD: Controller + vistas index/create/edit/show

### Equipo (Alumno, Docente, Director):
1. Crear los Controllers listados en `routes/web.php`
2. Crear las vistas Blade para cada página
3. Crear los Services en `app/Services/`
4. Crear los Controllers API en `app/Http/Controllers/Api/`

---

## Estructura de un CRUD típico (ejemplo: Alumnos en Admin)

```bash
# Generar controller
php artisan make:controller Admin/AlumnosController --resource

# Crear vistas
mkdir -p resources/views/admin/alumnos
touch resources/views/admin/alumnos/{index,create,edit,show}.blade.php
```

```php
// routes/web.php (dentro del grupo admin)
Route::resource('alumnos', \App\Http\Controllers\Admin\AlumnosController::class);
```

```php
// app/Http/Controllers/Admin/AlumnosController.php
class AlumnosController extends Controller
{
    public function index(Request $request)
    {
        $alumnos = Alumno::with(['carrera', 'user'])
            ->when($request->search, fn($q, $s) => $q->where('matricula', 'like', "%{$s}%")
                ->orWhere('nombre', 'like', "%{$s}%"))
            ->when($request->carrera, fn($q, $c) => $q->where('carrera_id', $c))
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->paginate(20);

        $carreras = Carrera::all();

        return view('admin.alumnos.index', compact('alumnos', 'carreras'));
    }

    // ... create, store, edit, update, destroy
}
```
