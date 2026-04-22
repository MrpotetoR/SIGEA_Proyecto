<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('servicio_social', function (Blueprint $table) {
            if (!Schema::hasColumn('servicio_social', 'institucion')) {
                $table->string('institucion', 150)->nullable()->after('id_alumno');
            }
        });
    }

    public function down(): void
    {
        Schema::table('servicio_social', function (Blueprint $table) {
            if (Schema::hasColumn('servicio_social', 'institucion')) {
                $table->dropColumn('institucion');
            }
        });
    }
};
