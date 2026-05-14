<?php

namespace App\Policies;

use App\Models\Conversacion;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ConversacionPolicy
{
    /**
     * Un usuario solo puede ver sus propias conversaciones
     */
    public function view(User $user, Conversacion $conversacion): bool
    {
        // Solo el usuario logueado puede ver la conversacion
        return $user->id === $conversacion->idUsuario;
    }

    /**
     * Un usuario solo puede eliminar sus propias conversaciones
     */
    public function delete(User $user, Conversacion $conversacion): bool
    {
        return $user->id === $conversacion->idUsuario;
    }
}
