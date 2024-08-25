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
        Schema::create('cvs', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('apellidos');
            $table->integer('telefono');
            $table->string('email');
            $table->string('ruta_cv')->unique();
            $table->string('incorporacion');
            $table->string('pais');
            $table->string('ciudad');
            $table->string('politica');
            $table->string('estado_candidatura')->default('En proceso');
            $table->unsignedBigInteger('job_id'); // Relación 1:Muchos con la tabla Jobs
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cvs');
    }
};
