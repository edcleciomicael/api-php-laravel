<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use Illuminate\Support\Facades\DB;

class UsuariosController extends Controller
{
    public function index() {
        return Usuario::all();
    }

    public function show($id)
    {
        //Query SQL
        $results = DB::select('select * from usuario where cod_usuario = :id', ['id' => 10]);
        //Retorna apenas um campo da table
        // $nomePessoa = Usuario::find($id, ['nome_completo']);
        //Retona todos os campos da table
        // $dados = Usuario::find($id);
        return response()->json($results);
    }
}
