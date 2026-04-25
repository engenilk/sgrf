<?php

namespace App\Controllers;

use App\Models\UsuarioModel;

class UsuarioController extends BaseController
{
    public function index()
    {
        // Trava de Segurança (ACL): Apenas Admin entra
        if (session()->get('perfil') !== 'Admin') {
            return redirect()->to('/pis')->with('erro', 'Acesso negado. Apenas administradores podem gerenciar usuários.');
        }

        $usuarioModel = new UsuarioModel();
        
        $this->data['usuarios'] = $usuarioModel->orderBy('nome', 'ASC')->findAll();
        $this->data['tituloPagina'] = 'Gestão de Usuários';

        return view('usuarios/index', $this->data);
    }

    public function salvar()
    {
        if (session()->get('perfil') !== 'Admin') return redirect()->to('/pis');

        $usuarioModel = new UsuarioModel();
        
        $nome = $this->request->getPost('nome');
        $login = $this->request->getPost('usuario');
        $senha = $this->request->getPost('senha');
        $perfil = $this->request->getPost('perfil');

        // Verifica se o login já existe
        if ($usuarioModel->where('usuario', $login)->first()) {
            return redirect()->back()->with('erro', 'Este login já está em uso por outro usuário.');
        }

        $usuarioModel->insert([
            'nome'    => $nome,
            'usuario' => $login,
            'senha'   => password_hash($senha, PASSWORD_DEFAULT),
            'perfil'  => $perfil,
            'ativo'   => 1
        ]);

        $this->auditar('Criação de Usuário', "Cadastrou o usuário $login com perfil $perfil.");

        return redirect()->back()->with('sucesso', 'Usuário cadastrado com sucesso!');
    }

    public function alternarStatus($id)
    {
        if (session()->get('perfil') !== 'Admin') return redirect()->to('/pis');

        $usuarioModel = new UsuarioModel();
        $usuario = $usuarioModel->find($id);

        if (!$usuario) return redirect()->back()->with('erro', 'Usuário não encontrado.');
        
        // Proteção para o admin não desativar a si mesmo por engano
        if ($usuario->id == session()->get('usuario_id')) {
            return redirect()->back()->with('erro', 'Você não pode desativar o seu próprio usuário.');
        }

        $novoStatus = $usuario->ativo ? 0 : 1;
        $acaoText = $novoStatus ? 'Ativou' : 'Desativou';

        $usuarioModel->update($id, ['ativo' => $novoStatus]);

        $this->auditar('Alteração de Status de Usuário', "$acaoText o acesso do usuário {$usuario->usuario}.");

        return redirect()->back()->with('sucesso', "O usuário foi $acaoText com sucesso.");
    }
}