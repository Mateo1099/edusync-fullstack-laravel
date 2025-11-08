<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Crea la tabla de administradores, relacionada con la tabla users.
     * Cada administrador tiene un usuario asociado y datos específicos.
     */
    public function up(): void
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // Relación con tabla users
            $table->string('nivel_acceso')->nullable(); // Nivel de acceso o permisos
            $table->timestamps();

            // Clave foránea para asegurar integridad referencial
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     * Elimina la tabla de administradores si existe.
     */
    public function down(): void
    {
        Schema::dropIfExists('admins');
    }
};
