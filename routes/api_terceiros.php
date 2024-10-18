<?php

use App\Http\Controllers\ModeloContratoController;
use App\Http\Controllers\ConvenioBancarioController;
use App\Http\Controllers\ModoCobrancaController;
use App\Http\Controllers\DiasVencimentoController;
use App\Http\Controllers\VendasController;
use App\Http\Controllers\ConsultaUsuariosController;
use App\Http\Controllers\ContratosController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\UsuariosController;
use App\Http\Controllers\TestesController;
// use Illuminate\Support\Facades\DB;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Aqui é onde você pode registrar rotas da API
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/usuarios', [UsuariosController::class, 'index']);

Route::get('/', [TestesController::class, 'index']);

Route::get('/usuarios/{id}', [UsuariosController::class, 'show']);

Route::post('/modelo_contrato', [ModeloContratoController::class, 'get_modelo_contrato']);

Route::post('/convenio_bancario', [ConvenioBancarioController::class, 'get_convenio_bancario']);

Route::post('/modo_cobranca', [ModoCobrancaController::class, 'get_modo_cobranca']);

Route::post('/dias_vencimento', [DiasVencimentoController::class, 'get_dias_vencimento']);

Route::post('/vendas', [VendasController::class, 'get_vendas']);

Route::post('/consulta_usuarios', [ConsultaUsuariosController::class, 'consulta_usuarios']);

Route::post('/contratos', [ContratosController::class, 'contratos']);