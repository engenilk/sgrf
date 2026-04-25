<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

abstract class BaseController extends Controller
{
    protected $request;
    protected $helpers = [];
    protected $data = [];

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        $this->data['unidadeNome'] = env('app.unidadeNome', 'PROINTER');
        $this->data['sistemaNomeCompleto'] = env('app.sistemaNomeCompleto', 'SGRF');
        
        // Passa o usuário logado para as views (para exibirmos na Navbar depois)
        $this->data['usuarioLogado'] = session()->get('usuario');
    }

    // Função global de auditoria
    protected function auditar($acao, $detalhes = null)
    {
        // Se a tabela auditoria ainda não existir (segurança para não quebrar em testes de migration)
        $db = \Config\Database::connect();
        if (!$db->tableExists('auditoria')) return;

        $auditoriaModel = new \App\Models\AuditoriaModel(); // Obs: O model pode reclamar que as colunas mudaram, vamos conferir isso.
        
        // Usa o Query Builder direto para evitar problemas com o Model desatualizado
        $db->table('auditoria')->insert([
            'usuario_id' => session()->get('usuario_id'), // Agora gravamos o ID!
            'usuario'    => session()->get('usuario') ?? 'Sistema',
            'acao'       => $acao,
            'detalhes'   => $detalhes,
            'ip_address' => $this->request->getIPAddress(),
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }

    // Função utilitária para limpar a máscara de moeda (R$ 1.500,50 -> 1500.50)
    protected function limparMascaraMoeda($valorMask)
    {
        if (empty($valorMask)) return 0;
        if (is_numeric($valorMask)) return (float) $valorMask; // Se já vier limpo
        
        // Remove R$, espaços e o ponto de milhar, depois troca a vírgula por ponto
        $valor = str_replace(['R$', ' ', "\u{00A0}"], '', $valorMask);
        $valor = str_replace('.', '', $valor);
        $valor = str_replace(',', '.', $valor);
        
        return (float) $valor;
    }
}