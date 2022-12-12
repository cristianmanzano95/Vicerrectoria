<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ConsultaController;

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

// Route::get('/consultavice/{idgrupo}/{idtipo}', [ConsultaController::class, 'consultafacultad']);  //mostrar todos los datos
// Route::get('/consultavice', [ConsultaController::class, 'consulta']);  //mostrar todos los datos

//Grupos y Semilleros
Route::get('/consultavice/{idgrupo}/{nomfacultad}/{idtipo}', [ConsultaController::class, 'consultafacultad']);  //Url Completa de Grupos y Semilleros

//Projectos
Route::get('/consultaviceproject/{idpro}', [ConsultaController::class, 'consultaprojects']);  //Url completa projectos

// Colciencias
Route::get('/consultacol/{id}', [ConsultaController::class, 'consultacolcien']);  //Url completa Colciencias

// IntegrantesGrupos Y Detalles
Route::get('/consultaintegrantes/{idgru}/grupo', [ConsultaController::class, 'consultagrupo']);  //Url completa integrantes grupos

// IntegrantesSemilleros Y Detalles
Route::get('/consultaintegrantesSemi/{idsemillero}/semillero', [ConsultaController::class, 'consultasemillero']);  //Url completa Integrantes Semilleros

// IntegrantesProyectos Y Detalles
Route::get('/consultadetallesproyecto/detallesproyecto/{idproyecto}', [ConsultaController::class, 'detallesgrupos']);  //Url completa Integrantes Semilleros

