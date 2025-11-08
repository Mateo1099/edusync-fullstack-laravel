<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Crea la tabla de profesores, relacionada con la tabla users.
     * Cada profesor tiene un usuario asociado y datos específicos.
     */
    public function up(): void
    {
        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // Relación con tabla users
            $table->string('especialidad')->nullable(); // Campo para especialidad del profesor
            $table->string('telefono')->nullable(); // Teléfono opcional
            $table->text('bio')->nullable(); // Biografía opcional
            $table->timestamps();

            // Clave foránea para asegurar integridad referencial
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     * Elimina la tabla de profesores si existe.
     */
    public function down(): void
    {
        Schema::dropIfExists('teachers');
    }
};
