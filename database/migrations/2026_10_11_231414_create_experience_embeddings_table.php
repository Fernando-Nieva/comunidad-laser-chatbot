<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('experience_embeddings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('experience_id')->unique()->constrained()->cascadeOnDelete();
            $table->json('embedding');
            $table->text('source_text');
            $table->string('model');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('experience_embeddings');
    }
};