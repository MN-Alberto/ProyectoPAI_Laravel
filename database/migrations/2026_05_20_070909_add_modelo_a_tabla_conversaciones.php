<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        //Añade la columna modelo a la tabla conversaciones con valor por defecto mistral y después de tituloConversacion
        Schema::table('conversaciones', function (Blueprint $table) {
            $table->string('modelo')->default('mistral')->after('tituloConversacion');
        });
    }
    public function down(): void
    {
        //Elimina la columna modelo de la tabla conversaciones
        Schema::table('conversaciones', function (Blueprint $table) {
            $table->dropColumn('modelo');
        });
    }
};
