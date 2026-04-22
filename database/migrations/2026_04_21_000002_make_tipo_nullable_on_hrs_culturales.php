<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Hacemos nullable la columna tipo para permitir registros sin
        // distincion cultural/deportiva (se elimino esa funcionalidad).
        DB::statement("ALTER TABLE hrs_culturales_deportivas MODIFY tipo ENUM('cultural','deportiva') NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE hrs_culturales_deportivas MODIFY tipo ENUM('cultural','deportiva') NOT NULL");
    }
};
