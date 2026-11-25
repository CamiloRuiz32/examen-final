<?php

use App\Http\Controllers\{ClienteController, CocheController, RevisionController};
use Illuminate\Support\Facades\Route;

Route::pattern('id', '[0-9]+');

Route::get('clientes/{id}/coches', [ClienteController::class, 'coches'])->whereNumber('id');
Route::post('clientes/{id}/coches', [CocheController::class, 'storeForCliente'])->whereNumber('id');
Route::get('coches/con-revisiones', [CocheController::class, 'conRevisiones']);
Route::get('coches/{id}/revisiones', [CocheController::class, 'revisiones'])->whereNumber('id');
Route::post('coches/{id}/revisiones', [RevisionController::class, 'storeForCoche'])->whereNumber('id');
Route::get('coches/{id}/foto', [CocheController::class, 'foto'])->whereNumber('id')->name('coches.foto');

Route::delete('coches/{id}/foto', [CocheController::class, 'destroyFoto']);

Route::apiResource('clientes', ClienteController::class)->parameters(['clientes' => 'id']);
Route::apiResource('coches', CocheController::class)->parameters(['coches' => 'id']);
Route::apiResource('revisiones', RevisionController::class)->parameters(['revisiones' => 'id']);

