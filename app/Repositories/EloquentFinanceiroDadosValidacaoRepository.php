<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\Contracts\FinanceiroDadosValidacaoRepository;

class EloquentFinanceiroDadosValidacaoRepository extends AbstractEloquentRepository implements FinanceiroDadosValidacaoRepository
{
    public function createRow($cpfCnpj, $tipo, $contatoValidar, $origem, $codItem){
        $newEntry = $this->save(
            array(
                "cpf_cnpj" => $cpfCnpj,
                "tipo" => $tipo,
                "contato_validar" => $contatoValidar,
                "cod_validacao" => $tipo === 1 ? substr(md5(rand()),0,5) : null,
                "data_envio" => date("Y-m-d H:i:s"),
                "data_confirmacao" => null,
                "origem" => $origem,
                "cod_update" => $codItem,
            )
        );

        return $newEntry;
    }

    public function verifyCode($id, $code){
        $verifiedCode = $this->findBy(['cod_financeiro_dados_validacao' => $id, 'cod_validacao' => strtolower($code), 'data_confirmacao' => null]);

        if(count($verifiedCode) > 0){
            $verifiedCode = $this->findOne($id);
            $this->update($verifiedCode, array('data_confirmacao' => date("Y-m-d H:i:s")));
            return $verifiedCode;
        }

        return null;
    }
}