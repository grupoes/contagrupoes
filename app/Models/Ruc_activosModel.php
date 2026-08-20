<?php namespace App\Models;

    use CodeIgniter\Model;

    class Ruc_activosModel extends Model
    {
        protected $table      = 'ruc_activos';
        protected $primaryKey = 'id';

        protected $useAutoIncrement = true;

        protected $returnType     = 'array';

        protected $allowedFields = ['id','ruc','razon_social','estado'];

        protected $useTimestamps = false;
        protected $createdField  = 'created_at';
        protected $updatedField  = 'updated_at';

    }

?>