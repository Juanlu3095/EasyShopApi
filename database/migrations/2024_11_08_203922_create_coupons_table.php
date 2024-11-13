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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('codigo');
            $table->string('tipo');
            $table->integer('descuento');
            $table->string('descripcion')->nullable();
            $table->string('estado_cupon');
            $table->date('fecha_caducidad')->nullable();
            $table->decimal('gasto_minimo')->nullable();
            $table->integer('limite_uso')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
