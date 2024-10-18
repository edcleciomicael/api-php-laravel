<?php

namespace App\Transformers;

use App\Models\ProdutosProduto;
use League\Fractal\TransformerAbstract;

class ProdutosProdutoTransformer extends TransformerAbstract
{
    public function transform(ProdutosProduto $produtosProduto)
    {
        $formatted = [
            'cod_produto'       => $produtosProduto->cod_produto,
            'nome_produto'      => $produtosProduto->nome_produto,
            'preco_venda'       => $produtosProduto->preco_venda,
            'is_pacote'         => $produtosProduto->is_pacote,
        ];

        return $formatted;
    }
}
