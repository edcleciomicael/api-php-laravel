<?php

namespace App\Transformers;

use App\Models\ContratosPessoaContrato;
use League\Fractal\TransformerAbstract;

class ContratoDetalhesTransformer extends TransformerAbstract
{
    public function transform(ContratosPessoaContrato $contratosPessoaContrato)
    {
        $formattedUser = [
            'cod_pessoa_contrato'   => $contratosPessoaContrato->cod_pessoa_contrato,
            'cpf_cnpj'              => $contratosPessoaContrato->cpf_cnpj,
            'endereco'              => $contratosPessoaContrato->endereco,
            'numero'                => $contratosPessoaContrato->numero,
            'complemento'           => $contratosPessoaContrato->complemento,
            'cep'                   => $contratosPessoaContrato->cep,
            'bairro'                => $contratosPessoaContrato->bairro,
            'cidade'                => $contratosPessoaContrato->nome_cidade,
            'uf'                    => $contratosPessoaContrato->uf,
            'email'                 => strpos($contratosPessoaContrato->email, '|') !== false ? \explode('|', $contratosPessoaContrato->email) : \explode(',', $contratosPessoaContrato->email),
            'telefone'              => strpos($contratosPessoaContrato->telefone, '|') !== false ? \explode('|', $contratosPessoaContrato->telefone) : \explode(',', $contratosPessoaContrato->telefone),
            'cod_unidade'           => $contratosPessoaContrato->cod_unidade,
            'cod_status_contrato'   => $contratosPessoaContrato->cod_status_contrato,
            'texto_status_contrato' => $contratosPessoaContrato->status_contrato,
            'servicos'              => $contratosPessoaContrato->servicos,
        ];

        return $formattedUser;
    }
}