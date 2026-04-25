<?php

namespace App\Models;

use CodeIgniter\Model;

class MovimentacaoModel extends Model
{
    protected $table            = 'movimentacoes';
    protected $primaryKey       = 'id';
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false; // Auditoria nunca se apaga
    protected $allowedFields    = [
        'pi_origem_id', 'pi_destino_id', 'empenho_id', 
        'tipo_operacao', 'valor', 'justificativa_historico'
    ];

    // Esta tabela só tem created_at, não é atualizada
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = ''; 
}
