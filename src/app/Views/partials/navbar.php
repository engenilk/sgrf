<?php 
    $uri = service('uri')->getSegment(1); 
    // Extrai o primeiro nome e prepara o login
    $nomeCompleto = session()->get('usuario') ?? 'Usuário';
    $primeiroNome = explode(' ', trim($nomeCompleto))[0];
    $loginLdap = session()->get('login') ?? 'admin';
?>
<link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'><path fill='%23751621' d='M8 1.5a.5.5 0 0 1 .5.5v1h5a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5h5v-1a.5.5 0 0 1 .5-.5zM1.5 5.5A.5.5 0 0 1 2 5h12a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5H2a.5.5 0 0 1-.5-.5v-1zM2 8h1v5H2V8zm3 0h1v5H5V8zm3 0h1v5H8V8zm3 0h1v5h-1V8zM1.5 14a.5.5 0 0 1 .5-.5h12a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5H2a.5.5 0 0 1-.5-.5v-1z'/></svg>">

<nav class="navbar navbar-expand-lg navbar-dark shadow-sm py-3 mb-4 d-print-none" style="background-color: #751621; border-bottom: 4px solid #302782;">
    <div class="container-fluid px-4">
        <a class="navbar-brand d-flex align-items-center" href="<?= base_url('dashboard') ?>">
            <i class="bi bi-bank2 me-2 fs-4"></i> SGRF / <?= esc($unidadeNome ?? 'PROINTER') ?>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item"><a class="nav-link <?= ($uri == 'dashboard' || $uri == '') ? 'active fw-bold' : '' ?>" href="<?= base_url('dashboard') ?>"><i class="bi bi-speedometer2 me-1"></i> Dashboard</a></li>
                <li class="nav-item"><a class="nav-link <?= ($uri == 'pis') ? 'active fw-bold' : '' ?>" href="<?= base_url('pis') ?>"><i class="bi bi-folder2-open me-1"></i> PIs</a></li>
                <li class="nav-item"><a class="nav-link <?= ($uri == 'empenhos') ? 'active fw-bold' : '' ?>" href="<?= base_url('empenhos') ?>"><i class="bi bi-file-earmark-text me-1"></i> Empenhos</a></li>
                <li class="nav-item"><a class="nav-link <?= ($uri == 'relatorios') ? 'active fw-bold' : '' ?>" href="<?= base_url('relatorios') ?>"><i class="bi bi-bar-chart-line me-1"></i> Relatórios</a></li>
                
                <?php if (session()->get('perfil') === 'Admin'): ?>
                    <li class="nav-item"><a class="nav-link <?= ($uri == 'usuarios') ? 'active fw-bold' : '' ?>" href="<?= base_url('usuarios') ?>"><i class="bi bi-people me-1"></i> Usuários</a></li>
                    <li class="nav-item"><a class="nav-link <?= ($uri == 'auditoria') ? 'active fw-bold' : '' ?>" href="<?= base_url('auditoria') ?>"><i class="bi bi-shield-lock me-1"></i> Auditoria</a></li>
                <?php endif; ?>
                
                <li class="nav-item ms-lg-3">
                    <span class="text-white-50 small me-3 d-none d-lg-inline">Usuário: <span class="text-white fw-bold"><?= esc($primeiroNome) ?></span> (<?= esc($loginLdap) ?>)</span>
                    <a class="btn btn-sm btn-outline-light rounded-pill px-3 shadow-sm" href="<?= base_url('logout') ?>">Sair <i class="bi bi-box-arrow-right ms-1"></i></a>
                </li>
            </ul>
        </div>
    </div>
</nav>