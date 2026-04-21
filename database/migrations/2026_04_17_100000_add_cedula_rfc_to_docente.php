<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('docente', function (Blueprint $table) {
            $table->string('num_cedula', 30)->nullable()->after('especialidad');
            $table->string('rfc', 20)->nullable()->after('num_cedula');
        });
    }

    public function down(): void
    {
        Schema::table('docente', function (Blueprint $table) {
            $table->dropColumn(['num_cedula', 'rfc']);
        });
    }
};
