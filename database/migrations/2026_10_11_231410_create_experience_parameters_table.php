<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('experience_parameters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('experience_id')->constrained()->cascadeOnDelete();
            $table->string('block_name'); // ej: "Hatch 1", "Corte", "Grabado"
            $table->string('name');       // ej: "Velocidad", "Potencia", "Pasadas", "DPI"
            $table->string('value');      // ej: "3000", "80", "2", "300"
            $table->string('unit')->nullable(); // ej: "mm/min", "%", "unidad", "dpi"
            $table->timestamps();

            $table->index(['experience_id', 'block_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('experience_parameters');
    }
};