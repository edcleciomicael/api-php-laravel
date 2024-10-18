<?php

namespace App\Transformers;

use App\Models\AppCentralAssinanteParametros;

use App\Models\Pessoa;
use League\Fractal\TransformerAbstract;

class PessoaTransformer extends TransformerAbstract
{
    public function transform(Pessoa $pessoa)
    {
        $param = new AppCentralAssinanteParametros;
        $param = $param->find(29);

        $validado = true;
        if($param->ativo){
            $validado = false;
            foreach ($pessoa->financeiroDadosValidacao as $validacao) {
                if($validacao->origem = 2 && !is_null($validacao->data_confirmacao)){
                    $validado = true;
                }
            }
        }

        $formatted = [
            'cod_pessoa'        => $pessoa->cod_pessoa,
            'nome'              => $pessoa->nome_pessoa,
            'cpf_cnpj'          => $pessoa->cpf_cnpj,
            'endereco'          => $pessoa->endereco,
            'numero'            => $pessoa->numero,
            'complemento'       => $pessoa->complemento,
            'cep'               => $pessoa->cep,
            'bairro'            => $pessoa->bairroCidade->bairro,
            'cidade'            => $pessoa->bairroCidade->cidade->nome_cidade,
            'uf'                => $pessoa->bairroCidade->cidade->uf,
            'email'             => strpos($pessoa->email, '|') !== false ? \explode('|', $pessoa->email) : \explode(',', $pessoa->email),
            'telefone'          => strpos($pessoa->telefone, '|') !== false ? \explode('|', $pessoa->telefone) : \explode(',', $pessoa->telefone),
            'pessoa_juridica'   => $pessoa->pessoa_juridica,
            'cod_unidade'       => $pessoa->cod_unidade,
            // 'logs'              => $pessoa->logs,
            // 'devices'           => $pessoa->DevicesUsers,
            'validado'          => $pessoa->validado,
        ];

        return $formatted;
    }
}