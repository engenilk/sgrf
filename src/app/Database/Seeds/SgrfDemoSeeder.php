<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SgrfDemoSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();

        // 1. Limpeza Segura
        $db->query('SET FOREIGN_KEY_CHECKS = 0');
        $db->table('acoes')->truncate();
        $db->table('movimentacoes')->truncate();
        $db->table('empenhos')->truncate();
        $db->table('pis')->truncate();
        $db->query('SET FOREIGN_KEY_CHECKS = 1');

        $dataAtual = date('Y-m-d H:i:s');

        // ==========================================
        // 2. Criação dos PIs
        // ==========================================
        $pis = [
            ['codigo' => 'PI-2026-ISF',   'programa' => '32 - Idioma sem Fronteiras', 'desc' => 'Custeio IsF', 'credito' => 500000.00],
            ['codigo' => 'PI-2026-PRINT', 'programa' => '15 - CAPES PrInt',           'desc' => 'Missões Exteriores', 'credito' => 850000.00],
            ['codigo' => 'PI-2026-ADMIN', 'programa' => null,                         'desc' => 'Administrativo PROINTER', 'credito' => 120000.00]
        ];

        foreach ($pis as $p) {
            $db->table('pis')->insert([
                'codigo'           => $p['codigo'],
                'programa'         => $p['programa'],
                'descricao'        => $p['desc'],
                'credito_alocado'  => $p['credito'],
                'credito_atual'    => $p['credito'],
                'saldo_disponivel' => $p['credito'], // Será recalculado dinamicamente
                'created_at'       => $dataAtual
            ]);
            
            $piId = $db->insertID();
            
            // Aporte Contábil Inicial
            $db->table('movimentacoes')->insert([
                'pi_destino_id' => $piId, 'tipo_operacao' => 'Aporte_Inicial', 
                'valor' => $p['credito'], 'justificativa_historico' => 'Aporte Abertura ' . $p['codigo'], 
                'created_at' => $dataAtual
            ]);

            // ==========================================
            // 3. Geração de 12 a 18 Empenhos por PI
            // ==========================================
            $qtdEmpenhos = rand(12, 18);
            $saldoPiRestante = $p['credito'];
            
            for ($i = 1; $i <= $qtdEmpenhos; $i++) {
                // Reserva entre 5 mil e 25 mil
                $valorEmpenho = rand(5000, 25000); 
                
                // Trava de segurança para não negativar o PI
                if ($saldoPiRestante - $valorEmpenho < 0) break; 
                
                $db->table('empenhos')->insert([
                    'pi_id'           => $piId,
                    'codigo_dfc'      => 'DFC-2026-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT),
                    'numero_processo' => '23073.' . str_pad(rand(10000, 99999), 6, '0', STR_PAD_LEFT) . '/2026-' . rand(10, 99),
                    'fonte'           => '0112000000',
                    'ptres'           => '168541',
                    'objeto'          => "Despesa padronizada de custeio/serviço lote $i",
                    'valor_total'     => $valorEmpenho,
                    'valor_consumido' => 0, // Será calculado pelas ações
                    'status'          => 'Ativo',
                    'created_at'      => date('Y-m-d H:i:s', strtotime("-$i days")) // Datas retroativas
                ]);
                
                $empId = $db->insertID();
                $saldoPiRestante -= $valorEmpenho;

                // ==========================================
                // 4. Geração de Ações (Pagamentos) consistentes
                // ==========================================
                $qtdAcoes = rand(1, 4);
                $consumoTotalEmpenho = 0;
                $valorAcaoBase = round($valorEmpenho / ($qtdAcoes + 1), 2);

                for ($j = 1; $j <= $qtdAcoes; $j++) {
                    $db->table('acoes')->insert([
                        'empenho_id'     => $empId,
                        'processo'       => '23073.' . rand(10000, 99999) . '/2026-PG',
                        'lista_credor'   => 'LC' . rand(1000, 9999),
                        'referencia'     => "Parcela $j do serviço",
                        'tipo_pagamento' => 'Ordem bancária',
                        'data_envio'     => date('Y-m-d', strtotime("-$j days")),
                        'descricao'      => 'Liquidação de fatura',
                        'beneficiario'   => 'Fornecedor Genérico LTDA',
                        'valor'          => $valorAcaoBase,
                        'created_at'     => $dataAtual
                    ]);
                    $consumoTotalEmpenho += $valorAcaoBase;
                }

                // Atualiza o valor consumido do Empenho e se liquidou
                $statusEmpenho = ($consumoTotalEmpenho >= $valorEmpenho * 0.99) ? 'Liquidado' : 'Ativo';
                $db->table('empenhos')->where('id', $empId)->update([
                    'valor_consumido' => $consumoTotalEmpenho,
                    'status' => $statusEmpenho
                ]);
            }

            // Atualiza o Saldo Disponível real do PI após criar todos os empenhos
            $db->table('pis')->where('id', $piId)->update(['saldo_disponivel' => $saldoPiRestante]);
        }

        echo "\n🚀 Volume massivo e matematicamente preciso gerado com sucesso!\n";
    }
}