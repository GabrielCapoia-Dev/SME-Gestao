<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ServidorController;
use App\Services\ApiFilterService;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



Route::get('/servidores', [ServidorController::class, 'index']);

Route::get('/obterDados/entidade/{entidade}/exercicio/{exercicio}/{content}={param}', [ApiFilterService::class, 'obterDadosApiServidores']);
