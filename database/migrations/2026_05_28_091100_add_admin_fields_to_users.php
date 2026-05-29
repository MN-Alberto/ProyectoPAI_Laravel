<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Campo para identificar al administrador
            $table->boolean('is_admin')->default(false)->after('password');
            // Campo JSON para los modelos permitidos (null = todos)
            $table->json('modelos_permitidos')->nullable()->after('is_admin');
            // Campo para dar de baja a un usuario (true = activo, false = dado de baja)
            $table->boolean('activo')->default(true)->after('modelos_permitidos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_admin', 'modelos_permitidos', 'activo']);
        });
    }
};
