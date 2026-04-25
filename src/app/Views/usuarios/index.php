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
                <h3 class="text-dark mb-0 fw-bold"><i class="bi bi-people-fill text-primary me-2"></i>Controle de Acesso</h3>
                <p class="text-muted mb-0">Gerencie quem pode acessar e operar o sistema</p>
            </div>
            <button class="btn btn-success shadow-sm fw-bold rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalNovoUsuario">
                <i class="bi bi-person-plus-fill me-1"></i> Novo Usuário
            </button>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header card-header-ufpa py-3">
                <h6 class="m-0"><i class="bi bi-list-check me-2"></i>Usuários Cadastrados</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 border-white">
                        <thead class="table-light fs-7 text-muted text-uppercase">
                            <tr>
                                <th class="ps-4">Nome</th>
                                <th>Login (LDAP)</th>
                                <th>Perfil de Acesso</th>
                                <th class="text-center">Status</th>
                                <th class="text-center pe-4">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($usuarios)): foreach ($usuarios as $user): ?>
                                <tr>
                                    <td class="ps-4 fw-bold text-dark"><i class="bi bi-person-circle me-2 text-secondary"></i><?= esc($user->nome) ?></td>
                                    <td class="text-muted"><?= esc($user->usuario) ?></td>
                                    <td>
                                        <span class="badge <?= $user->perfil === 'Admin' ? 'bg-dark' : 'bg-primary' ?> rounded-pill">
                                            <?= esc($user->perfil) ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($user->ativo): ?>
                                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">Ativo</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill">Bloqueado</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center pe-4">
                                        <?php if ($user->id != session()->get('usuario_id')): ?>
                                            <a href="<?= base_url('usuarios/status/' . $user->id) ?>" class="btn btn-sm btn-outline-<?= $user->ativo ? 'danger' : 'success' ?> rounded-pill px-3 shadow-sm" onclick="return confirm('Tem certeza que deseja <?= $user->ativo ? 'bloquear' : 'desbloquear' ?> este usuário?');">
                                                <i class="bi <?= $user->ativo ? 'bi-lock-fill' : 'bi-unlock-fill' ?> me-1"></i> <?= $user->ativo ? 'Bloquear' : 'Liberar' ?>
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted fs-7 fst-italic">Sua conta</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; endif; ?>
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

    <div class="modal fade" id="modalNovoUsuario" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header bg-success text-white rounded-top-4">
                    <h5 class="modal-title fw-bold"><i class="bi bi-person-plus me-2"></i>Cadastrar Usuário</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="<?= base_url('usuarios/salvar') ?>" method="POST">
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label text-muted fs-7 fw-bold">Nome Completo</label>
                            <input type="text" name="nome" class="form-control bg-light" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted fs-7 fw-bold">Login (será o mesmo do LDAP futuramente)</label>
                            <input type="text" name="usuario" class="form-control bg-light" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted fs-7 fw-bold">Senha Inicial (Provisória)</label>
                            <input type="password" name="senha" class="form-control bg-light" required>
                            <div class="form-text">O usuário usará esta senha até a integração LDAP ser concluída.</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted fs-7 fw-bold">Perfil de Acesso</label>
                            <select name="perfil" class="form-select bg-light" required>
                                <option value="Comum">Comum (Apenas opera PIs e Empenhos)</option>
                                <option value="Admin">Administrador (Acesso total + Auditoria)</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer bg-light rounded-bottom-4">
                        <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success fw-bold rounded-pill px-4 shadow-sm">Cadastrar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>