<?php

namespace App\Controllers;

use App\Models\PiModel;
use App\Models\MovimentacaoModel;

class MovimentacaoController extends BaseController
{
    public function transferir()
    {
        $origemId = $this->request->getPost('pi_origem_id');
        $destinoId = $this->request->getPost('pi_destino_id');
        $valor = $this->request->getPost('valor');
        $justificativa = $this->request->getPost('justificativa_historico');

        // Validação básica
        if ($origemId == $destinoId) {
            return redirect()->back()->with('erro', 'O PI de origem e destino não podem ser o mesmo.');
        }
        if ($valor <= 0) {
            return redirect()->back()->with('erro', 'O valor da transferência deve ser maior que zero.');
        }

        $piModel = new PiModel();
        $movimentacaoModel = new MovimentacaoModel();
        $db = \Config\Database::connect();

        // 1. Inicia a Transação
        $db->transStart();

        // 2. Busca os PIs atualizados
        $piOrigem = $piModel->find($origemId);
        $piDestino = $piModel->find($destinoId);

        if (!$piOrigem || !$piDestino) {
            return redirect()->back()->with('erro', 'PI de origem ou destino não encontrado.');
        }

        // 3. Valida se a origem tem saldo suficiente
        if ($valor > $piOrigem->saldo_disponivel) {
            return redirect()->back()->with('erro', 'Saldo insuficiente no PI de origem. Saldo disponível: R$ ' . number_format($piOrigem->saldo_disponivel, 2, ',', '.'));
        }

        // 4. Atualiza o PI de Origem (Reduz o limite aprovado e o saldo)
        $piModel->update($origemId, [
            'limite_anual_aprovado' => $piOrigem->limite_anual_aprovado - $valor,
            'saldo_disponivel'      => $piOrigem->saldo_disponivel - $valor
        ]);

        // 5. Atualiza o PI de Destino (Aumenta o limite aprovado e o saldo)
        $piModel->update($destinoId, [
            'limite_anual_aprovado' => $piDestino->limite_anual_aprovado + $valor,
            'saldo_disponivel'      => $piDestino->saldo_disponivel + $valor
        ]);

        // 6. Registra no Livro Razão (Movimentações)
        $movimentacaoModel->insert([
            'pi_origem_id'            => $origemId,
            'pi_destino_id'           => $destinoId,
            'tipo_operacao'           => 'Transferencia',
            'valor'                   => $valor,
            'justificativa_historico' => $justificativa
        ]);

        // 7. Completa a Transação
        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('erro', 'Erro interno ao processar a transferência. Nenhuma alteração foi salva.');
        }

        return redirect()->back()->with('sucesso', 'Transferência de R$ ' . number_format($valor, 2, ',', '.') . ' realizada com sucesso!');
    }
}
