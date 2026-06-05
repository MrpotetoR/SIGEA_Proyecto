<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('carpeta_documento', function (Blueprint $table) {
            $table->id('id_carpeta');
            $table->string('nombre', 120);
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->enum('visibilidad', ['publica', 'privada'])->default('publica');
            $table->foreignId('user_id')->constrained('users')->onUpdate('cascade')->onDelete('restrict');
            $table->timestamps();

            $table->foreign('parent_id')
                ->references('id_carpeta')->on('carpeta_documento')
                ->onUpdate('cascade')->onDelete('restrict');

            $table->index(['parent_id', 'visibilidad']);
        });

        Schema::table('documento_institucional', function (Blueprint $table) {
            $table->unsignedBigInteger('carpeta_id')->nullable()->after('user_id');
            $table->foreign('carpeta_id')
                ->references('id_carpeta')->on('carpeta_documento')
                ->onUpdate('cascade')->onDelete('restrict');
            $table->index('carpeta_id');
        });
    }

    public function down(): void
    {
        Schema::table('documento_institucional', function (Blueprint $table) {
            $table->dropForeign(['carpeta_id']);
            $table->dropIndex(['carpeta_id']);
            $table->dropColumn('carpeta_id');
        });

        Schema::dropIfExists('carpeta_documento');
    }
};
