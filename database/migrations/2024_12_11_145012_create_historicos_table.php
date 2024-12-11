<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('historicos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_documento'); // Asegúrate de que sea unsignedBigInteger
            $table->unsignedBigInteger('id_usuario'); // Lo mismo aquí
            $table->string('estado_anterior', 255)->default('recibido');
            $table->string('estado_nuevo', 255)->nullable();
            $table->timestamp('fecha_cambio')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->string('observaciones', 255)->nullable();
            $table->timestamps();

            // Claves foráneas
            $table->foreign('id_documento')->references('id_documento')->on('documentos')->onDelete('cascade');
            $table->foreign('id_usuario')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historicos');
    }
};