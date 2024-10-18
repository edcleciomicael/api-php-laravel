<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\UsuariosController;
use App\Http\Controllers\PessoasController;
use App\Http\Controllers\TestesController;
use App\Http\Controllers\AcessTokenContoller;
use App\Http\Controllers\AuthController;
// use Illuminate\Support\Facades\DB;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Aqui é onde você pode registrar rotas da API
|
*/

// $router->group(['middleware' => ['auth:api', 'throttle:60']], function () use ($router) {
//     $router->get('usuario', [
//         'uses'       => 'PessoaController@index',
//         'middleware' => "scope:users,users:read"
//     ]);
// });

Route::get('/', [TestesController::class, 'index']);

Route::get('/usuarios/{codPessoa}', [PessoasController::class, 'show']);

Route::post('/login', [AuthController::class, 'login']);

Route::post('/contratos', [PessoasController::class, 'login']);
