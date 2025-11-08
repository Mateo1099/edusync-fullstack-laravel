<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Crea la tabla de tutores/responsables (Guardian), relacionada con la tabla users.
     * Permite registrar cualquier responsable legal o familiar de un estudiante.
     */
    public function up(): void
    {
        Schema::create('guardians', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // Relación con tabla users
            $table->string('parentesco')->nullable(); // Parentesco con el estudiante (padre, madre, abuelo, etc.)
            $table->string('telefono')->nullable(); // Teléfono opcional
            $table->text('direccion')->nullable(); // Dirección opcional
            $table->timestamps();

            // Clave foránea para asegurar integridad referencial
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     * Elimina la tabla de tutores si existe.
     */
    public function down(): void
    {
        Schema::dropIfExists('guardians');
    }
};
