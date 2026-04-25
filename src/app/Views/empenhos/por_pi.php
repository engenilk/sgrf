<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $tituloPagina ?> - SGRF / <?= $unidadeNome ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        body { 
            background-color: #f4f6f9; 
            font-family: 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            display: flex; flex-direction: column; min-height: 100vh;
        }
        .navbar-ufpa { background-color: #751621; border-bottom: 4px solid #302782; }
        .navbar-brand { font-weight: 800; letter-spacing: -0.5px; }
        .nav-link { font-weight: 500; }
        
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
            <div class="alert alert-success alert-dismissible fade show shadow-sm rounded-3"><i class="bi bi-check-circle-fill me-2"></i><?= session()->getFlashdata('sucesso') ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('erro')): ?>
            <div class="alert alert-danger alert-dismissible fade show shadow-sm rounded-3"><i class="bi bi-exclamation-triangle-fill me-2"></i><?= session()->getFlashdata('erro') ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        <?php endif; ?>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <a href="<?= base_url('pis') ?>" class="btn btn-sm btn-outline-secondary mb-2 rounded-pill px-3"><i class="bi bi-arrow-left"></i> Voltar aos Planos Internos</a>
                <h3 class="text-dark mb-0 fw-bold">Empenhos: <?= esc($pi->codigo) ?></h3>
                <p class="text-muted mb-0"><?= esc($pi->descricao) ?> <?= $pi->programa ? ' | ' . esc($pi->programa) : '' ?></p>
            </div>
            <button class="btn btn-success shadow-sm fw-bold rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalNovoEmpenho">
                <i class="bi bi-plus-lg me-1"></i> Novo Empenho
            </button>
        </div>

        <div class="row mb-4">
            <div class="col-md-4 mb-3 mb-md-0">
                <div class="card shadow-sm border-0 border-start border-4 border-secondary h-100">
                    <div class="card-body">
                        <div class="text-muted fs-7 fw-bold text-uppercase">Crédito Alocado (Inicial)</div>
                        <div class="fs-4 fw-bold text-dark">R$ <?= number_format($pi->credito_alocado, 2, ',', '.') ?></div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3 mb-md-0">
                <div class="card shadow-sm border-0 border-start border-4" style="border-color: #302782 !important;">
                    <div class="card-body">
                        <div class="text-muted fs-7 fw-bold text-uppercase">Crédito Atual</div>
                        <div class="fs-4 fw-bold text-dark" style="color: #302782 !important;">R$ <?= number_format($pi->credito_atual, 2, ',', '.') ?></div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm border-0 border-start border-4 <?= $pi->saldo_disponivel > 0 ? 'border-success' : 'border-danger' ?> h-100">
                    <div class="card-body">
                        <div class="text-muted fs-7 fw-bold text-uppercase">Saldo Disponível no PI</div>
                        <div class="fs-4 fw-bold <?= $pi->saldo_disponivel > 0 ? 'text-success' : 'text-danger' ?>">R$ <?= number_format($pi->saldo_disponivel, 2, ',', '.') ?></div>
                    </div>
                </div>
            </div>
        </div>


        <div class="card bg-light border-0 mb-4">
            <div class="card-body p-3">
                <form method="GET" action="<?= base_url('empenhos/por_pi/' . $pi->id) ?>" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label fs-7 fw-bold text-muted mb-1"><i class="bi bi-calendar-event me-1"></i> Data Inicial</label>
                        <input type="date" name="data_inicio" class="form-control form-control-sm border-secondary-subtle" value="<?= esc($filtros['data_inicio'] ?? '') ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fs-7 fw-bold text-muted mb-1"><i class="bi bi-calendar-event-fill me-1"></i> Data Final</label>
                        <input type="date" name="data_fim" class="form-control form-control-sm border-secondary-subtle" value="<?= esc($filtros['data_fim'] ?? '') ?>">
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-sm btn-primary w-100 fw-bold shadow-sm" style="background-color: #302782; border:none;"><i class="bi bi-funnel-fill me-1"></i> Filtrar</button>
                        <?php if(!empty($filtros['data_inicio']) || !empty($filtros['data_fim'])): ?>
                            <a href="<?= base_url('empenhos/por_pi/' . $pi->id) ?>" class="btn btn-sm btn-outline-secondary shadow-sm" title="Limpar Filtros"><i class="bi bi-x-circle"></i></a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>


        <div class="card shadow-sm mb-4">
            <div class="card-header card-header-ufpa py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0"><i class="bi bi-list-check me-2"></i>Lista de Empenhos</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 border-white" style="table-layout: fixed; width: 100%;">
                        <thead class="table-light text-muted fs-7 text-uppercase">
                            <tr>
                                <th style="width: 15%;" class="ps-4">Código DFC</th>
                                <th style="width: 15%;">Processo</th>
                                <th style="width: 25%;">Objeto</th>
                                <th style="width: 15%;" class="text-end">Valor Total</th>
                                <th style="width: 10%;" class="text-center">Status</th>
                                <th style="width: 10%;" class="text-center">Progresso</th>
                                <th style="width: 10%;" class="text-center pe-4">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($empenhos)): ?>
                                <?php foreach ($empenhos as $emp): ?>
                                    <tr>
                                        <td class="ps-4 fw-bold text-dark text-truncate" title="<?= esc($emp->codigo_dfc ?? 'N/A') ?>"><?= esc($emp->codigo_dfc ?? 'N/A') ?></td>
                                        <td class="text-truncate" title="<?= esc($emp->numero_processo) ?>"><?= esc($emp->numero_processo) ?></td>
                                        <td class="text-muted text-truncate" title="<?= esc($emp->objeto) ?>"><?= esc($emp->objeto) ?></td>
                                        <td class="text-end fw-bold text-primary">R$ <?= number_format($emp->valor_total, 2, ',', '.') ?></td>
                                        <td class="text-center">
                                            <span class="badge bg-<?= $emp->status == 'Ativo' ? 'success' : ($emp->status == 'Anulado' ? 'danger' : 'secondary') ?> rounded-pill">
                                                <?= esc($emp->status) ?>
                                            </span>
                                        </td>
                                        <td class="text-center align-middle">
                                            <?php 
                                                $perc = ($emp->valor_total > 0) ? ($emp->valor_consumido / $emp->valor_total) * 100 : 0;
                                                $corBarra = $perc > 90 ? 'bg-danger' : ($perc > 75 ? 'bg-warning' : 'bg-success');
                                            ?>
                                            <div class="progress" style="height: 8px;" title="<?= number_format($perc, 1) ?>% consumido">
                                                <div class="progress-bar <?= $corBarra ?>" role="progressbar" style="width: <?= $perc ?>%;"></div>
                                            </div>
                                        </td>
                                        <td class="text-center pe-4">
                                            <a href="<?= base_url('empenhos/detalhes/' . $emp->id) ?>" class="btn btn-sm btn-ufpa-primary rounded-pill px-3 shadow-sm">
                                                Abrir
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="7" class="text-center py-5 text-muted"><i class="bi bi-inbox fs-2 d-block mb-2"></i>Nenhum empenho registrado neste PI.</td></tr>
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

 <div class="modal fade" id="modalNovoEmpenho" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header bg-success text-white rounded-top-4">
                    <h5 class="modal-title fw-bold"><i class="bi bi-file-earmark-plus me-2"></i>Reservar Novo Empenho</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="<?= base_url('empenhos/salvar') ?>" method="POST">
                    <div class="modal-body p-4">
                        <input type="hidden" name="pi_id" value="<?= $pi->id ?>">
                        
                        <div class="row g-3 mb-3">
                            <div class="col-12">
                                <label class="form-label text-muted fs-7 fw-bold">Número do Processo Principal</label>
                                <input type="text" name="numero_processo" class="form-control bg-light" value="23073." required>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label class="form-label text-muted fs-7 fw-bold">Código DFC (Opcional)</label>
                                <input type="text" name="codigo_dfc" class="form-control bg-light" placeholder="Ex: DFC-2026-001">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-muted fs-7 fw-bold">Fonte</label>
                                <input type="text" name="fonte" class="form-control bg-light" placeholder="Ex: 0112000000" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-muted fs-7 fw-bold">PTRES</label>
                                <input type="text" name="ptres" class="form-control bg-light" placeholder="Ex: 168541" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-muted fs-7 fw-bold">Objeto / Descrição</label>
                            <textarea name="objeto" class="form-control bg-light" rows="2" placeholder="Descreva a finalidade da reserva..." required></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label text-muted fs-7 fw-bold">Observações (Opcional)</label>
                            <textarea name="observacoes" class="form-control bg-light" rows="2" placeholder="Informações adicionais..."></textarea>
                        </div>

                        <div class="mb-3 bg-light p-3 border rounded-3">
                            <label class="form-label text-dark fs-6 fw-bold">Valor da Reserva</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0 fw-bold">R$</span>
                                <input type="text" id="inputValorEmpenho" name="valor_total" class="form-control border-start-0 fs-5 fw-bold text-primary" placeholder="0,00" required onkeyup="mascaraMoeda(this); validarLimite(this, <?= $pi->saldo_disponivel ?>, 'btnSalvarEmpenho')">
                            </div>
                            <div class="form-text text-success mt-2" id="hintValorEmpenho">
                                <i class="bi bi-info-circle me-1"></i> Saldo máximo permitido: <strong>R$ <?= number_format($pi->saldo_disponivel, 2, ',', '.') ?></strong>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light rounded-bottom-4">
                        <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" id="btnSalvarEmpenho" class="btn btn-success fw-bold rounded-pill px-4 shadow-sm">Confirmar Reserva</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function mascaraMoeda(input) {
            let valor = input.value.replace(/\D/g, ''); // Remove tudo que não for dígito
            if (valor === '') { input.value = ''; return; }
            valor = (valor / 100).toFixed(2) + '';
            valor = valor.replace(".", ",");
            valor = valor.replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.'); // Adiciona ponto de milhar
            input.value = valor;
        }

        function validarLimite(input, limiteMaximo, btnId) {
            let btn = document.getElementById(btnId);
            let hint = input.parentElement.nextElementSibling;
            
            // Converte '1.500,50' para float
            let valorNumerico = parseFloat(input.value.replace(/\./g, '').replace(',', '.'));
            
            if (valorNumerico > limiteMaximo) {
                input.classList.add('is-invalid');
                input.classList.add('text-danger');
                input.classList.remove('text-primary');
                hint.classList.replace('text-success', 'text-danger');
                btn.disabled = true;
            } else {
                input.classList.remove('is-invalid', 'text-danger');
                input.classList.add('text-primary');
                hint.classList.replace('text-danger', 'text-success');
                btn.disabled = false;
            }
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>