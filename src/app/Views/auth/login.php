<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SGRF / <?= $unidadeNome ?? 'PROINTER' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { 
            background-color: #f4f6f9; 
            font-family: 'Segoe UI', Roboto, sans-serif;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            overflow: hidden;
            width: 100%;
            max-width: 900px;
        }
        .login-left {
            background: linear-gradient(135deg, #751621 0%, #4a0e15 100%);
            color: white;
            padding: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .login-right {
            background: #ffffff;
            padding: 4rem 3rem;
        }
        .btn-ufpa {
            background-color: #302782;
            color: white;
            border: none;
            border-radius: 50rem;
            padding: 0.75rem 1.5rem;
            font-weight: bold;
            transition: all 0.3s;
        }
        .btn-ufpa:hover {
            background-color: #201a58;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(48, 39, 130, 0.3);
        }
        .form-control {
            border-radius: 8px;
            padding: 0.75rem 1rem;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
        }
        .form-control:focus {
            border-color: #302782;
            box-shadow: 0 0 0 0.25rem rgba(48, 39, 130, 0.25);
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card login-card d-flex flex-row flex-wrap flex-md-nowrap">
                    
                    <div class="col-md-5 login-left text-center text-md-start">
                        <i class="bi bi-bank2 display-3 mb-4 text-white-50"></i>
                        <h2 class="fw-bold mb-3">SGRF</h2>
                        <h5 class="fw-light mb-4 text-white-50">Sistema de Gerenciamento de Recursos Financeiros</h5>
                        <div class="mt-auto pt-4 border-top border-light border-opacity-25">
                            <small class="fw-bold d-block mb-1">Universidade Federal do Pará</small>
                            <small class="text-white-50"><?= esc($unidadeNome ?? 'PROINTER') ?> • CTIC</small>
                        </div>
                    </div>

                    <div class="col-md-7 login-right">
                        <div class="mb-5 text-center text-md-start">
                            <h3 class="fw-bold text-dark mb-1">Bem-vindo(a)</h3>
                            <p class="text-muted">Faça login com suas credenciais para acessar o painel.</p>
                        </div>

                        <?php if (session()->getFlashdata('erro')): ?>
                            <div class="alert alert-danger rounded-3 border-0 shadow-sm mb-4">
                                <i class="bi bi-exclamation-octagon-fill me-2"></i> <?= session()->getFlashdata('erro') ?>
                            </div>
                        <?php endif; ?>

                        <form action="<?= base_url('login/autenticar') ?>" method="POST">
                            <div class="mb-4">
                                <label class="form-label text-muted fw-bold fs-7 text-uppercase">Usuário</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-person"></i></span>
                                    <input type="text" name="usuario" class="form-control border-start-0" placeholder="Digite seu login..." required autofocus>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label text-muted fw-bold fs-7 text-uppercase">Senha</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-lock"></i></span>
                                    <input type="password" name="senha" class="form-control border-start-0" placeholder="••••••••" required>
                                </div>
                            </div>

                            <div class="d-grid mt-5">
                                <button type="submit" class="btn btn-ufpa shadow-sm">
                                    Acessar Sistema <i class="bi bi-box-arrow-in-right ms-2"></i>
                                </button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>

</body>
</html>