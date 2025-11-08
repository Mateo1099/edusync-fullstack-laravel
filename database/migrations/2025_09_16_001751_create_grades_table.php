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
        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('assignment_id')->constrained()->onDelete('cascade');
            $table->decimal('score', 5, 2);
            $table->text('feedback')->nullable();
            $table->string('status')->default('submitted'); // submitted, graded, revised
            $table->string('submission_path')->nullable();
            $table->timestamps();
            
            $table->unique(['student_id', 'assignment_id']);
            $table->index(['assignment_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};
