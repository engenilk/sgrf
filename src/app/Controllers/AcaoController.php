<?php

namespace App\Controllers;

use App\Models\AcaoModel;
use App\Models\EmpenhoModel;

class AcaoController extends BaseController
{
    public function registrar()
    {
        $empenhoId = $this->request->getPost('empenho_id');
        $valorAcao = $this->limparMascaraMoeda($this->request->getPost('valor'));

        if ($valorAcao <= 0) {
            return redirect()->back()->with('erro', 'O valor da ação deve ser maior que zero.');
        }

        $empenhoModel = new EmpenhoModel();
        $acaoModel = new AcaoModel();
        $db = \Config\Database::connect();

        $db->transStart();

        $empenho = $empenhoModel->find($empenhoId);

        if (!$empenho || $empenho->status !== 'Ativo') {
            return redirect()->back()->with('erro', 'Empenho inválido ou inativo.');
        }

        $saldoDisponivelEmpenho = $empenho->valor_total - $empenho->valor_consumido;

        if ($valorAcao > $saldoDisponivelEmpenho) {
            return redirect()->back()->with('erro', 'O valor informado ultrapassa o saldo disponível no empenho.');
        }

        // Registra a Ação com todos os campos novos do V2
        $acaoModel->insert([
            'empenho_id'     => $empenhoId,
            'processo'       => $this->request->getPost('processo'),
            'lista_credor'   => $this->request->getPost('lista_credor'),
            'referencia'     => $this->request->getPost('referencia'),
            'tipo_pagamento' => $this->request->getPost('tipo_pagamento'),
            'data_envio'     => $this->request->getPost('data_envio'),
            'descricao'      => $this->request->getPost('descricao'),
            'beneficiario'   => $this->request->getPost('beneficiario'),
            'valor'          => $valorAcao,
            'observacoes'    => $this->request->getPost('observacoes')
        ]);

        // Atualiza o valor consumido do Empenho
        $empenhoModel->update($empenhoId, [
            'valor_consumido' => $empenho->valor_consumido + $valorAcao
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('erro', 'Erro ao registrar a ação de pagamento.');
        }

        $this->auditar('Registro de Ação/Consumo', 'Registrou pagamento no valor de R$ ' . number_format($valorAcao, 2, ',', '.') . ' no empenho ' . $empenho->numero_processo);

        return redirect()->back()->with('sucesso', 'Ação registrada! O saldo do empenho foi atualizado.');
    }
}