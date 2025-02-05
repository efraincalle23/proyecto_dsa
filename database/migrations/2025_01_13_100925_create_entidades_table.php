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
        Schema::create('entidades', function (Blueprint $table) {
            $table->id();                            // ID único
            $table->string('nombre');                // Nombre de la entidad
            $table->string('siglas', 20);            // Siglas o abreviatura
            $table->string('tipo'); // Tipo de la entidad
            $table->foreignId('entidad_superior_id') // Referencia jerárquica
                ->nullable()
                ->constrained('entidades')
                ->nullOnDelete();
            $table->boolean('eliminado')->default(false); // Eliminación lógica
            $table->timestamps();                    // Timestamps
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entidades');
    }
};