<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentosEmitidosTable extends Migration
{
    /**
     * Ejecuta la migración.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('documentos_emitidos', function (Blueprint $table) {
            $table->id();
            $table->string('numero_oficio'); // Número de oficio único
            $table->string('nombre_doc'); // Campo para el nombre del documento
            $table->text('asunto');
            $table->date('fecha_recibido');
            $table->string('tipo');
            $table->string('destino'); // Persona receptor
            $table->text('observaciones')->nullable();
            $table->foreignId('entidad_id')->constrained('entidades')->onDelete('cascade'); // Relación con la entidad receptora
            $table->foreignId('respuesta_a')->nullable()->constrained('documentos_recibidos')->onDelete('set null'); // Respuesta a otro documento recibido, puede ser NULL
            $table->boolean('eliminado')->default(false); // Eliminación lógica
            $table->foreignId('respondido_con')->nullable()->constrained('documentos_recibidos')->onDelete('set null'); // Relación con documentos recibidos
            $table->enum('formato_documento', ['virtual', 'fisico'])->default('virtual'); // Indica si el documento es virtual o físico
            $table->timestamps(); // timestamps (created_at, updated_at)
        });
    }

    /**
     * Revierte la migración.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('documentos_emitidos');
    }
}