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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->timestamp('start_date');
            $table->timestamp('end_date');
            $table->string('location')->nullable();
            $table->string('type'); // academic, social, sport, etc.
            $table->foreignId('creator_id')->constrained('users')->onDelete('cascade');
            $table->boolean('is_public')->default(true);
            $table->string('status')->default('scheduled'); // scheduled, cancelled, completed
            $table->timestamps();
            
            $table->index(['start_date', 'end_date']);
            $table->index(['type', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
