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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('apellidos');
            $table->string('pais');
            $table->string('direccion');
            $table->integer('codigo_postal');
            $table->string('poblacion');
            $table->string('provincia');
            $table->integer('telefono');
            $table->string('email');
            $table->text('notas')->nullable();
            $table->unsignedBigInteger('paymentmethods_id')->constrained(table: 'paymentmethods', indexname: 'id');
            $table->decimal('subtotal');
            $table->unsignedBigInteger('orderstatus_id')->constrained('orderstatuses')->default(2); // Para indicar si se ha pagado y/o se ha enviado.
            $table->string('nombre_descuento')->nullable(); // Guardamos el nombre del descuento por si se borra el cupón, para que al menos quede registrado
            $table->decimal('descuento')->nullable();
            $table->decimal('total');
            $table->unsignedBigInteger('user_id')->constrained('users')->nullable()->default(null); // Si el usuario no está registrado, no podrá ver su pedido en la web
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
