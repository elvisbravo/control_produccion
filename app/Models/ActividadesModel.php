<?php

namespace App\Models;

use CodeIgniter\Model;

class ActividadesModel extends Model
{
    protected $table      = 'actividades';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';

    protected $allowedFields = ['id', 'prospecto_id', 'usuario_id', 'estado', 'tiempo_estimado', 'tiempo_real', 'fecha_inicio', 'fecha_fin', 'prioridad', 'created_at', 'updated_at'];

    protected $useTimestamps = false;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
