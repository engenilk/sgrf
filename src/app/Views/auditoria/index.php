<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $tituloPagina ?> - SGRF / <?= $unidadeNome ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', Roboto, sans-serif; display: flex; flex-direction: column; min-height: 100vh; }
        .navbar-ufpa { background-color: #751621; border-bottom: 4px solid #302782; }
        .navbar-brand { font-weight: 800; }
        .card { border: none; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .card-header-ufpa { background-color: #302782; color: #ffffff; font-weight: 600; padding: 1rem 1.25rem; }
        .table-hover tbody tr:hover { background-color: rgba(48, 39, 130, 0.04) !important; }
        
        /* Cores específicas para as tags de Ação */
        .badge-acao-login { background-color: #0dcaf0; color: #000; }
        .badge-acao-criacao { background-color: #198754; }
        .badge-acao-anulacao { background-color: #dc3545; }
        .badge-acao-consumo { background-color: #fd7e14; }
        .badge-acao-reforco { background-color: #302782; }
    </style>
</head>
<body>

<?= view('partials/navbar') ?>

    <div class="container-fluid px-4 flex-grow-1 mb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="text-dark mb-0 fw-bold"><i class="bi bi-shield-check text-success me-2"></i>Logs de Auditoria</h3>
                <p class="text-muted mb-0">Monitoramento de atividades e controle de acesso</p>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header card-header-ufpa py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0"><i class="bi bi-list-columns-reverse me-2"></i>Histórico do Sistema (Últimos 500 registros)</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle mb-0 border-white" style="table-layout: fixed; width: 100%;">
                        <thead class="table-light fs-7 text-muted text-uppercase">
                            <tr>
                                <th style="width: 15%;" class="ps-4">Data e Hora</th>
                                <th style="width: 15%;">Usuário</th>
                                <th style="width: 12%;">Endereço IP</th>
                                <th style="width: 18%;">Ação</th>
                                <th style="width: 40%;" class="pe-4">Detalhes</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($logs)): ?>
                                <?php foreach ($logs as $log): 
                                    // Define a cor da badge dinamicamente baseada na palavra-chave da ação
                                    $acaoStr = strtolower($log->acao);
                                    $badgeClass = 'bg-secondary';
                                    
                                    if (strpos($acaoStr, 'login') !== false) $badgeClass = 'badge-acao-login';
                                    elseif (strpos($acaoStr, 'novo') !== false || strpos($acaoStr, 'criação') !== false) $badgeClass = 'badge-acao-criacao';
                                    elseif (strpos($acaoStr, 'anulação') !== false) $badgeClass = 'badge-acao-anulacao';
                                    elseif (strpos($acaoStr, 'ação') !== false || strpos($acaoStr, 'consumo') !== false) $badgeClass = 'badge-acao-consumo';
                                    elseif (strpos($acaoStr, 'reforço') !== false) $badgeClass = 'badge-acao-reforco';
                                ?>
                                    <tr>
                                        <td class="ps-4 text-muted fs-7"><?= date('d/m/Y H:i:s', strtotime($log->created_at)) ?></td>
                                        <td class="fw-bold text-dark text-truncate" title="<?= esc($log->usuario) ?>"><i class="bi bi-person-circle me-1 text-secondary"></i> <?= esc($log->usuario) ?></td>
                                        <td class="text-muted fs-7"><?= esc($log->ip_address) ?></td>
                                        <td><span class="badge <?= $badgeClass ?> rounded-pill px-2 py-1"><?= esc($log->acao) ?></span></td>
                                        <td class="text-muted text-truncate pe-4" title="<?= esc($log->detalhes) ?>"><?= esc($log->detalhes) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="5" class="text-center py-5 text-muted"><i class="bi bi-inbox fs-2 d-block mb-2"></i>Nenhum registro de auditoria encontrado.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <footer class="bg-white text-center py-3 mt-auto shadow-sm border-top">
        <div class="container text-muted" style="font-size: 0.8rem;">
            <strong class="text-dark">SGRF - Sistema de Gerenciamento de Recursos Financeiros</strong><br>
            Universidade Federal do Pará (UFPA) • PROINTER • CTIC <br>
            &copy; <?= date('Y') ?>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>