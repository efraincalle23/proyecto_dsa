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
        Schema::table('historicos', function (Blueprint $table) {
            $table->foreignId('destinatario')->nullable()->constrained('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('historicos', function (Blueprint $table) {
            $table->dropForeign(['destinatario']);
            $table->dropColumn('destinatario');
        });
    }
};