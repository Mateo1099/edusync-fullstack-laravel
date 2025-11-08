<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Crea la tabla de roles para los usuarios del sistema.
     * Permite definir y asignar diferentes tipos de roles.
     */
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique(); // Nombre del rol (admin, profesor, estudiante, guardian, etc.)
            $table->string('descripcion')->nullable(); // Descripción opcional del rol
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     * Elimina la tabla de roles si existe.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
