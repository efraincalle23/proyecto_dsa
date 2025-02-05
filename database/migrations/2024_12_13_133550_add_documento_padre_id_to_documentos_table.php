<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('documentos', function (Blueprint $table) {
            $table->unsignedBigInteger('documento_padre_id')->nullable()->after('id_documento');
            $table->foreign('documento_padre_id')->references('id_documento')->on('documentos')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('documentos', function (Blueprint $table) {
            $table->dropForeign(['documento_padre_id']);
            $table->dropColumn('documento_padre_id');
        });
    }

};