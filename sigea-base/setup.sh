#!/bin/bash
# ============================================
# SIGEA — Script de instalación inicial
# ============================================

echo "🚀 Creando proyecto Laravel 11..."
composer create-project laravel/laravel sigea "11.*"
cd sigea

echo "📦 Instalando paquetes..."
composer require spatie/laravel-permission
composer require laravel/sanctum
composer require barryvdh/laravel-dompdf
composer require maatwebsite/excel

echo "🎨 Instalando frontend..."
npm install
npm install -D tailwindcss postcss autoprefixer
npx tailwindcss init -p

echo "⚙️  Publicando configuraciones..."
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"

echo "🗄️  Configurando .env..."
# Editar manualmente: DB_DATABASE=sigea, DB_USERNAME=root, DB_PASSWORD=...

echo "✅ Proyecto creado. Ahora:"
echo "1. Configura tu .env con los datos de MySQL"
echo "2. Copia las migraciones a database/migrations/"
echo "3. Copia los modelos a app/Models/"
echo "4. Copia los seeders a database/seeders/"
echo "5. Ejecuta: php artisan migrate --seed"
