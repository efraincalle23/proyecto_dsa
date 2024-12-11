<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('documentos', function (Blueprint $table) {
            $table->id('id_documento'); // Definir el ID como PK
            $table->string('numero_oficio')->unique();
            $table->date('fecha_recepcion');
            $table->string('remitente', 50);
            $table->string('tipo', 20);
            $table->string('descripcion', 200);
            $table->string('observaciones', 200)->nullable();
            $table->string('archivo')->nullable(); // Columna para el archivo
            $table->timestamps(); // Para las fechas de creación y actualización
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documentos');
    }
};