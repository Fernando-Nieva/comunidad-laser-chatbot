<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('experiences', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->foreignId('material_id')->constrained()->restrictOnDelete();
            $table->foreignId('job_type_id')->constrained()->restrictOnDelete();
            $table->decimal('thickness_mm', 8, 2)->nullable();
            $table->string('machine');
            $table->integer('machine_power_w')->nullable();
            $table->string('software');
            $table->text('description');
            $table->text('results');
            $table->text('observations')->nullable();
            $table->enum('status', ['approved', 'pending', 'rejected'])->default('approved');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('experiences');
    }
};