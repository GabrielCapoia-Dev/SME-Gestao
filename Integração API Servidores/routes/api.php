<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ServidorController;
use App\Http\Controllers\Api\ServidorController as ApiServidorController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



Route::get('/servidores', [ServidorController::class, 'index']);



Route::get('api/servidores', [ApiServidorController::class, 'index']);
Route::get('api/servidores/{matricula}', [ApiServidorController::class, 'show']);
