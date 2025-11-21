<?php

namespace App\Models;

use CodeIgniter\Model;

class UsuarioModel extends Model
{
    protected $table      = 'usuarios';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';

    protected $allowedFields = [
        'id',
        'nombres',
        'apellidos',
        'perfil_id',
        'correo',
        'password',
        'telefono',
        'direccion',
        'fecha_nacimiento',
        'estado'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Obtener usuarios por perfil/rol
     */
    public function obtenerPorPerfil($perfilId, $soloActivos = true)
    {
        $builder = $this->where('perfil_id', $perfilId);

        if ($soloActivos) {
            $builder->where('estado', 1);
        }

        return $builder->orderBy('nombres', 'ASC')->findAll();
    }

    /**
     * Obtener auxiliares de producción activos
     */
    public function obtenerAuxiliares()
    {
        return $this->obtenerPorPerfil(4, true);
    }

    /**
     * Obtener jefes de producción activos
     */
    public function obtenerJefes()
    {
        return $this->obtenerPorPerfil(3, true);
    }

    /**
     * Obtener captadores activos
     */
    public function obtenerCaptadores()
    {
        return $this->obtenerPorPerfil(2, true);
    }
}
