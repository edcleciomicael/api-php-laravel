<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Pessoa;
// use App\Models\Usuario;

class PessoasController extends Controller
{
    public function show($codPessoa)
    {
        //Query SQL
        // $results = DB::select('select * from public.pessoa WHERE cod_pessoa = :id', ['id' => 1001]);
        //Retorna apenas um campo da table
        // $nomePessoa = Usuario::find($id, ['nome_completo']);
        //Retona todos os campos da table
        // $dados = Pessoa::find($id, ['razao_social']);
        $dados = DB::select('select * from public.pessoa where cod_pessoa = ?', [$codPessoa]);
        return $dados;
    }

    public function contratos($codPessoa)
    {
        $dados = DB::select('select * from public.pessoa where cod_pessoa = ?', [$codPessoa]);
        return $dados;
    }
}
