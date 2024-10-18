<?php

namespace App\Http\Controllers;

use App\Models\Pessoa;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $cpfCnpj = $this->formatCpfCnpj($request->cpf_cnpj);

        $camposSelecionados = [
            'cod_pessoa',
            'nome_pessoa',
            'cpf_cnpj',
            'endereco',
            'numero',
            'cod_bairro_cidade',
            'complemento',
            'cep',
            'email',
            'telefone',
            'pessoa_juridica',
            'cod_unidade',
        ];

        $user = Pessoa::where('cpf_cnpj', $cpfCnpj)
            ->select($camposSelecionados)
            ->first();

            if ($user) {
                // Convertendo os e-mails em um array se não estiver vazio
                $emails = $user->email ? explode(',', $user->email) : [];

                // Criar um array associativo mapeando os campos
                $dadosUsuario = array_combine($camposSelecionados, array_map(fn($campo) => $user->$campo, $camposSelecionados));

                // Adicionando os e-mails como um array
                $dadosUsuario['emails'] = $emails;

                // Retornar apenas os campos desejados na resposta JSON
                return response()->json($dadosUsuario);
            }

        // $res = ($user) ? response()->json($user) : "As credenciais fornecidas são inválidas." ;

        return response()->json($res);

        throw ValidationException::withMessages([
            'cpf_cnpj' => ['As credenciais fornecidas são inválidas.'],
        ]);
    }

    protected function formatCpfCnpj($cpfCnpj)
    {
        // Remove caracteres não numéricos
        $cpfCnpj = preg_replace('/\D/', '', $cpfCnpj);

        // Adiciona pontos e traços se não estiverem presentes
        if (strlen($cpfCnpj) === 11) {
            // Formata CPF: 123.456.789-01
            $cpfCnpj = vsprintf('%s%s%s.%s%s%s.%s%s%s-%s%s', str_split($cpfCnpj));
        } elseif (strlen($cpfCnpj) === 14) {
            // Formata CNPJ: 12.345.678/0001-90
            $cpfCnpj = vsprintf('%s%s.%s%s%s.%s%s%s/%s%s%s%s-%s%s', str_split($cpfCnpj));
        }

        return $cpfCnpj;
    }
}
