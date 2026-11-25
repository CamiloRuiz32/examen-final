<?php

use App\Http\Controllers\ConcesionarioController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'inicio')->name('inicio');
Route::get('/consulta', [ConcesionarioController::class, 'consulta'])->name('consulta');

Route::pattern('modulo', 'clientes|coches|revisiones');
Route::pattern('id', '[0-9]+');
Route::get('/{modulo}', [ConcesionarioController::class, 'index'])->name('registros.index');
Route::get('/{modulo}/crear', [ConcesionarioController::class, 'create'])->name('registros.create');
Route::post('/{modulo}', [ConcesionarioController::class, 'store'])->name('registros.store');
Route::get('/{modulo}/{id}', [ConcesionarioController::class, 'show'])->name('registros.show');
Route::get('/{modulo}/{id}/editar', [ConcesionarioController::class, 'edit'])->name('registros.edit');
Route::match(['put', 'patch'], '/{modulo}/{id}', [ConcesionarioController::class, 'update'])->name('registros.update');
Route::delete('/{modulo}/{id}', [ConcesionarioController::class, 'destroy'])->name('registros.destroy');
Route::delete('/coches/{id}/foto', [ConcesionarioController::class, 'destroyFoto'])->name('coches.foto.destroy');
