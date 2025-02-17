<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentosRecibidosTable extends Migration
{
    /**
     * Ejecuta la migración para crear la tabla documentos_recibidos.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('documentos_recibidos', function (Blueprint $table) {
            $table->id();  // Clave primaria
            $table->string('numero_oficio');  // Número de oficio
            $table->string('nombre_doc'); // Campo para el nombre del documento
            $table->string('asunto');  // Asunto del documento
            $table->date('fecha_recibido');  // Fecha en que el documento fue recibido
            $table->string('tipo');
            $table->string('remitente');  // Persona o entidad que firma el documento
            $table->string('observaciones', 200)->nullable();
            $table->foreignId('entidad_id')->constrained('entidades')->onDelete('cascade');  // Relación con la entidad remitente (entidad_id)
            $table->boolean('eliminado')->default(false);  // Eliminación lógica
            $table->enum('formato_documento', ['virtual', 'fisico'])->default('virtual'); // Indica si el documento es virtual o físico
            $table->timestamps();  // Campos created_at y updated_at
            // Restricción de unicidad combinada
            $table->unique(['numero_oficio', 'entidad_id'], 'unique_numero_oficio_entidad');
        });
    }



    /**
     * Revierte la migración para eliminar la tabla documentos_recibidos.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('documentos_recibidos');
    }
}