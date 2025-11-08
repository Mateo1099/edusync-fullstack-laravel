<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            if (!Schema::hasColumn('courses', 'code')) {
                $table->string('code')->unique();
            }
            if (!Schema::hasColumn('courses', 'credits')) {
                $table->integer('credits')->default(0);
            }
            if (!Schema::hasColumn('courses', 'start_date')) {
                $table->date('start_date')->nullable();
            }
            if (!Schema::hasColumn('courses', 'end_date')) {
                $table->date('end_date')->nullable();
            }
            if (!Schema::hasColumn('courses', 'status')) {
                $table->enum('status', ['active', 'inactive', 'draft'])->default('draft');
            }
            if (!Schema::hasColumn('courses', 'capacity')) {
                $table->integer('capacity')->default(30);
            }
            // Asegurarnos de que teacher_id sea una clave foránea
            if (!Schema::hasColumn('courses', 'teacher_id')) {
                $table->foreignId('teacher_id')->constrained('users');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            //
        });
    }
};
