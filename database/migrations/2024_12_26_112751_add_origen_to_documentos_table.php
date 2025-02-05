<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOrigenToDocumentosTable extends Migration
{
    public function up()
    {
        Schema::table('documentos', function (Blueprint $table) {
            // Agregar el campo 'origen'
            $table->string('origen')->nullable();  // Puedes cambiar 'string' por el tipo que necesites (e.g., 'enum', 'boolean')
        });
    }

    public function down()
    {
        Schema::table('documentos', function (Blueprint $table) {
            // Eliminar el campo 'origen' si es necesario revertir la migración
            $table->dropColumn('origen');
        });
    }
}