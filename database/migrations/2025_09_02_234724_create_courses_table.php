<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Crea la tabla de cursos, relacionada con profesores y administradores.
     * Permite registrar cursos y sus datos principales.
     */
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('nombre'); // Nombre del curso
            $table->text('descripcion')->nullable(); // Descripción del curso
            $table->unsignedBigInteger('created_by')->nullable(); // Usuario que creó el curso (profesor o admin)
            $table->date('fecha_inicio')->nullable(); // Fecha de inicio
            $table->date('fecha_fin')->nullable(); // Fecha de fin
            $table->string('codigo_curso')->unique(); // Código único del curso
            $table->integer('creditos')->default(3); // Créditos del curso
            $table->string('estado')->default('activo'); // Estado del curso
            $table->timestamps();

            // Clave foránea para asegurar integridad referencial
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     * Elimina la tabla de cursos si existe.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
