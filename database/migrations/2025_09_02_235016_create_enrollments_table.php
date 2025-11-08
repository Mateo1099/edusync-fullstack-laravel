<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Crea la tabla de inscripciones, relacionando estudiantes y cursos.
     * Permite registrar qué estudiante está inscrito en qué curso.
     */
    public function up(): void
    {
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id'); // Relación con tabla students
            $table->unsignedBigInteger('course_id'); // Relación con tabla courses
            $table->date('fecha_inscripcion')->nullable(); // Fecha de inscripción
            $table->string('estado')->default('inscrito'); // Estado de la inscripción
            $table->decimal('calificacion_final', 5, 2)->nullable(); // Calificación final opcional
            $table->timestamps();

            // Claves foráneas para asegurar integridad referencial
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     * Elimina la tabla de inscripciones si existe.
     */
    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};
