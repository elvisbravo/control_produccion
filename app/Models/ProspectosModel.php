<?php

namespace App\Models;

use CodeIgniter\Model;

class ProspectosModel extends Model
{
    protected $table      = 'prospectos';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';

    protected $allowedFields = ['id', 'fecha_contacto', 'origen_id', 'usuario_venta_id', 'nivel_academico_id', 'carrera_id', 'estado', 'tarea_id', 'fecha_entrega', 'usuario_jefe_valoro'];

    protected $useTimestamps = false;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}