<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
        'modelos_permitidos',
        'activo',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'modelos_permitidos' => 'array',
            'is_admin' => 'boolean',
            'activo' => 'boolean',
        ];
    }

    /**
     * Verifica si el usuario es administrador
     * Comprueba el campo is_admin O el email hardcodeado
     */
    public function isAdmin(): bool
    {
        return $this->is_admin || $this->email === 'admin@gmail.com';
    }

    /**
     * Verifica si el usuario puede usar un modelo específico
     * Si modelos_permitidos es null, tiene acceso a todos
     */
    public function puedeUsarModelo(string $modelo): bool
    {
        if ($this->modelos_permitidos === null) {
            return true;
        }
        return in_array($modelo, $this->modelos_permitidos);
    }

    /**
     * Verifica si el usuario está activo
     */
    public function estaActivo(): bool
    {
        return $this->activo;
    }

    /**
     * Enviar la notificación de restablecimiento de contraseña.
     * Sobreescribe el metodo de la clase Authenticatable para enviar la notificacion personalizada
     * @param mixed $token 
     */
    public function sendPasswordResetNotification($token): void
    {
        // Enviamos la notificacion al usuario a traves del canal de correo electronico
        $this->notify(new \App\Notifications\RestablecerPassword($token));
    }

    // Definimos la relacion entre usuario y conversacion
    public function conversaciones(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Conversacion::class, 'idUsuario');
    }
}
