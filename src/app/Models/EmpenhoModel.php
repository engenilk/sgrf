<?php

namespace App\Models;

use CodeIgniter\Model;

class EmpenhoModel extends Model
{
    protected $table            = 'empenhos';
    protected $primaryKey       = 'id';
    protected $returnType       = 'object';
    // Empenhos não têm deleted_at no nosso SQL por questões de integridade
    protected $useSoftDeletes   = false; 
    protected $allowedFields    = ['pi_id', 'numero_processo', 'objeto', 'valor_total', 'valor_consumido', 'status'];

    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
}
