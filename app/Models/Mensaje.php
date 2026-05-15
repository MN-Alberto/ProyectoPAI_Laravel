<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model; // nos permite crear modelos
use Illuminate\Database\Eloquent\Relations\BelongsTo; // nos permite crear relaciones uno a muchos

class Mensaje extends Model
{
    protected $table = 'mensajes'; // le indicamos a laravel que la tabla es mensajes, sino no la encuenta, busca mensajess
    protected $fillable = ['idConversacion', 'rol', 'contenido']; // campos que laravel puede rellenar automaticamente al crear un mensaje

    // Definimos la relacion entre mensaje y conversacion
    public function conversacion(): BelongsTo
    {
        return $this->belongsTo(Conversacion::class, 'idConversacion');
    }
}
