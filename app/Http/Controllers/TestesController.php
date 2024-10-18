<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TestesController extends Controller
{
    public function index() {
        $data = [0 => "Api Funcionando, Tentando Conectar ao Banco ... ",];
        try {
            DB::connection()->getPdo();
            if(DB::connection()->getDatabaseName()){
                array_push($data, "Conexão bem-sucedida ao banco de dados: " . DB::connection()->getDatabaseName());
            }else{
                array_push($data, "Não foi possível encontrar o banco de dados. Verifique suas configurações.");
            }
        } catch (\Exception $e) {
            array_push($data, "Não foi possível conectar ao banco de dados. Verifique suas configurações. Erro: " . $e->getMessage());
        }

        return response(json_encode($data))->header('Content-Type', 'application/json');
    }
}
