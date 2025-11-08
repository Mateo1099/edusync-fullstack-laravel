<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Crea la tabla de estudiantes, relacionada con la tabla users.
     * Cada estudiante tiene un usuario asociado y datos específicos.
     */
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // Relación con tabla users
            $table->string('matricula')->unique(); // Matrícula escolar única
            $table->string('grupo')->nullable(); // Grupo o clase
            $table->date('fecha_nacimiento')->nullable(); // Fecha de nacimiento
            $table->string('telefono')->nullable(); // Teléfono opcional
            $table->text('direccion')->nullable(); // Dirección opcional
            $table->timestamps();

            // Clave foránea para asegurar integridad referencial
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     * Elimina la tabla de estudiantes si existe.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
