<?php

use Illuminate\Database\Migrations\Migration; // nos permite crear migraciones
use Illuminate\Database\Schema\Blueprint; // nos permite crear tablas
use Illuminate\Support\Facades\Schema; // nos permite crear tablas

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('mensajes', function (Blueprint $table) {
            $table->id(); // id autogenerado
            $table->foreignId('idConversacion')->constrained('conversaciones')->cascadeOnDelete(); // fk del id de la conversación a la que pertenece el mensaje (relacion muchos a uno, un usuario puede tener muchas conversaciones y cada conversacion solo un usuario)
            $table->enum('rol', ['usuario', 'ia']); // rol del usuario que envía el mensaje, puede ser usuario o asistente (El usuario es la persona que envia el mensaje y el asistente es la respuesta de la ia)
            $table->longText('contenido'); // contenido del mensaje que envia el usuario a la ia o la respuesta de la ia al usuario
            $table->timestamps(); // fecha de creación y actualización para el mensaje
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mensajes'); // eliminamos la tabla mensajes si existe
    }
};
