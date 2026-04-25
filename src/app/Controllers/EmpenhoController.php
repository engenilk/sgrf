<?php

namespace App\Controllers;

use App\Models\EmpenhoModel;
use App\Models\AcaoModel;
use App\Models\PiModel;
use App\Models\MovimentacaoModel;

class EmpenhoController extends BaseController
{

public function index()
    {
        $db = \Config\Database::connect();
        
        // Busca todos os empenhos e junta com os dados do PI correspondente
        $builder = $db->table('empenhos e');
        $builder->select('
            e.*, 
            p.codigo as pi_codigo, 
            p.programa as pi_programa,
            (e.valor_total - e.valor_consumido) as saldo_disponivel
        ', false);
        $builder->join('pis p', 'e.pi_id = p.id', 'left');
        
        // Ordenação decrescente em relação ao valor disponível (Pedido da demandante)
        $builder->orderBy('saldo_disponivel', 'DESC');
        
        $empenhos_brutos = $builder->get()->getResult();

        // Agrupa os empenhos por PI (Uma "tabela" por PI)
        $empenhos_agrupados = [];
        foreach ($empenhos_brutos as $emp) {
            $nomePi = $emp->pi_codigo . ($emp->pi_programa ? ' (' . $emp->pi_programa . ')' : '');
            $empenhos_agrupados[$nomePi][] = $emp;
        }

        $this->data['empenhosAgrupados'] = $empenhos_agrupados;
        $this->data['tituloPagina'] = 'Visão Geral de Empenhos';

        return view('empenhos/index', $this->data);
    }

    public function porPi($pi_id)
    {
        $piModel = new \App\Models\PiModel();
        $empenhoModel = new \App\Models\EmpenhoModel();

        $pi = $piModel->find($pi_id);
        if (!$pi) return redirect()->to('/pis')->with('erro', 'Plano Interno não encontrado.');

        // Captura os filtros de data da URL
        $dataInicio = $this->request->getGet('data_inicio');
        $dataFim    = $this->request->getGet('data_fim');

        $builder = $empenhoModel->where('pi_id', $pi_id);

        // Aplica os filtros se eles foram preenchidos
        if (!empty($dataInicio)) {
            $builder->where('DATE(created_at) >=', $dataInicio);
        }
        if (!empty($dataFim)) {
            $builder->where('DATE(created_at) <=', $dataFim);
        }

        $this->data['pi'] = $pi;
        // Mantém a ordenação pelo maior saldo disponível pedida pela demandante
        $this->data['empenhos'] = $builder->orderBy('(valor_total - valor_consumido)', 'DESC')->findAll();
        
        // Passa os filtros para a view para manter os inputs preenchidos
        $this->data['filtros'] = [
            'data_inicio' => $dataInicio,
            'data_fim'    => $dataFim
        ];
        
        $this->data['tituloPagina'] = 'Empenhos Vinculados';

        return view('empenhos/por_pi', $this->data);
    }

    public function detalhes($id)
    {
        $empenhoModel = new EmpenhoModel();
        $acaoModel = new AcaoModel();
        $piModel = new PiModel();

        $empenho = $empenhoModel->find($id);
        if (!$empenho) return redirect()->to('/pis')->with('erro', 'Empenho não encontrado.');

        $this->data['empenho'] = $empenho;
        $this->data['pi'] = $piModel->find($empenho->pi_id);
        
        // Ordena ações pelas mais recentes
        $this->data['acoes'] = $acaoModel->where('empenho_id', $id)->orderBy('created_at', 'DESC')->findAll();
        $this->data['tituloPagina'] = 'Detalhes do Empenho';

        return view('empenhos/detalhes', $this->data);
    }

    public function salvar()
    {
        $piId = $this->request->getPost('pi_id');
        // Usamos a nova função para limpar a máscara (R$ 0,00 -> 0.00)
        $valorTotal = $this->limparMascaraMoeda($this->request->getPost('valor_total'));
        
        $piModel = new PiModel();
        $empenhoModel = new EmpenhoModel();
        $movimentacaoModel = new MovimentacaoModel();
        $db = \Config\Database::connect();

        if ($valorTotal <= 0) return redirect()->back()->with('erro', 'O valor do empenho deve ser maior que zero.');

        $db->transStart();

        $pi = $piModel->find($piId);
        if ($valorTotal > $pi->saldo_disponivel) {
            return redirect()->back()->with('erro', 'Saldo insuficiente no PI. Saldo atual: R$ ' . number_format($pi->saldo_disponivel, 2, ',', '.'));
        }

        // Registra o Empenho com TODOS os campos novos
        $empenhoId = $empenhoModel->insert([
            'pi_id'              => $piId,
            'codigo_dfc'         => $this->request->getPost('codigo_dfc'),
            'numero_processo'    => $this->request->getPost('numero_processo'),
            'fonte'              => $this->request->getPost('fonte'),
            'ptres'              => $this->request->getPost('ptres'),
            'objeto'             => $this->request->getPost('objeto'),
            'observacoes'        => $this->request->getPost('observacoes'),
            'valor_total'        => $valorTotal,
            'valor_consumido'    => 0, 
            'status'             => 'Ativo'
        ]);

        $piModel->update($piId, ['saldo_disponivel' => $pi->saldo_disponivel - $valorTotal]);

        $movimentacaoModel->insert([
            'pi_origem_id'            => $piId,
            'empenho_id'              => $empenhoId,
            'tipo_operacao'           => 'Novo_Empenho',
            'valor'                   => $valorTotal,
            'justificativa_historico' => 'Reserva de crédito para Empenho: ' . $this->request->getPost('numero_processo')
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) return redirect()->back()->with('erro', 'Erro ao registrar o empenho.');

        $this->auditar('Novo Empenho', 'Cadastrou o empenho ' . $this->request->getPost('numero_processo') . ' no PI ' . $pi->codigo);

        return redirect()->back()->with('sucesso', 'Empenho reservado com sucesso!');
    }

    public function reforcar($id)
    {
        $valorReforco = $this->limparMascaraMoeda($this->request->getPost('valor_reforco'));
        
        if ($valorReforco <= 0) return redirect()->back()->with('erro', 'O valor do reforço deve ser maior que zero.');

        $empenhoModel = new EmpenhoModel();
        $piModel = new PiModel();
        $movimentacaoModel = new MovimentacaoModel();
        $db = \Config\Database::connect();

        $db->transStart();

        $empenho = $empenhoModel->find($id);
        $pi = $piModel->find($empenho->pi_id);

        if ($valorReforco > $pi->saldo_disponivel) return redirect()->back()->with('erro', 'Saldo insuficiente no PI para este reforço.');

        $empenhoModel->update($id, ['valor_total' => $empenho->valor_total + $valorReforco]);
        $piModel->update($pi->id, ['saldo_disponivel' => $pi->saldo_disponivel - $valorReforco]);

        $movimentacaoModel->insert([
            'pi_origem_id'            => $pi->id,
            'empenho_id'              => $id,
            'tipo_operacao'           => 'Reforco_Empenho',
            'valor'                   => $valorReforco,
            'justificativa_historico' => 'Reforço de saldo para o Empenho: ' . $empenho->numero_processo
        ]);

        $db->transComplete();

        $this->auditar('Reforço de Empenho', "Adicionou R$ $valorReforco ao empenho " . $empenho->numero_processo);

        return redirect()->back()->with('sucesso', 'Reforço aplicado com sucesso!');
    }

    public function anular($id)
    {
        $valorAnulacao = $this->limparMascaraMoeda($this->request->getPost('valor_anulacao'));

        if ($valorAnulacao <= 0) return redirect()->back()->with('erro', 'Valor inválido.');

        $empenhoModel = new EmpenhoModel();
        $piModel = new PiModel();
        $movimentacaoModel = new MovimentacaoModel();
        $db = \Config\Database::connect();

        $db->transStart();

        $empenho = $empenhoModel->find($id);
        $saldoRestante = $empenho->valor_total - $empenho->valor_consumido;

        if ($valorAnulacao > $saldoRestante) return redirect()->back()->with('erro', 'Valor de anulação maior que o saldo restante.');

        $pi = $piModel->find($empenho->pi_id);

        $piModel->update($pi->id, ['saldo_disponivel' => $pi->saldo_disponivel + $valorAnulacao]);

        $movimentacaoModel->insert([
            'pi_destino_id'           => $pi->id,
            'empenho_id'              => $id,
            'tipo_operacao'           => 'Anulacao_Empenho',
            'valor'                   => $valorAnulacao,
            'justificativa_historico' => 'Anulação de saldo não consumido do Empenho: ' . $empenho->numero_processo
        ]);

        $novoValorTotal = $empenho->valor_total - $valorAnulacao;
        $novoStatus = ($novoValorTotal == $empenho->valor_consumido) ? 'Anulado' : 'Ativo';

        $empenhoModel->update($id, ['valor_total' => $novoValorTotal, 'status' => $novoStatus]);

        $db->transComplete();

        $this->auditar('Anulação de Empenho', "Anulou R$ $valorAnulacao do empenho " . $empenho->numero_processo);

        return redirect()->back()->with('sucesso', 'Anulação processada com sucesso!');
    }
}