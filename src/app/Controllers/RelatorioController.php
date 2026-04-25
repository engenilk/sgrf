<?php

namespace App\Controllers;

class RelatorioController extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();
        
        // 1. Visão Consolidada dos PIs (Retrato Atual - Sem filtros de data)
        $builder = $db->table('pis p');
        $builder->select('
            p.codigo, 
            p.descricao, 
            p.credito_alocado,
            p.credito_atual,
            COALESCE(SUM(CASE WHEN e.status != "Anulado" THEN e.valor_total ELSE 0 END), 0) as total_empenhado,
            COALESCE(SUM(CASE WHEN e.status != "Anulado" THEN e.valor_consumido ELSE 0 END), 0) as total_executado
        ', false);
        $builder->join('empenhos e', 'p.id = e.pi_id', 'left');
        $builder->where('p.deleted_at', null);
        $builder->groupBy('p.id');
        $builder->orderBy('p.codigo', 'ASC');
        
        $this->data['pis_consolidados'] = $builder->get()->getResult();

        // --- PREPARAÇÃO DOS FILTROS DA RAZÃO CONTÁBIL ---
        $dataInicio = $this->request->getGet('data_inicio');
        $dataFim    = $this->request->getGet('data_fim');
        $filtroPi   = $this->request->getGet('pi_id');
        $filtroEmp  = $this->request->getGet('empenho_id');

        // 2. Razão Contábil (Histórico)
        $builderMov = $db->table('movimentacoes m');
        $builderMov->select('
            m.*,
            po.codigo as pi_origem,
            pd.codigo as pi_destino,
            e.numero_processo as empenho_processo
        ');
        $builderMov->join('pis po', 'm.pi_origem_id = po.id', 'left');
        $builderMov->join('pis pd', 'm.pi_destino_id = pd.id', 'left');
        $builderMov->join('empenhos e', 'm.empenho_id = e.id', 'left');

        // Aplicação dinâmica dos Filtros
        if (!empty($dataInicio)) {
            $builderMov->where('DATE(m.created_at) >=', $dataInicio);
        }
        if (!empty($dataFim)) {
            $builderMov->where('DATE(m.created_at) <=', $dataFim);
        }
        if (!empty($filtroPi)) {
            // Se filtrou por PI, busca onde ele é origem OU destino
            $builderMov->groupStart()
                       ->where('m.pi_origem_id', $filtroPi)
                       ->orWhere('m.pi_destino_id', $filtroPi)
                       ->groupEnd();
        }
        if (!empty($filtroEmp)) {
            $builderMov->where('m.empenho_id', $filtroEmp);
        }

        $builderMov->orderBy('m.created_at', 'DESC');
        
        $this->data['movimentacoes'] = $builderMov->get()->getResult();
        
        // Listas para popular os Selects do Formulário de Filtro
        $this->data['lista_pis'] = $db->table('pis')->select('id, codigo')->where('deleted_at', null)->get()->getResult();
        $this->data['lista_empenhos'] = $db->table('empenhos')->select('id, numero_processo')->where('status !=', 'Anulado')->get()->getResult();
        
        // Devolve os filtros para manter os campos preenchidos após pesquisar
        $this->data['filtros'] = [
            'data_inicio' => $dataInicio,
            'data_fim'    => $dataFim,
            'pi_id'       => $filtroPi,
            'empenho_id'  => $filtroEmp
        ];

        $this->data['tituloPagina'] = 'Relatórios Gerenciais';

        return view('relatorios/index', $this->data);
    }
}