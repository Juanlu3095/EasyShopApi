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
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->string('puesto');
            $table->unsignedBigInteger('jobcategory_id');
            $table->unsignedBigInteger('province_id');
            $table->string('jornada');
            $table->string('nivel_profesional');
            $table->string('modalidad');
            $table->text('descripcion');
            $table->text('requisitos');
            $table->text('beneficios');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jobs');
    }
};
