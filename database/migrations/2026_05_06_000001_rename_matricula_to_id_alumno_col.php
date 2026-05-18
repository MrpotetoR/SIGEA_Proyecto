<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('alumno', function (Blueprint $table) {
            $table->renameColumn('matricula', 'id_alumno_publico');
        });
    }

    public function down(): void
    {
        Schema::table('alumno', function (Blueprint $table) {
            $table->renameColumn('id_alumno_publico', 'matricula');
        });
    }
};
