<?php namespace App\Models;

    use CodeIgniter\Model;

    class PerfilModel extends Model
    {
        protected $table      = 'perfiles';
        protected $primaryKey = 'id';

        protected $useAutoIncrement = true;

        protected $returnType     = 'array';

        protected $allowedFields = ['id','nombre_perfil', 'estado'];

        protected $useTimestamps = false;
        protected $createdField  = 'created_at';
        protected $updatedField  = 'updated_at';

    }

?>