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
        .nav-tabs .nav-link { color: #495057; font-weight: 500; }
        .nav-tabs .nav-link.active { color: #302782; font-weight: bold; border-top: 3px solid #302782; }
        .tab-content { background: #fff; border: 1px solid #dee2e6; border-top: none; border-radius: 0 0 12px 12px; padding: 1.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .table-hover tbody tr:hover { background-color: rgba(48, 39, 130, 0.04) !important; }
        
        @media print {
            @page { size: landscape; margin: 10mm; }
            body { background: white !important; -webkit-print-color-adjust: exact; }
            .d-print-none, .navbar, footer { display: none !important; }
            .tab-content { border: none !important; padding: 0 !important; box-shadow: none !important; }
            .table th, .table td { font-size: 11px !important; padding: 6px !important; }
        }
    </style>
</head>
<body>

<?= view('partials/navbar') ?>

    <div class="container-fluid px-4 flex-grow-1 mb-5">
        <div class="d-flex justify-content-between align-items-center mb-4 d-print-none">
            <div>
                <h3 class="text-dark mb-0 fw-bold"><?= $tituloPagina ?></h3>
                <p class="text-muted mb-0">Posição financeira atualizada e auditoria contábil</p>
            </div>
            <button class="btn btn-outline-secondary rounded-pill shadow-sm px-4 fw-bold" onclick="window.print()">
                <i class="bi bi-printer me-1"></i> Imprimir
            </button>
        </div>

        <ul class="nav nav-tabs d-print-none border-0 mb-0" id="relatorioTabs" role="tablist">
            <li class="nav-item"><button class="nav-link active rounded-top-3 border" data-bs-toggle="tab" data-bs-target="#consolidado">Visão Consolidada</button></li>
            <li class="nav-item ms-1"><button class="nav-link rounded-top-3 border" data-bs-toggle="tab" data-bs-target="#razao">Razão Contábil (Extrato)</button></li>
        </ul>

        <div class="tab-content" id="relatorioTabsContent">
            
            <div class="tab-pane fade show active" id="consolidado">
                <h5 class="mb-3" style="color: #302782;"><i class="bi bi-pie-chart-fill me-2"></i>Resumo Executivo por Plano Interno</h5>
                <div class="table-responsive">
                    <table class="table table-hover align-middle border" style="table-layout: fixed; width: 100%;">
                        <thead class="table-light text-muted fs-7 text-uppercase">
                            <tr>
                                <th style="width: 15%;">Código do PI</th>
                                <th style="width: 15%;" class="text-end">Crédito Alocado</th>
                                <th style="width: 15%;" class="text-end">Crédito Atual</th>
                                <th style="width: 15%;" class="text-end">Total Empenhado</th>
                                <th style="width: 15%;" class="text-end">Total Executado</th>
                                <th style="width: 15%;" class="text-end">Saldo Disponível</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                                $totAlocado = 0; $totAtual = 0; $totEmpenhado = 0; $totExecutado = 0; $totDisponivel = 0;
                                if (!empty($pis_consolidados)): 
                                    foreach ($pis_consolidados as $pi): 
                                        $saldoCalculado = $pi->credito_atual - $pi->total_empenhado;
                                        
                                        $totAlocado += $pi->credito_alocado; $totAtual += $pi->credito_atual;
                                        $totEmpenhado += $pi->total_empenhado; $totExecutado += $pi->total_executado;
                                        $totDisponivel += $saldoCalculado;
                            ?>
                                <tr>
                                    <td class="fw-bold text-dark text-truncate" title="<?= esc($pi->descricao) ?>"><?= esc($pi->codigo) ?></td>
                                    <td class="text-end text-muted">R$ <?= number_format($pi->credito_alocado, 2, ',', '.') ?></td>
                                    <td class="text-end text-secondary">R$ <?= number_format($pi->credito_atual, 2, ',', '.') ?></td>
                                    <td class="text-end text-warning fw-bold">R$ <?= number_format($pi->total_empenhado, 2, ',', '.') ?></td>
                                    <td class="text-end text-danger fw-bold">R$ <?= number_format($pi->total_executado, 2, ',', '.') ?></td>
                                    <td class="text-end text-success fw-bold">R$ <?= number_format($saldoCalculado, 2, ',', '.') ?></td>
                                </tr>
                            <?php endforeach; else: ?>
                                <tr><td colspan="6" class="text-center py-3">Nenhum dado encontrado.</td></tr>
                            <?php endif; ?>
                        </tbody>
                        <tfoot class="table-dark">
                            <tr>
                                <td class="text-end fw-bold">TOTAIS GERAIS:</td>
                                <td class="text-end fw-bold">R$ <?= number_format($totAlocado, 2, ',', '.') ?></td>
                                <td class="text-end fw-bold">R$ <?= number_format($totAtual, 2, ',', '.') ?></td>
                                <td class="text-end fw-bold text-warning">R$ <?= number_format($totEmpenhado, 2, ',', '.') ?></td>
                                <td class="text-end fw-bold text-danger">R$ <?= number_format($totExecutado, 2, ',', '.') ?></td>
                                <td class="text-end fw-bold text-info">R$ <?= number_format($totDisponivel, 2, ',', '.') ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="tab-pane fade" id="razao">
                
                <div class="card bg-light border-0 mb-4 d-print-none">
                    <div class="card-body p-3">
                        <form method="GET" action="<?= base_url('relatorios') ?>" class="row g-3 align-items-end">
                            <div class="col-md-2">
                                <label class="form-label fs-7 fw-bold text-muted mb-1">Data Início</label>
                                <input type="date" name="data_inicio" class="form-control form-control-sm border-secondary-subtle" value="<?= esc($filtros['data_inicio'] ?? '') ?>">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fs-7 fw-bold text-muted mb-1">Data Fim</label>
                                <input type="date" name="data_fim" class="form-control form-control-sm border-secondary-subtle" value="<?= esc($filtros['data_fim'] ?? '') ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fs-7 fw-bold text-muted mb-1">Filtrar por PI</label>
                                <select name="pi_id" class="form-select form-select-sm border-secondary-subtle">
                                    <option value="">Todos os PIs...</option>
                                    <?php foreach ($lista_pis as $piSelect): ?>
                                        <option value="<?= $piSelect->id ?>" <?= ($filtros['pi_id'] == $piSelect->id) ? 'selected' : '' ?>><?= esc($piSelect->codigo) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fs-7 fw-bold text-muted mb-1">Filtrar por Empenho</label>
                                <select name="empenho_id" class="form-select form-select-sm border-secondary-subtle">
                                    <option value="">Todos os Empenhos...</option>
                                    <?php foreach ($lista_empenhos as $empSelect): ?>
                                        <option value="<?= $empSelect->id ?>" <?= ($filtros['empenho_id'] == $empSelect->id) ? 'selected' : '' ?>><?= esc($empSelect->numero_processo) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-2 d-flex gap-2">
                                <button type="submit" class="btn btn-sm btn-primary w-100 fw-bold shadow-sm" style="background-color: #302782; border:none;"><i class="bi bi-funnel-fill me-1"></i> Filtrar</button>
                                <?php if(array_filter($filtros)): ?>
                                    <a href="<?= base_url('relatorios') ?>" class="btn btn-sm btn-outline-secondary shadow-sm" title="Limpar Filtros"><i class="bi bi-x-circle"></i></a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>

                <h5 class="mb-3 d-flex justify-content-between align-items-center" style="color: #302782;">
                    <span><i class="bi bi-list-columns-reverse me-2"></i>Livro Razão (Movimentações)</span>
                    <span class="badge bg-secondary fs-7 rounded-pill"><?= count($movimentacoes) ?> registro(s)</span>
                </h5>

                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle border fs-7" style="table-layout: fixed; width: 100%;">
                        <thead class="table-light text-muted text-uppercase">
                            <tr>
                                <th style="width: 12%;">Data/Hora</th>
                                <th style="width: 18%;">Operação</th>
                                <th style="width: 25%;">Origem <i class="bi bi-arrow-right"></i> Destino</th>
                                <th style="width: 30%;">Justificativa / Empenho</th>
                                <th style="width: 15%;" class="text-end">Valor (R$)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($movimentacoes)): foreach ($movimentacoes as $mov): 
                                
                                // Lógica para colorir a tag de operação
                                $corTag = 'bg-secondary';
                                if ($mov->tipo_operacao === 'Aporte_Inicial') $corTag = 'bg-success';
                                elseif ($mov->tipo_operacao === 'Novo_Empenho') $corTag = 'bg-primary';
                                elseif ($mov->tipo_operacao === 'Reforco_Empenho') $corTag = 'bg-info text-dark';
                                elseif ($mov->tipo_operacao === 'Anulacao_Empenho') $corTag = 'bg-danger';
                                elseif ($mov->tipo_operacao === 'Transferencia') $corTag = 'bg-warning text-dark';
                            ?>
                                <tr>
                                    <td class="text-muted"><?= date('d/m/Y H:i', strtotime($mov->created_at)) ?></td>
                                    
                                    <td><span class="badge <?= $corTag ?> fw-bold px-2 py-1"><?= esc(str_replace('_', ' ', $mov->tipo_operacao)) ?></span></td>
                                    
                                    <td class="text-truncate">
                                        <?= $mov->pi_origem ? '<span class="text-danger fw-bold">'.esc($mov->pi_origem).'</span>' : '<span class="text-muted">Externo</span>' ?>
                                        <i class="bi bi-arrow-right mx-1 text-muted"></i>
                                        <?= $mov->pi_destino ? '<span class="text-success fw-bold">'.esc($mov->pi_destino).'</span>' : '<span class="text-muted">Saída</span>' ?>
                                    </td>
                                    
                                    <td class="text-truncate" title="<?= esc($mov->justificativa_historico) ?>">
                                        <?= esc($mov->justificativa_historico) ?>
                                        <?php if($mov->empenho_processo): ?>
                                            <br><small class="text-muted"><i class="bi bi-file-earmark-text me-1"></i><?= esc($mov->empenho_processo) ?></small>
                                        <?php endif; ?>
                                    </td>
                                    
                                    <td class="text-end fw-bold <?= $mov->pi_destino ? 'text-primary' : 'text-danger' ?>">
                                        <?= number_format($mov->valor, 2, ',', '.') ?>
                                    </td>
                                </tr>
                            <?php endforeach; else: ?>
                                <tr><td colspan="5" class="text-center py-5 text-muted"><i class="bi bi-search fs-2 d-block mb-2"></i>Nenhuma movimentação encontrada para os filtros selecionados.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <footer class="bg-white text-center py-3 mt-auto shadow-sm border-top d-print-none">
        <div class="container text-muted" style="font-size: 0.8rem;">
            <strong class="text-dark">SGRF</strong><br>Universidade Federal do Pará (UFPA) • PROINTER • CTIC<br>&copy; <?= date('Y') ?>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            if (window.location.search.includes('data_inicio') || window.location.search.includes('pi_id')) {
                var triggerEl = document.querySelector('button[data-bs-target="#razao"]');
                var tab = new bootstrap.Tab(triggerEl);
                tab.show();
            }
        });
    </script>
</body>
</html>