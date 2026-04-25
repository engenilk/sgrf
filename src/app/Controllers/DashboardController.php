<?php

namespace App\Controllers;

class DashboardController extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();

        // 1. Consolidação de PIs
        $pis = $db->table('pis')
                  ->selectSum('credito_atual')
                  ->selectSum('saldo_disponivel')
                  ->where('deleted_at', null)
                  ->get()->getRow();
                  
        $this->data['pi_total'] = $pis->credito_atual ?? 0;
        $this->data['pi_disponivel'] = $pis->saldo_disponivel ?? 0;
        $this->data['pi_consumido'] = $this->data['pi_total'] - $this->data['pi_disponivel'];

        // 2. Consolidação de Empenhos (Ignora os anulados)
        $emps = $db->table('empenhos')
                   ->selectSum('valor_total')
                   ->selectSum('valor_consumido')
                   ->where('status !=', 'Anulado')
                   ->get()->getRow();
                   
        $this->data['emp_total'] = $emps->valor_total ?? 0;
        $this->data['emp_consumido'] = $emps->valor_consumido ?? 0;
        $this->data['emp_disponivel'] = $this->data['emp_total'] - $this->data['emp_consumido'];

        // 3. Últimos 5 Empenhos cadastrados/movimentados
        $this->data['ultimos_empenhos'] = $db->table('empenhos e')
                                           ->select('e.*, p.codigo as pi_codigo')
                                           ->join('pis p', 'e.pi_id = p.id', 'left')
                                           ->orderBy('e.id', 'DESC')
                                           ->limit(5)
                                           ->get()->getResult();

        $this->data['tituloPagina'] = 'Dashboard Geral';

        return view('dashboard/index', $this->data);
    }
}