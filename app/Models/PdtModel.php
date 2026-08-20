<?php namespace App\Models;

    use CodeIgniter\Model;

    class PdtModel extends Model
    {
        protected $table      = 'pdt_ejemplo';
        protected $primaryKey = 'id_pdt';

        protected $useAutoIncrement = true;

        protected $returnType     = 'array';

        protected $allowedFields = ['id_pdt','id_pdt_renta','nombre_pdt','nombre_constancia','estado'];

        protected $useTimestamps = false;
        protected $createdField  = 'created_at';
        protected $updatedField  = 'updated_at';

    }

?>