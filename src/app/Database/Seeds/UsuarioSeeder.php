<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UsuarioSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('usuarios');

        // Verifica se já existe para não duplicar
        $existe = $builder->where('usuario', 'admin')->get()->getRow();

        if (!$existe) {
            $builder->insert([
                'nome'       => 'Administrador do Sistema',
                'usuario'    => 'admin',
                'senha'      => password_hash('senha123', PASSWORD_DEFAULT),
                'perfil'     => 'Admin',
                'ativo'      => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            
            echo "\n✅ Usuário Administrador inserido com sucesso direto na base!\n";
        } else {
            echo "\n⚠️ O usuário Administrador já existe na base.\n";
        }
    }
}