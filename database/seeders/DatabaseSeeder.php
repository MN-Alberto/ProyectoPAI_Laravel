<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Crear/Actualizar usuario admin
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Administrador',
                'password' => \Illuminate\Support\Facades\Hash::make('paso1234'),
                'is_admin' => true,
                'activo' => true,
            ]
        );

        $usuariosDemo = [
            ['name' => 'Gonzalo Junquera', 'email' => 'gonzalojunquera@daw2.com'],
            ['name' => 'Enrique Nieto', 'email' => 'enriquenieto@daw2.com'],
            ['name' => 'Oscar Pozuelo', 'email' => 'oscarpozuelo@daw2.com'],
            ['name' => 'Alvaro allen', 'email' => 'alvaroallen@daw2.com'],
            ['name' => 'Alberto Mendez', 'email' => 'albertomendez@daw2.com'],
            ['name' => 'Jesus Temprano', 'email' => 'jesustemprano@daw2.com'],
            ['name' => 'Alvaro Garcia', 'email' => 'alvarogarcia@daw2.com'],
            ['name' => 'Alejandro De la Huerga', 'email' => 'alejandrodelahuerga@daw2.com'],
            ['name' => 'Cristian Mateos', 'email' => 'cristianmateos@daw2.com'],
            ['name' => 'Heraclio Borbujo', 'email' => 'heraclioborbujo@daw2.com'],
            ['name' => 'Amor Rodriguez', 'email' => 'amorrodriguez@daw2.com'],
            ['name' => 'Alberto Bahillo', 'email' => 'albertobahillo@daw2.com'],
            ['name' => 'Jorge Corral', 'email' => 'jorgecorral@daw2.com'],
            ['name' => 'Hermelinda Ramos', 'email' => 'hermelindaramos@daw2.com'],
            ['name' => 'Antonio Jañez', 'email' => 'antoniojañez@daw2.com'],
            ['name' => 'Claudio Lozano', 'email' => 'claudiolozano@daw2.com'],
            ['name' => 'Gisela', 'email' => 'gisela@daw2.com'],
            ['name' => 'Beatriz Montenegro', 'email' => 'beatrizmontenegro@daw2.com'],
            ['name' => 'Test Usuario', 'email' => 'testusuario@daw2.com'],
            ['name' => 'Test Usuario 2', 'email' => 'test2usuario@daw2.com'],
        ];

        foreach ($usuariosDemo as $index => $u) {
            User::updateOrCreate(
                ['email' => $u['email']],
                [
                    'name' => $u['name'],
                    //  La contraseña para todos los usuarios demo es 'paso1234'
                    'password' => \Illuminate\Support\Facades\Hash::make('paso1234'),
                    'is_admin' => false,
                    // Últimos 3 usuarios inactivos para pruebas
                    'activo' => ($index < 18),
                    // Algunos usuarios con limitaciones de modelo para pruebas
                    'modelos_permitidos' => ($index % 5 === 0) ? ['mistral', 'phi3'] : null,
                ]
            );
        }
    }
}
