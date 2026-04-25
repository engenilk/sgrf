<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AuthGuard implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Se não houver a flag isLoggedIn na sessão, redireciona para o login
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login')->with('erro', 'Você precisa estar logado para acessar o sistema.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}
}