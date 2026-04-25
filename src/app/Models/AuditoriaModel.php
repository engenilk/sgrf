<?php

namespace App\Models;

use CodeIgniter\Model;

class AuditoriaModel extends Model
{
    protected $table            = 'auditoria';
    protected $primaryKey       = 'id';
    protected $returnType       = 'object';
    
    protected $allowedFields    = ['usuario_id', 'usuario', 'acao', 'detalhes', 'ip_address', 'created_at'];
}