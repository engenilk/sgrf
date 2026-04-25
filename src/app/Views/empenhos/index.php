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
        .btn-ufpa-primary { background-color: #302782; color: white; border: none; }
        .btn-ufpa-primary:hover { background-color: #241d63; color: white; }
    </style>
</head>
<body>

<?= view('partials/navbar') ?>

    <div class="container-fluid px-4 flex-grow-1 mb-5">
        
        <div class="mb-4">
            <h3 class="text-dark mb-0 fw-bold"><i class="bi bi-file-earmark-text text-primary me-2"></i>Visão Geral de Empenhos</h3>
            <p class="text-muted mb-0">Listagem de todas as reservas orçamentárias agrupadas por Plano Interno</p>
        </div>

        <?php if (!empty($empenhosAgrupados)): ?>
            <?php foreach ($empenhosAgrupados as $nomePi => $listaEmpenhos): ?>
                
                <div class="card shadow-sm mb-4 border-0">
                    <div class="card-header card-header-ufpa d-flex justify-content-between align-items-center">
                        <span class="fs-6"><i class="bi bi-folder-fill me-2 text-warning"></i> PI: <?= esc($nomePi) ?></span>
                        <span class="badge bg-light text-dark rounded-pill"><?= count($listaEmpenhos) ?> Empenho(s)</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 border-white" style="table-layout: fixed; width: 100%;">
                                <thead class="table-light text-muted fs-7 text-uppercase">
                                    <tr>
                                        <th style="width: 18%;" class="ps-4">Processo / DFC</th>
                                        <th style="width: 25%;">Objeto (Descrição)</th>
                                        <th style="width: 15%;" class="text-end">Valor Total</th>
                                        <th style="width: 15%;" class="text-end">Saldo Disponível</th>
                                        <th style="width: 12%;" class="text-center">Progresso</th>
                                        <th style="width: 15%;" class="text-center pe-4">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($listaEmpenhos as $emp): ?>
                                        <tr>
                                            <td class="ps-4">
                                                <div class="fw-bold text-dark text-truncate" title="<?= esc($emp->numero_processo) ?>"><?= esc($emp->numero_processo) ?></div>
                                                <div class="text-muted fs-7 text-truncate" title="<?= esc($emp->codigo_dfc ?? '') ?>"><?= esc($emp->codigo_dfc ?? 'Sem DFC') ?></div>
                                            </td>
                                            <td class="text-muted text-truncate" title="<?= esc($emp->objeto) ?>"><?= esc($emp->objeto) ?></td>
                                            <td class="text-end fw-bold text-secondary">R$ <?= number_format($emp->valor_total, 2, ',', '.') ?></td>
                                            
                                            <?php 
                                                $saldo = $emp->valor_total - $emp->valor_consumido;
                                                $classeSaldo = 'text-success';
                                                if ($saldo <= 0) $classeSaldo = 'text-danger';
                                                elseif ($saldo < ($emp->valor_total * 0.1)) $classeSaldo = 'text-warning';
                                            ?>
                                            <td class="text-end fw-bold fs-6 <?= $classeSaldo ?>">
                                                R$ <?= number_format($saldo, 2, ',', '.') ?>
                                            </td>

                                            <td class="text-center align-middle">
                                                <?php 
                                                    $perc = ($emp->valor_total > 0) ? ($emp->valor_consumido / $emp->valor_total) * 100 : 0;
                                                    $corBarra = $perc > 90 ? 'bg-danger' : ($perc > 75 ? 'bg-warning' : 'bg-success');
                                                ?>
                                                <div class="progress rounded-pill shadow-sm" style="height: 8px;" title="<?= number_format($perc, 1) ?>% consumido">
                                                    <div class="progress-bar <?= $corBarra ?>" role="progressbar" style="width: <?= $perc ?>%;"></div>
                                                </div>
                                            </td>

                                            <td class="text-center pe-4">
                                                <a href="<?= base_url('empenhos/detalhes/' . $emp->id) ?>" class="btn btn-sm btn-ufpa-primary rounded-pill px-3 shadow-sm">
                                                    Abrir <i class="bi bi-box-arrow-in-right"></i>
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
            <div class="card p-5 text-center shadow-sm border-0">
                <div class="text-muted">
                    <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                    Nenhum empenho cadastrado no sistema no momento.
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>