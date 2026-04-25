<?php

namespace App\Models;

use CodeIgniter\Model;

class AcaoModel extends Model
{
    protected $table            = 'acoes';
    protected $primaryKey       = 'id';
    protected $returnType       = 'object';
    protected $useSoftDeletes   = true;
    protected $allowedFields    = ['empenho_id', 'identificador', 'descricao', 'beneficiario', 'valor'];

    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $deletedField     = 'deleted_at';
}
