<?php

namespace App\OpenApi;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="UDEA REST API",
 *     description="API REST del Sistema Integral de Gestión Educativa Académica (UDEA). Autenticación mediante tokens Sanctum (header `Authorization: Bearer <token>`).",
 *
 *     @OA\Contact(name="Equipo UDEA", email="contacto@udea.local")
 * )
 *
 * @OA\Server(
 *     url="http://localhost:8000",
 *     description="Servidor local de desarrollo"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="Sanctum"
 * )
 *
 * @OA\Tag(name="Auth", description="Autenticación por tokens")
 * @OA\Tag(name="Perfil", description="Datos y contraseña del usuario autenticado")
 * @OA\Tag(name="Noticias", description="CRUD de noticias institucionales")
 * @OA\Tag(name="Alumnos", description="Consulta de alumnos")
 * @OA\Tag(name="Kardex", description="Historial académico del alumno autenticado")
 * @OA\Tag(name="Notificaciones", description="Notificaciones in-app del usuario")
 */
class OpenApiInfo {}
