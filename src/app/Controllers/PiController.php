<?php

namespace App\Controllers;

class PiController extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();
        
        // Atualizado com os novos nomes de colunas do Banco (V2)
        $builder = $db->table('pis p');
        $builder->select('
            p.id,
            p.codigo, 
            p.programa,
            p.descricao, 
            p.credito_alocado,
            p.credito_atual, 
            (p.credito_atual - COALESCE(SUM(e.valor_total), 0)) as saldo_disponivel
        ', false);

        $builder->join('empenhos e', "p.id = e.pi_id AND e.status != 'Anulado'", 'left');
        $builder->where('p.deleted_at', null);
        $builder->groupBy('p.id');
        
        // Ordenação decrescente pelo saldo disponível
        $builder->orderBy('saldo_disponivel', 'DESC');
        
        $pis_brutos = $builder->get()->getResult();

        // Agrupando os PIs por Programa
        $pis_agrupados = [];
        $lista_simples = []; // Guardamos uma lista simples para popular o Modal de Transferência
        
        foreach ($pis_brutos as $pi) {
            $programa = !empty($pi->programa) ? $pi->programa : 'Sem Programa Associado';
            $pis_agrupados[$programa][] = $pi;
            $lista_simples[] = $pi;
        }

        $this->data['pisAgrupados'] = $pis_agrupados;
        $this->data['pis_simples'] = $lista_simples;
        $this->data['tituloPagina'] = 'Planos Internos';
        
        return view('pis/index', $this->data);
    }

    public function salvar()
    {
        $codigo   = $this->request->getPost('codigo');
        $programa = $this->request->getPost('programa');
        $descricao= $this->request->getPost('descricao');
        $credito  = $this->request->getPost('credito_alocado');

        $piModel = new \App\Models\PiModel(); // Certifique-se de que não tem erros de digitação aqui
        $movimentacaoModel = new \App\Models\MovimentacaoModel();
        $db = \Config\Database::connect();

        if (empty($codigo) || empty($descricao) || $credito < 0) {
            return redirect()->back()->with('erro', 'Preencha os campos obrigatórios e informe um crédito válido.');
        }

        if ($db->table('pis')->where('codigo', $codigo)->countAllResults() > 0) {
            return redirect()->back()->with('erro', 'Já existe um PI cadastrado com este código.');
        }

        $db->transStart();

        // Cadastra o PI (o crédito atual e o saldo inicializam com o valor do aporte inicial)
        $piId = $db->table('pis')->insert([
            'codigo'           => $codigo,
            'programa'         => $programa,
            'descricao'        => $descricao,
            'credito_alocado'  => $credito,
            'credito_atual'    => $credito,
            'saldo_disponivel' => $credito,
            'created_at'       => date('Y-m-d H:i:s')
        ]);

        if ($credito > 0) {
            $movimentacaoModel->insert([
                'pi_destino_id'           => $piId,
                'tipo_operacao'           => 'Aporte_Inicial',
                'valor'                   => $credito,
                'justificativa_historico' => 'Aporte de abertura para o novo PI: ' . $codigo
            ]);
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('erro', 'Erro interno ao salvar o PI.');
        }

        $this->auditar('Criação de PI', "Criou o Plano Interno $codigo ($programa).");

        return redirect()->back()->with('sucesso', 'Plano Interno cadastrado com sucesso!');
    }
}