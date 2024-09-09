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
        Schema::create('images', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('alt')->nullable();
            $table->text('descripcion')->nullable();
            $table->string('leyenda')->nullable();
            $table->string('ruta_archivo')->unique(); // REVISAR ESTO
            $table->unsignedBigInteger('imageable_id')->nullable(); // Para la relación polimórfica con Productcategory y productos, se relaciona con la id de éstos
            $table->string('imageable_type')->nullable(); // Contiene la tabla con la que se relaciona: Productcategory o productos. Relación polimórfica 1:Muchos.
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('images');
    }
};
