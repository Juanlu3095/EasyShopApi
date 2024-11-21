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
        Schema::create('paymentmethods', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('slug'); // para no usar la id y mostrar la id
            $table->text('descripcion'); // descripción el panel de administración
            $table->text('descripcion_cliente'); // descripción en el checkout para el cliente
            $table->smallInteger('activo');
            $table->json('configuracion'); // Guardará la configuración de la cuenta bancaria que reciba el importe en formato JSON
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paymentmethods');
    }
};
