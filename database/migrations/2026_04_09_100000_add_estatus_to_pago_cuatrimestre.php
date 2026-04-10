<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('pago_cuatrimestre', function (Blueprint $table) {
            $table->enum('estatus', ['pendiente', 'aprobado', 'rechazado'])->default('pendiente')->after('baucher_path');
            $table->string('comentario_rechazo', 500)->nullable()->after('estatus');
            $table->foreignId('revisado_por')->nullable()->after('comentario_rechazo')
                  ->constrained('users')->nullOnDelete();
            $table->timestamp('revisado_en')->nullable()->after('revisado_por');
            $table->foreignId('subido_por')->nullable()->after('revisado_en')
                  ->constrained('users')->nullOnDelete();
        });

        // Los pagos existentes se marcan como aprobados (fueron subidos por servicios)
        DB::table('pago_cuatrimestre')->whereNull('subido_por')->update(['estatus' => 'aprobado']);
    }
    public function down(): void
    {
        Schema::table('pago_cuatrimestre', function (Blueprint $table) {
            $table->dropForeign(['revisado_por']);
            $table->dropForeign(['subido_por']);
            $table->dropColumn(['estatus', 'comentario_rechazo', 'revisado_por', 'revisado_en', 'subido_por']);
        });
    }
};
