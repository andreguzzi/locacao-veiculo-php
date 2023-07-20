<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

//Route::resource('cliente', 'App\Http\Controllers\ClienteController');
Route::apiResource('cliente', 'App\Http\Controllers\ClienteController');
Route::resource('carro', 'App\Http\Controllers\CarroController');
Route::resource('locacao', 'App\Http\Controllers\LocacaoController');
Route::resource('marca', 'App\Http\Controllers\MarcaController');
Route::resource('modelo', 'App\Http\Controllers\ModeloController');
