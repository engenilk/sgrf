<?php

namespace App\Models;

use CodeIgniter\Model;

class PiModel extends Model
{
    protected $table            = 'pis';
    protected $primaryKey       = 'id';
    protected $returnType       = 'object';
    protected $useSoftDeletes   = true;
    protected $allowedFields    = ['codigo', 'descricao', 'limite_anual_aprovado', 'saldo_disponivel'];

    // Ativa a inserção automática de created_at, updated_at e deleted_at
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $deletedField     = 'deleted_at';
}
