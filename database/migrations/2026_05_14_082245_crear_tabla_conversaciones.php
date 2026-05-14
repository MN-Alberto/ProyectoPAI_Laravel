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
        // Creamos la tabla conversaciones con los campos id, idUsuario, tituloConversacion y timestamps
        Schema::create('conversaciones', function (Blueprint $table) {
            $table->id(); // id autogenerado
            $table->foreignId('idUsuario')->constrained()->cascadeOnDelete(); // id del usuario que crea la conversación
            $table->string('tituloConversacion')->default('Nueva conversación'); // titulo de la conversación, por defecto será "Nueva conversación"
            $table->timestamps(); // fecha de creación y actualización para la conversación
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversaciones'); // eliminamos la tabla conversaciones si existe
    }
};
