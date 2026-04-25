<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $tituloPagina ?> - SGRF / <?= $unidadeNome ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        /* Tipografia e Fundo Suavizados */
        body { 
            background-color: #f4f6f9; 
            font-family: 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            display: flex; flex-direction: column; min-height: 100vh;
        }
        /* Cores Institucionais UFPA */
        .navbar-ufpa { background-color: #751621; border-bottom: 4px solid #302782; }
        .navbar-brand { font-weight: 800; letter-spacing: -0.5px; }
        .nav-link { font-weight: 500; }
        
        /* Arredondamento e Sombras (Card UI) */
        .card { border: none; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); overflow: hidden; }
        .card-header-ufpa { background-color: #302782; color: #ffffff; font-weight: 600; padding: 1rem 1.25rem; }
        
        .table-hover tbody tr:hover { background-color: rgba(48, 39, 130, 0.04) !important; transition: all 0.2s; }
        .btn-ufpa-primary { background-color: #302782; color: white; border: none; }
        .btn-ufpa-primary:hover { background-color: #241d63; color: white; }
    </style>
</head>
<body>

<?= view('partials/navbar') ?>

    <div class="container-fluid px-4 flex-grow-1 mb-5">
        <?php if (session()->getFlashdata('sucesso')): ?>
            <div class="alert alert-success alert-dismissible fade show shadow-sm rounded-3" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> <?= session()->getFlashdata('sucesso') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('erro')): ?>
            <div class="alert alert-danger alert-dismissible fade show shadow-sm rounded-3" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= session()->getFlashdata('erro') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="text-dark mb-1 fw-bold">Planos Internos</h3>
                <p class="text-muted mb-0">Gestão e alocação orçamentária do exercício</p>
            </div>
            <div>
                <button class="btn btn-warning shadow-sm me-2 text-dark fw-bold rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalTransferencia">
                    <i class="bi bi-arrow-left-right me-1"></i> Transferir
                </button>
                <button class="btn btn-success shadow-sm fw-bold rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalNovoPi">
                    <i class="bi bi-plus-lg me-1"></i> Novo PI
                </button>
            </div>
        </div>

        <?php if (!empty($pisAgrupados)): ?>
            <?php foreach ($pisAgrupados as $programa => $listaPis): ?>
                
                <div class="card mb-4">
                    <div class="card-header card-header-ufpa d-flex justify-content-between align-items-center">
                        <span class="fs-5"><i class="bi bi-bookmark-fill me-2 text-warning"></i> Programa: <?= esc($programa) ?></span>
                        <span class="badge bg-light text-dark rounded-pill"><?= count($listaPis) ?> PI(s)</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 border-white" style="table-layout: fixed; width: 100%;">
                                <thead class="table-light text-muted fs-7 text-uppercase">
                                    <tr>
                                        <th style="width: 18%;" class="ps-4">Código</th>
                                        <th style="width: 26%;">Descrição</th>
                                        <th style="width: 16%;" class="text-end">Crédito Alocado</th>
                                        <th style="width: 16%;" class="text-end">Crédito Atual</th>
                                        <th style="width: 14%;" class="text-end">Saldo Disp.</th>
                                        <th style="width: 10%;" class="text-center pe-4">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($listaPis as $pi): ?>
                                        <tr>
                                            <td class="ps-4 fw-bold text-dark text-truncate" title="<?= esc($pi->codigo) ?>"><?= esc($pi->codigo) ?></td>
                                            <td class="text-muted text-truncate" title="<?= esc($pi->descricao) ?>"><?= esc($pi->descricao) ?></td>
                                            <td class="text-end text-muted">R$ <?= number_format($pi->credito_alocado, 2, ',', '.') ?></td>
                                            <td class="text-end text-secondary">R$ <?= number_format($pi->credito_atual, 2, ',', '.') ?></td>
                                            <?php 
                                                $classeSaldo = 'text-success';
                                                if ($pi->saldo_disponivel <= 0) $classeSaldo = 'text-danger';
                                                elseif ($pi->saldo_disponivel < ($pi->credito_atual * 0.1)) $classeSaldo = 'text-warning';
                                            ?>
                                            <td class="text-end fw-bold fs-6 <?= $classeSaldo ?>">
                                                R$ <?= number_format($pi->saldo_disponivel, 2, ',', '.') ?>
                                            </td>
                                            <td class="text-center pe-4">
                                                <a href="<?= base_url('empenhos/por_pi/' . $pi->id) ?>" class="btn btn-sm btn-ufpa-primary rounded-pill px-3 shadow-sm">
                                                    Gerenciar <i class="bi bi-arrow-right-short"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            <?php endforeach; ?>
        <?php else: ?>
            <div class="card p-5 text-center shadow-sm">
                <div class="text-muted">
                    <i class="bi bi-folder-x fs-1 d-block mb-3"></i>
                    Nenhum Plano Interno cadastrado no sistema.
                </div>
            </div>
        <?php endif; ?>
    </div>

    <footer class="bg-white text-center py-3 mt-auto shadow-sm border-top">
        <div class="container text-muted" style="font-size: 0.8rem;">
            <strong class="text-dark">SGRF - Sistema de Gerenciamento de Recursos Financeiros</strong><br>
            Universidade Federal do Pará (UFPA) • PROINTER • CTIC <br>
            &copy; <?= date('Y') ?>
        </div>
    </footer>

    <div class="modal fade" id="modalNovoPi" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header bg-success text-white rounded-top-4">
                    <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle me-2"></i>Cadastrar Novo PI</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="<?= base_url('pis/salvar') ?>" method="POST">
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label text-muted fs-7 fw-bold">Código do PI</label>
                            <input type="text" name="codigo" class="form-control bg-light" placeholder="Ex: PI-2026-01" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted fs-7 fw-bold">Programa (Opcional)</label>
                            <input type="text" name="programa" class="form-control bg-light" placeholder="Ex: 32 - Idioma sem Fronteiras">
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted fs-7 fw-bold">Descrição</label>
                            <input type="text" name="descricao" class="form-control bg-light" placeholder="Ex: Custeio Administrativo" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted fs-7 fw-bold">Crédito Alocado / Aporte Inicial (R$)</label>
                            <input type="number" step="0.01" min="0" name="credito_alocado" class="form-control bg-light" value="0.00" required>
                            <div class="form-text text-muted"><i class="bi bi-info-circle me-1"></i> Este valor inicial formará o Crédito Atual e o Saldo Disponível.</div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light rounded-bottom-4">
                        <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success fw-bold rounded-pill px-4 shadow-sm">Salvar PI</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>