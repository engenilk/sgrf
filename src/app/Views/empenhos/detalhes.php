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
        .info-label { font-size: 0.75rem; color: #6c757d; text-transform: uppercase; font-weight: 700; margin-bottom: 0.1rem; }
        .info-value { font-size: 1rem; font-weight: 500; color: #212529; }
        .table-hover tbody tr:hover { background-color: rgba(48, 39, 130, 0.04) !important; }
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

        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <a href="<?= base_url('empenhos/por_pi/' . $pi->id) ?>" class="btn btn-sm btn-outline-secondary mb-2 rounded-pill px-3"><i class="bi bi-arrow-left"></i> Voltar</a>
                <h3 class="text-dark mb-0 fw-bold">Empenho: <?= esc($empenho->numero_processo) ?></h3>
                <p class="text-muted">PI Origem: <strong><?= esc($pi->codigo) ?></strong></p>
            </div>
            <?php if ($empenho->status === 'Ativo'): ?>
                <div class="d-flex gap-2">
                    <button class="btn btn-primary rounded-pill shadow-sm px-4 fw-bold" style="background-color: #302782; border:none;" data-bs-toggle="modal" data-bs-target="#modalReforco">Reforçar</button>
                    <button class="btn btn-danger rounded-pill shadow-sm px-4 fw-bold" data-bs-toggle="modal" data-bs-target="#modalAnulacao">Anular</button>
                </div>
            <?php endif; ?>
        </div>

        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-5 border-end">
                        <div class="row g-3">
                            <div class="col-6"><div class="info-label">Cód DFC</div><div class="info-value text-primary fw-bold"><?= esc($empenho->codigo_dfc ?? 'N/A') ?></div></div>
                            <div class="col-6"><div class="info-label">Status</div><span class="badge bg-<?= $empenho->status == 'Ativo' ? 'success' : ($empenho->status == 'Anulado' ? 'danger' : 'secondary') ?>"><?= esc($empenho->status) ?></span></div>
                            <div class="col-6"><div class="info-label">PTRES</div><div class="info-value"><?= esc($empenho->ptres ?? 'N/A') ?></div></div>
                            <div class="col-6"><div class="info-label">Fonte</div><div class="info-value"><?= esc($empenho->fonte ?? 'N/A') ?></div></div>
                            <div class="col-12"><div class="info-label">Objeto</div><div class="info-value text-muted"><?= esc($empenho->objeto) ?></div></div>
                        </div>
                    </div>
                    
                    <div class="col-md-7 ps-md-4">
                        <div class="row mt-3 mt-md-0">
                            <div class="col-sm-4 text-center">
                                <div class="info-label">Valor Total (Reserva)</div>
                                <div class="info-value text-secondary fs-5">R$ <?= number_format($empenho->valor_total, 2, ',', '.') ?></div>
                            </div>
                            <div class="col-sm-4 text-center">
                                <div class="info-label">Consumido</div>
                                <div class="info-value text-danger fs-5">R$ <?= number_format($empenho->valor_consumido, 2, ',', '.') ?></div>
                            </div>
                            <div class="col-sm-4 text-center bg-light rounded-3 p-2">
                                <?php $saldo = $empenho->valor_total - $empenho->valor_consumido; ?>
                                <div class="info-label text-success">Saldo Disponível</div>
                                <div class="info-value text-success fw-bold fs-4">R$ <?= number_format($saldo, 2, ',', '.') ?></div>
                            </div>
                        </div>
                        <?php $percentual = ($empenho->valor_total > 0) ? ($empenho->valor_consumido / $empenho->valor_total) * 100 : 0; ?>
                        <div class="progress mt-4 rounded-pill" style="height: 10px;">
                            <div class="progress-bar bg-<?= $percentual > 90 ? 'danger' : ($percentual > 75 ? 'warning' : 'success') ?>" role="progressbar" style="width: <?= $percentual ?>%;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header card-header-ufpa py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0"><i class="bi bi-wallet2 me-2"></i>Ações e Pagamentos Registrados</h6>
                <button class="btn btn-sm btn-light text-dark fw-bold shadow-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalNovaAcao" <?= $empenho->status !== 'Ativo' ? 'disabled' : '' ?>>
                    <i class="bi bi-plus-lg me-1"></i> Lançar Ação
                </button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 border-white" style="table-layout: fixed; width: 100%;">
                        <thead class="table-light fs-7 text-muted text-uppercase">
                            <tr>
                                <th style="width: 15%;" class="ps-4">Data Envio</th>
                                <th style="width: 20%;">Processo Pgto</th>
                                <th style="width: 25%;">Beneficiário</th>
                                <th style="width: 25%;">Tipo Pagamento / Ref</th>
                                <th style="width: 15%;" class="text-end pe-4">Valor (R$)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($acoes)): ?>
                                <?php foreach ($acoes as $acao): ?>
                                    <tr>
                                        <td class="ps-4 text-muted"><?= $acao->data_envio ? date('d/m/Y', strtotime($acao->data_envio)) : date('d/m/Y', strtotime($acao->created_at)) ?></td>
                                        <td class="fw-bold text-dark text-truncate" title="<?= esc($acao->processo ?? 'N/A') ?>"><?= esc($acao->processo ?? 'N/A') ?></td>
                                        <td class="text-truncate" title="<?= esc($acao->beneficiario) ?>"><?= esc($acao->beneficiario) ?></td>
                                        <td class="text-muted fs-7 text-truncate">
                                            <span class="badge bg-light text-dark border me-1"><?= esc($acao->tipo_pagamento ?? 'N/D') ?></span>
                                            <?= esc($acao->referencia ?? '') ?>
                                        </td>
                                        <td class="text-end pe-4 text-danger fw-bold">- <?= number_format($acao->valor, 2, ',', '.') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="5" class="text-center py-4 text-muted">Nenhuma ação registrada.</td></tr>
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

    <div class="modal fade" id="modalNovaAcao" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header bg-light rounded-top-4">
                    <h5 class="modal-title fw-bold text-dark"><i class="bi bi-wallet2 me-2"></i>Registrar Pagamento / Ação</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="<?= base_url('acoes/registrar') ?>" method="POST">
                    <div class="modal-body p-4">
                        <input type="hidden" name="empenho_id" value="<?= $empenho->id ?>">
                        
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label text-muted fs-7 fw-bold">Processo de Pagamento</label>
                                <input type="text" name="processo" class="form-control bg-light" value="23073." required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted fs-7 fw-bold">Lista de Credor (LC) (Opcional)</label>
                                <input type="text" name="lista_credor" class="form-control bg-light" placeholder="Ex: LC2026001">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-muted fs-7 fw-bold">Referência</label>
                            <input type="text" name="referencia" class="form-control bg-light" placeholder="Ex: Referente ao mês de janeiro" required>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label text-muted fs-7 fw-bold">Tipo de Pagamento</label>
                                <select name="tipo_pagamento" class="form-select bg-light" required>
                                    <option value="">Selecione...</option>
                                    <option value="Ordem bancária">Ordem Bancária</option>
                                    <option value="Crédito em Conta">Crédito em Conta</option>
                                    <option value="Transferência Internacional">Transferência Internacional</option>
                                    <option value="Outros">Outros</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted fs-7 fw-bold">Data de Envio</label>
                                <input type="date" name="data_envio" class="form-control bg-light" value="<?= date('Y-m-d') ?>" required>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label text-muted fs-7 fw-bold">Beneficiário</label>
                                <input type="text" name="beneficiario" class="form-control bg-light" placeholder="Nome da Empresa/Pessoa" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted fs-7 fw-bold">Descrição do Pagamento</label>
                                <input type="text" name="descricao" class="form-control bg-light" placeholder="Ex: Pagamento da Fatura 123" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label text-muted fs-7 fw-bold">Observações (Opcional)</label>
                            <textarea name="observacoes" class="form-control bg-light" rows="2"></textarea>
                        </div>

                        <div class="mb-3 bg-light p-3 border rounded-3">
                            <label class="form-label text-dark fs-6 fw-bold">Valor a Pagar</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0 fw-bold">R$</span>
                                <input type="text" name="valor" class="form-control border-start-0 fs-5 fw-bold text-danger" placeholder="0,00" required onkeyup="mascaraMoeda(this); validarLimite(this, <?= $saldo ?>, 'btnSalvarAcao')">
                            </div>
                            <div class="form-text text-success mt-2">
                                <i class="bi bi-info-circle me-1"></i> Saldo máximo do empenho: <strong>R$ <?= number_format($saldo, 2, ',', '.') ?></strong>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light rounded-bottom-4">
                        <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" id="btnSalvarAcao" class="btn btn-dark fw-bold rounded-pill px-4 shadow-sm" style="background-color: #302782; border:none;">Confirmar Pagamento</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalReforco" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content border-0 shadow rounded-4">
                <div class="modal-header text-white rounded-top-4" style="background-color: #302782;">
                    <h5 class="modal-title"><i class="bi bi-arrow-up-circle me-2"></i>Reforçar Empenho</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="<?= base_url('empenhos/reforcar/' . $empenho->id) ?>" method="POST">
                    <div class="modal-body p-4">
                        <p class="text-muted mb-4">O valor do reforço será deduzido do limite do Plano Interno.</p>
                        <div class="mb-3">
                            <label class="form-label text-muted fs-7 fw-bold">Valor do Reforço</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0 fw-bold">R$</span>
                                <input type="text" name="valor_reforco" class="form-control border-start-0 fw-bold text-primary" placeholder="0,00" required onkeyup="mascaraMoeda(this); validarLimite(this, <?= $pi->saldo_disponivel ?>, 'btnReforcar')">
                            </div>
                            <div class="form-text text-primary mt-1">Saldo disponível no PI: <strong>R$ <?= number_format($pi->saldo_disponivel, 2, ',', '.') ?></strong></div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light rounded-bottom-4">
                        <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" id="btnReforcar" class="btn btn-primary rounded-pill px-4" style="background-color: #302782; border:none;">Aplicar Reforço</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalAnulacao" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content border-0 shadow rounded-4">
                <div class="modal-header bg-danger text-white rounded-top-4">
                    <h5 class="modal-title"><i class="bi bi-exclamation-triangle-fill me-2"></i>Anular Empenho</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="<?= base_url('empenhos/anular/' . $empenho->id) ?>" method="POST">
                    <div class="modal-body p-4">
                        <p class="text-muted mb-4">Você pode realizar uma anulação parcial ou total do saldo não consumido.</p>
                        <div class="mb-3">
                            <label class="form-label text-muted fs-7 fw-bold">Valor a Anular</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0 fw-bold">R$</span>
                                <input type="text" name="valor_anulacao" class="form-control border-start-0 fw-bold text-danger" placeholder="0,00" required onkeyup="mascaraMoeda(this); validarLimite(this, <?= $saldo ?>, 'btnAnular')">
                            </div>
                            <div class="form-text text-danger mt-1">Saldo máximo passível de anulação: <strong>R$ <?= number_format($saldo, 2, ',', '.') ?></strong></div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light rounded-bottom-4">
                        <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Voltar</button>
                        <button type="submit" id="btnAnular" class="btn btn-danger rounded-pill px-4">Confirmar Anulação</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Função para formatar moeda brasileira em tempo real
        function mascaraMoeda(input) {
            let valor = input.value.replace(/\D/g, ''); 
            if (valor === '') { input.value = ''; return; }
            valor = (valor / 100).toFixed(2) + '';
            valor = valor.replace(".", ",");
            valor = valor.replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.'); 
            input.value = valor;
        }

        // Função para travar o formulário se passar do limite
        function validarLimite(input, limiteMaximo, btnId) {
            let btn = document.getElementById(btnId);
            let hint = input.parentElement.nextElementSibling;
            
            // Converte a string mascarada para número (ex: "1.500,50" -> 1500.50)
            let valorNumerico = parseFloat(input.value.replace(/\./g, '').replace(',', '.'));
            
            if (valorNumerico > limiteMaximo) {
                input.classList.add('is-invalid');
                hint.classList.replace('text-success', 'text-danger');
                hint.classList.replace('text-primary', 'text-danger');
                btn.disabled = true;
            } else {
                input.classList.remove('is-invalid');
                hint.classList.replace('text-danger', 'text-success');
                hint.classList.replace('text-danger', 'text-primary');
                btn.disabled = false;
            }
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>