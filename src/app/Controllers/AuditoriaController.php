<?php

namespace App\Controllers;

use App\Models\AuditoriaModel;

class AuditoriaController extends BaseController
{
    public function index()
    {
        // Trava de Segurança (ACL): Apenas Admin
        if (session()->get('perfil') !== 'Admin') {
            return redirect()->to('/pis')->with('erro', 'Acesso negado. Apenas administradores podem visualizar os logs de auditoria.');
        }

        $auditoriaModel = new AuditoriaModel();
        
        // Busca os últimos 500 registros para não sobrecarregar a tela
        $this->data['logs'] = $auditoriaModel->orderBy('created_at', 'DESC')->findAll(500);
        $this->data['tituloPagina'] = 'Painel de Auditoria';

        return view('auditoria/index', $this->data);
    }
}