<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $tituloPagina ?> - SGRF / <?= $unidadeNome ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', Roboto, sans-serif; display: flex; flex-direction: column; min-height: 100vh; }
        .navbar-ufpa { background-color: #751621; border-bottom: 4px solid #302782; }
        .navbar-brand { font-weight: 800; }
        .card { border: none; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .card-header-ufpa { background-color: #302782; color: #ffffff; font-weight: 600; padding: 1rem 1.25rem; }
        .table-hover tbody tr:hover { background-color: rgba(48, 39, 130, 0.04) !important; }
        .chart-container { position: relative; height: 300px; width: 100%; }
    </style>
</head>
<body>

<?= view('partials/navbar') ?>

    <div class="container-fluid px-4 flex-grow-1 mb-5">
        <div class="mb-4">
            <h3 class="text-dark mb-0 fw-bold"><i class="bi bi-speedometer text-danger me-2"></i>Painel Gerencial</h3>
            <p class="text-muted mb-0">Visão panorâmica da execução orçamentária do exercício</p>
        </div>

        <div class="row mb-4 g-4">
            <div class="col-md-6">
                <div class="card shadow-sm h-100">
                    <div class="card-header card-header-ufpa bg-white text-dark border-bottom">
                        <h6 class="m-0 fw-bold"><i class="bi bi-bar-chart-fill text-primary me-2"></i>Status Global dos PIs</h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="chartPIs"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card shadow-sm h-100">
                    <div class="card-header card-header-ufpa bg-white text-dark border-bottom">
                        <h6 class="m-0 fw-bold"><i class="bi bi-bar-chart-steps text-danger me-2"></i>Execução dos Empenhos</h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="chartEmpenhos"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header card-header-ufpa py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0"><i class="bi bi-clock-history me-2"></i>Últimos Empenhos Cadastrados</h6>
                <a href="<?= base_url('pis') ?>" class="btn btn-sm btn-outline-light rounded-pill px-3">Ver Todos os PIs</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 border-white">
                        <thead class="table-light fs-7 text-muted text-uppercase">
                            <tr>
                                <th class="ps-4">Processo</th>
                                <th>PI Vinculado</th>
                                <th>Objeto</th>
                                <th class="text-end">Valor Total</th>
                                <th class="text-center">Status</th>
                                <th class="text-center pe-4">Ação</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($ultimos_empenhos)): foreach ($ultimos_empenhos as $emp): ?>
                                <tr>
                                    <td class="ps-4 fw-bold text-dark text-truncate" style="max-width: 150px;" title="<?= esc($emp->numero_processo) ?>"><?= esc($emp->numero_processo) ?></td>
                                    <td><span class="badge bg-light text-dark border"><?= esc($emp->pi_codigo) ?></span></td>
                                    <td class="text-muted text-truncate" style="max-width: 250px;" title="<?= esc($emp->objeto) ?>"><?= esc($emp->objeto) ?></td>
                                    <td class="text-end fw-bold text-primary">R$ <?= number_format($emp->valor_total, 2, ',', '.') ?></td>
                                    <td class="text-center">
                                        <span class="badge bg-<?= $emp->status == 'Ativo' ? 'success' : ($emp->status == 'Anulado' ? 'danger' : 'secondary') ?> rounded-pill">
                                            <?= esc($emp->status) ?>
                                        </span>
                                    </td>
                                    <td class="text-center pe-4">
                                        <a href="<?= base_url('empenhos/detalhes/' . $emp->id) ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3 shadow-sm" style="color: #302782; border-color: #302782;">
                                            Acessar <i class="bi bi-arrow-right-short"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; else: ?>
                                <tr><td colspan="6" class="text-center py-4 text-muted">Nenhum empenho registrado ainda.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <footer class="bg-white text-center py-3 mt-auto shadow-sm border-top">
        <div class="container text-muted" style="font-size: 0.8rem;">
            <strong class="text-dark">SGRF</strong><br>Universidade Federal do Pará (UFPA) • PROINTER • CTIC<br>&copy; <?= date('Y') ?>
        </div>
    </footer>

    <script>
        // Cores institucionais para os gráficos
        const corUFPA_Azul = 'rgba(48, 39, 130, 0.85)';
        const corUFPA_Bordo = 'rgba(117, 22, 33, 0.85)';
        const corSucesso = 'rgba(25, 135, 84, 0.85)';
        const corAlerta = 'rgba(255, 193, 7, 0.85)';
        const corFundo = 'rgba(233, 236, 239, 0.8)';

        // Dados vindos do PHP
        const piConsumido = <?= $pi_consumido ?>;
        const piDisponivel = <?= $pi_disponivel ?>;
        
        const empConsumido = <?= $emp_consumido ?>;
        const empDisponivel = <?= $emp_disponivel ?>; // Saldo que ainda pode ser gasto no empenho

        // Gráfico de PIs (Stacked Bar: Consumido vs Disponível)
        new Chart(document.getElementById('chartPIs'), {
            type: 'bar',
            data: {
                labels: ['Orçamento Total Aprovado (PIs)'],
                datasets: [
                    {
                        label: 'Valor Comprometido (R$)',
                        data: [piConsumido],
                        backgroundColor: corUFPA_Bordo,
                        borderRadius: {topLeft: 0, topRight: 0, bottomLeft: 4, bottomRight: 4}
                    },
                    {
                        label: 'Saldo Livre Disponível (R$)',
                        data: [piDisponivel],
                        backgroundColor: corSucesso,
                        borderRadius: {topLeft: 4, topRight: 4, bottomLeft: 0, bottomRight: 0}
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: { stacked: true },
                    y: { stacked: true, beginAtZero: true }
                },
                plugins: {
                    tooltip: { callbacks: { label: function(context) { return context.dataset.label + ': R$ ' + context.parsed.y.toLocaleString('pt-BR', {minimumFractionDigits: 2}); } } }
                }
            }
        });

        // Gráfico de Empenhos (Barra Dupla Lado a Lado)
        new Chart(document.getElementById('chartEmpenhos'), {
            type: 'bar',
            data: {
                labels: ['Execução de Empenhos'],
                datasets: [
                    {
                        label: 'Total Reservado (R$)',
                        data: [<?= $emp_total ?>],
                        backgroundColor: corUFPA_Azul,
                        borderRadius: 4
                    },
                    {
                        label: 'Total Consumido/Pago (R$)',
                        data: [empConsumido],
                        backgroundColor: corAlerta,
                        borderRadius: 4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true }
                },
                plugins: {
                    tooltip: { callbacks: { label: function(context) { return context.dataset.label + ': R$ ' + context.parsed.y.toLocaleString('pt-BR', {minimumFractionDigits: 2}); } } }
                }
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>