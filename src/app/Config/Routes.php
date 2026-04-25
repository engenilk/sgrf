<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// --------------------------------------------------------------------
// Rotas Públicas (Sem Filtro de Autenticação)
// --------------------------------------------------------------------
$routes->get('/', 'AuthController::login');
$routes->get('login', 'AuthController::login');
$routes->post('login/autenticar', 'AuthController::autenticar');
$routes->get('logout', 'AuthController::logout');


// --------------------------------------------------------------------
// Rotas Protegidas (Exigem Login via Filtro authGuard)
// --------------------------------------------------------------------
$routes->group('', ['filter' => 'authGuard'], function($routes) {
    
    // Dashboard / Planos Internos (PIs)
    $routes->group('pis', function($routes) {
        $routes->get('/', 'PiController::index');
        $routes->post('salvar', 'PiController::salvar');
    });

    // Gestão de Empenhos
    $routes->group('empenhos', function($routes) {
        $routes->get('por_pi/(:num)', 'EmpenhoController::porPi/$1');
        $routes->get('detalhes/(:num)', 'EmpenhoController::detalhes/$1');
        
        // Ações de formulário do Empenho
        $routes->post('salvar', 'EmpenhoController::salvar'); 
        $routes->post('reforcar/(:num)', 'EmpenhoController::reforcar/$1');
        $routes->post('anular/(:num)', 'EmpenhoController::anular/$1');
    });

    // Gestão de Ações (Consumo do Empenho)
    $routes->group('acoes', function($routes) {
        $routes->post('registrar', 'AcaoController::registrar');
    });

    // Transferências entre PIs
    $routes->group('movimentacoes', function($routes) {
        $routes->post('transferir', 'MovimentacaoController::transferir');
    });

    // Relatório Geral e Extrato (Razão Contábil)
    $routes->get('relatorios', 'RelatorioController::index');

    //Auditoria
    $routes->get('auditoria', 'AuditoriaController::index'); 
    
    //Usuários
    $routes->get('usuarios', 'UsuarioController::index');
    $routes->post('usuarios/salvar', 'UsuarioController::salvar');
    $routes->get('usuarios/status/(:num)', 'UsuarioController::alternarStatus/$1');

    $routes->get('dashboard', 'DashboardController::index');
    $routes->get('/', 'DashboardController::index');

    //Empenhos
    $routes->get('empenhos', 'EmpenhoController::index');

});