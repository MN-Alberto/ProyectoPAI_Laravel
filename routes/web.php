<?php

use App\Http\Controllers\ConversacionController;
use App\Http\Controllers\MensajeController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

//Ruta por defecto al entrar a la aplicación
Route::get('/', function () {
    return view('welcome');
});

//Rutas para el perfil del usuario
Route::middleware('auth')->group(function () {
    //Rutas para el perfil del usuario
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    //Rutas para actualizar el perfil del usuario
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    //Rutas para actualizar el perfil desde el chat modal
    Route::patch('/profile/actualizar-usuario', [ProfileController::class, 'actualizarUsuario'])->name('profile.actualizar-usuario');
    //Rutas para eliminar el perfil del usuario
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    //Rutas para cambiar el modelo
    Route::patch('/conversaciones/{conversacion}/modelo', [MensajeController::class, 'cambiarModelo'])->name('conversaciones.modelo');
});

//Rutas para las conversaciones
Route::middleware('auth')->group(function () {
    //Rutas para las conversaciones
    Route::get('/conversaciones', [ConversacionController::class, 'index'])->name('conversaciones.index');
    //Rutas para guardar las conversaciones
    Route::post('/conversaciones', [ConversacionController::class, 'store'])->name('conversaciones.store');
    //Rutas para mostrar las conversaciones
    Route::get('/conversaciones/{conversacion}', [ConversacionController::class, 'show'])->name('conversaciones.show');
    //Rutas para eliminar las conversaciones
    Route::delete('/conversaciones/{conversacion}', [ConversacionController::class, 'destroy'])->name('conversaciones.destroy');
    //Rutas para guardar los mensajes
    Route::post('/conversaciones/{conversacion}/mensajes', [MensajeController::class, 'store'])->name('mensajes.store');
    //Rutas para guardar la respuesta de la ia en la base de datos
    Route::post('/conversaciones/{conversacion}/mensajes/{mensaje}/guardar', [MensajeController::class, 'guardarRespuesta']);
    //Rutas para el bloqueo del modelo
    Route::post('/modelo/adquirir-bloqueo', [MensajeController::class, 'adquirirBloqueo'])->name('modelo.adquirir-bloqueo');
    Route::post('/modelo/liberar-bloqueo', [MensajeController::class, 'liberarBloqueo'])->name('modelo.liberar-bloqueo');
});

//Rutas para la autenticación
require __DIR__ . '/auth.php';
