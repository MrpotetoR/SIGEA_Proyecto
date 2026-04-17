# SIGEA — Sistema Integral de Gestión Educativa Académica

Plataforma web para la gestión académica de una institución educativa de nivel superior. Cubre las operaciones de cuatro perfiles —Alumno, Docente, Director de Carrera y Servicios Escolares— sobre una sola base de datos relacional, con panel diferenciado por rol, modo oscuro y API REST versionada.

## Stack

- **Backend:** Laravel 12, PHP 8.2
- **Auth:** Laravel Breeze (web) + Laravel Sanctum (API por tokens)
- **Roles/Permisos:** spatie/laravel-permission
- **Frontend:** Blade + Tailwind CSS 3 (`darkMode: 'class'`) + Alpine.js 3
- **PDF:** barryvdh/laravel-dompdf
- **Bundler:** Vite
- **Testing:** PHPUnit 11
- **Tooling:** Laravel Pint, Laravel Boost (MCP)

## Módulos por panel

### Alumno
- Dashboard, perfil, horario, mis docentes, kardex (con descarga PDF), historial académico
- Calificaciones por parcial, asistencia
- Horas culturales (ACUDE), servicio social
- Pagos por cuatrimestre (subir baucher PDF, ver estatus)
- Evaluación docente, noticias, chatbot

### Docente
- Dashboard, perfil, grupos, horario, tutorados
- Toma de asistencia y captura de calificaciones por grupo
- Reportes de asistencia y rendimiento
- CRUD de horas culturales y servicio social
- Resultados de evaluación docente, noticias

### Director de Carrera
- Dashboard, perfil, plan de estudios
- CRUD de grupos (con inscripción/desinscripción de alumnos) y horarios
- Listado de docentes y alumnos (con historial)
- Asistencia, índice de aprobación, evaluación docente, noticias

### Servicios Escolares
- Dashboard general
- CRUD de alumnos (con padre/tutor, pagos iniciales y documentos), docentes, directores, personal
- CRUD de carreras, materias, ciclos escolares
- Inscripciones, baucher (subir/aprobar/rechazar) con notificación al alumno
- Constancias on-the-fly (PDF sin almacenamiento en disco)
- Noticias con imagen (local máx. 512 KB o URL externa) y lightbox
- Documentos institucionales y reportes
- Filtro de alumnos con adeudo

## Características transversales

- **Modo oscuro** en los cuatro paneles, persistido en localStorage
- **Búsqueda con debounce** en todos los listados
- **Notificaciones in-app** con polling y badge de no leídas
- **Lightbox** para imágenes de noticias en todos los paneles
- **Códigos de estado** personalizados (404, 403, 500, 419)
- **Cambio de contraseña** disponible para todos los roles

## Arquitectura SOA

SIGEA sigue una arquitectura orientada a servicios (SOA), tanto **consumiendo** servicios externos como **exponiendo** los propios:

```
                ┌─────────────┐
                │  SIGEA Web  │  ← cliente principal (sesión)
                └──────┬──────┘
                       │
        ┌──────────────┴──────────────┐
        │                             │
        ▼ (consume)                   ▼ (expone)
  ┌──────────────┐               ┌────────────┐
  │  Servicios   │               │ SIGEA REST │
  │  externos    │               │  /api/v1   │
  ├──────────────┤               └────────────┘
  │ Email (SMTP) │ → recuperación
  │ Chatbot      │ → asistente IA
  └──────────────┘
```

## API REST

Versionada bajo `/api/v1/`, autenticada con Sanctum (token por header `Authorization: Bearer <token>`). 18 endpoints diseñados para demostrar los conceptos REST esenciales: autenticación por token, recursos, paginación, filtros, status codes y recursos anidados.

### Autenticación
| Método | Endpoint | Descripción |
|---|---|---|
| POST | `/api/v1/login` | Devuelve token y datos del usuario |
| POST | `/api/v1/logout` | Invalida el token actual |
| GET  | `/api/v1/me` | Usuario autenticado |

### Perfil
| Método | Endpoint | Descripción |
|---|---|---|
| GET   | `/api/v1/perfil` | Perfil + alumno/docente asociado |
| PATCH | `/api/v1/perfil` | Actualizar nombre/email |
| PUT   | `/api/v1/perfil/password` | Cambiar contraseña |

### Noticias (CRUD completo)
| Método | Endpoint | Descripción |
|---|---|---|
| GET    | `/api/v1/noticias` | Listar (paginado, filtro `desde`) |
| POST   | `/api/v1/noticias` | Crear |
| GET    | `/api/v1/noticias/{id}` | Detalle |
| PUT    | `/api/v1/noticias/{id}` | Actualizar |
| DELETE | `/api/v1/noticias/{id}` | Eliminar |

### Alumnos (solo lectura)
| Método | Endpoint | Descripción |
|---|---|---|
| GET | `/api/v1/alumnos` | Listar (filtros `buscar`, `estatus`, `carrera`) |
| GET | `/api/v1/alumnos/{id}` | Detalle con carrera y usuario |

### Kardex
| Método | Endpoint | Descripción |
|---|---|---|
| GET | `/api/v1/kardex` | Historial del alumno autenticado |
| GET | `/api/v1/kardex/pdf` | Descarga PDF |

### Notificaciones
| Método | Endpoint | Descripción |
|---|---|---|
| GET  | `/api/v1/notificaciones` | Listar (filtro `no_leidas`, meta `no_leidas` count) |
| POST | `/api/v1/notificaciones/{id}/leida` | Marcar leída |
| POST | `/api/v1/notificaciones/marcar-todas` | Marcar todas leídas |

Total: **18 rutas**.

## Instalación

```bash
git clone <repo>
cd sigea
composer install
npm install
cp .env.example .env
php artisan key:generate
```

Configurar la base de datos en `.env` y luego:

```bash
php artisan migrate --seed
php artisan storage:link
npm run dev          # o npm run build para producción
php artisan serve
```

La aplicación queda disponible en `http://localhost:8000`.

## Estructura

```
app/
  Http/
    Controllers/
      Alumno/        Docente/        Director/        Servicios/
      Api/           Auth/
    Resources/       (API Resources)
  Models/            (Alumno, Docente, Carrera, Materia, Grupo, ...)
  Services/          (KardexService, PDFService, NotificacionService, ...)
resources/
  views/
    alumno/  docente/  director/  servicios/
    pdf/     partials/
routes/
  web.php  api.php  auth.php  console.php
```

## Convenciones

- Modelos siguen el snake_case de la BD original (`id_alumno`, `clave_carrera`, etc.)
- Cada controlador de panel está aislado bajo su namespace correspondiente
- API expone IDs renombrados a `id` en los Resources para uniformidad
- Pint formatea automáticamente con `vendor/bin/pint --dirty --format agent`

## Testing

```bash
php artisan test --compact
```

## Licencia

Proyecto académico. Laravel framework licenciado bajo [MIT](https://opensource.org/licenses/MIT).
