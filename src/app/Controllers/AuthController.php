<?php

namespace App\Controllers;

use App\Models\UsuarioModel;

class AuthController extends BaseController
{
    public function login()
    {
        // Se já estiver logado, manda pro Dashboard (PIs por enquanto)
        if (session()->get('isLoggedIn')) {
            return redirect()->to('/dashboard');
        }
        
        return view('auth/login', $this->data);
    }

    public function autenticar()
    {
        $login = $this->request->getPost('usuario');
        $senha = $this->request->getPost('senha');

        // Instancia o Model diretamente usando a declaração 'use' lá em cima
        $usuarioModel = new UsuarioModel();
        
        // Busca o usuário no banco
        $user = $usuarioModel->where('usuario', $login)->first();

        // Verifica se o usuário existe e se a senha bate com o hash
        if ($user && password_verify($senha, $user->senha)) {
            
            // Trava de segurança para usuários desativados
            if (!$user->ativo) {
                return redirect()->back()->with('erro', 'Sua conta está desativada. Procure o administrador do sistema.');
            }

            // Cria a sessão com os dados reais do banco
            session()->set([
                'isLoggedIn' => true,
                'usuario_id' => $user->id,
                'usuario'    => $user->nome,
                'login'      => $user->usuario,
                'perfil'     => $user->perfil
            ]);

            // Registra no Log de Auditoria
            $this->auditar('Login', 'Acesso realizado com sucesso.');

            return redirect()->to('/dashboard');
        }

        return redirect()->back()->with('erro', 'Usuário ou senha inválidos.');
    }

    public function logout()
    {
        // Só audita o logout se tiver alguém logado
        if (session()->get('isLoggedIn')) {
            $this->auditar('Logout', 'Usuário encerrou a sessão.');
        }
        
        session()->destroy();
        return redirect()->to('/login');
    }
}