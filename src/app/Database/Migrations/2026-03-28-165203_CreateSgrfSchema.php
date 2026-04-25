<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSgrfSchema extends Migration
{
    public function up()
    {
        // 1. Tabela de Usuários (ACL e Autenticação)
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'nome'       => ['type' => 'VARCHAR', 'constraint' => 255],
            'usuario'    => ['type' => 'VARCHAR', 'constraint' => 100, 'unique' => true],
            'senha'      => ['type' => 'VARCHAR', 'constraint' => 255],
            'perfil'     => ['type' => 'ENUM', 'constraint' => ['Admin', 'Comum'], 'default' => 'Comum'],
            'ativo'      => ['type' => 'BOOLEAN', 'default' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('usuarios');

        // 2. Tabela de PIs
        $this->forge->addField([
            'id'               => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'codigo'           => ['type' => 'VARCHAR', 'constraint' => 50, 'unique' => true],
            'programa'         => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'descricao'        => ['type' => 'VARCHAR', 'constraint' => 255],
            'credito_alocado'  => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0.00],
            'credito_atual'    => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0.00],
            'saldo_disponivel' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0.00],
            'created_at'       => ['type' => 'DATETIME', 'null' => true],
            'updated_at'       => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'       => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('pis');

        // 3. Tabela de Empenhos
        $this->forge->addField([
            'id'                 => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'pi_id'              => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'codigo_dfc'         => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'numero_processo'    => ['type' => 'VARCHAR', 'constraint' => 100],
            'processo_associado' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'fonte'              => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'ptres'              => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'objeto'             => ['type' => 'TEXT'],
            'observacoes'        => ['type' => 'TEXT', 'null' => true],
            'valor_total'        => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0.00],
            'valor_consumido'    => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0.00],
            'status'             => ['type' => 'ENUM', 'constraint' => ['Ativo', 'Liquidado', 'Anulado'], 'default' => 'Ativo'],
            'created_at'         => ['type' => 'DATETIME', 'null' => true],
            'updated_at'         => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('pi_id', 'pis', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('empenhos');

        // 4. Tabela de Ações
        $this->forge->addField([
            'id'             => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'empenho_id'     => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'processo'       => ['type' => 'VARCHAR', 'constraint' => 100],
            'lista_credor'   => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'referencia'     => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'tipo_pagamento' => ['type' => 'ENUM', 'constraint' => ['Ordem bancária', 'Crédito em Conta', 'Transferência Internacional', 'Outros']],
            'data_envio'     => ['type' => 'DATE', 'null' => true],
            'descricao'      => ['type' => 'TEXT'],
            'beneficiario'   => ['type' => 'VARCHAR', 'constraint' => 255],
            'valor'          => ['type' => 'DECIMAL', 'constraint' => '15,2'],
            'observacoes'    => ['type' => 'TEXT', 'null' => true],
            'created_at'     => ['type' => 'DATETIME', 'null' => true],
            'updated_at'     => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'     => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('empenho_id', 'empenhos', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('acoes');

        // 5. Tabela de Movimentações
        $this->forge->addField([
            'id'                      => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'pi_origem_id'            => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'pi_destino_id'           => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'empenho_id'              => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'tipo_operacao'           => ['type' => 'ENUM', 'constraint' => ['Aporte_Inicial', 'Transferencia', 'Novo_Empenho', 'Reforco_Empenho', 'Anulacao_Empenho', 'Estorno_Acao']],
            'valor'                   => ['type' => 'DECIMAL', 'constraint' => '15,2'],
            'justificativa_historico' => ['type' => 'TEXT'],
            'created_at'              => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('pi_origem_id', 'pis', 'id', 'CASCADE', 'SET NULL');
        $this->forge->addForeignKey('pi_destino_id', 'pis', 'id', 'CASCADE', 'SET NULL');
        $this->forge->addForeignKey('empenho_id', 'empenhos', 'id', 'CASCADE', 'SET NULL');
        $this->forge->createTable('movimentacoes');

        // 6. Tabela de Auditoria
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'usuario_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'usuario'    => ['type' => 'VARCHAR', 'constraint' => 100],
            'acao'       => ['type' => 'VARCHAR', 'constraint' => 255],
            'detalhes'   => ['type' => 'TEXT', 'null' => true],
            'ip_address' => ['type' => 'VARCHAR', 'constraint' => 45],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('usuario_id', 'usuarios', 'id', 'CASCADE', 'SET NULL');
        $this->forge->createTable('auditoria');
    }

    public function down()
    {
        $this->forge->dropTable('auditoria', true);
        $this->forge->dropTable('movimentacoes', true);
        $this->forge->dropTable('acoes', true);
        $this->forge->dropTable('empenhos', true);
        $this->forge->dropTable('pis', true);
        $this->forge->dropTable('usuarios', true);
    }
}