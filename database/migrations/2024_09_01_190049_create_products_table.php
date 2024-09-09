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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->text('descripcion');
            $table->text('descripcion_corta');
            $table->unsignedBigInteger('productcategory_id')->nullable();
            $table->unsignedBigInteger('brand_id')->nullable();
            $table->string('estado_producto');
            $table->decimal('precio');
            $table->decimal('precio_rebajado')->nullable();
            $table->string('sku')->nullable();
            $table->string('isbn_ean')->nullable();
            $table->integer('inventario')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
