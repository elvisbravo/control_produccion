<?php

namespace App\Models;

use CodeIgniter\Model;

class HorarioUsuarioModel extends Model
{
    protected $table      = 'horario_usuario';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;
    protected $returnType     = 'array';

    protected $allowedFields = ['id', 'usuario_id', 'fecha', 'hora_inicio', 'hora_fin', 'estado'];

    protected $useTimestamps = false;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $dateFormat    = 'datetime';
}
