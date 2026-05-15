<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model; // nos permite crear modelos
use Illuminate\Database\Eloquent\Relations\HasMany; // nos permite crear relaciones muchos a muchos
use Illuminate\Database\Eloquent\Relations\BelongsTo; // nos permite crear relaciones uno a muchos

class Conversacion extends Model
{
    protected $table = 'conversaciones'; // le indicamos a laravel que la tabla es conversaciones, sino no la encuenta, busca conversacions

    protected $fillable = ['idUsuario', 'titulo']; // campos que laravel puede rellenar automaticamente al crear una conversacion

    // Definimos la relacion entre conversacion y usuario
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'idUsuario');
    }

    // Definimos la relacion entre conversacion y mensajes
    public function mensajes(): HasMany
    {
        return $this->hasMany(Mensaje::class);
    }
}
