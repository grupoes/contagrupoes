<?php namespace App\Models;

    use CodeIgniter\Model;

    class PdtAnualModel extends Model
    {
        protected $table      = 'pdt_anual';
        protected $primaryKey = 'id_pdt_anual';

        protected $useAutoIncrement = true;

        protected $returnType     = 'array';

        protected $allowedFields = ['id_pdt_anual','ruc_empresa','periodo','id_pdt_tipo'];

        protected $useTimestamps = false;
        protected $createdField  = 'created_at';
        protected $updatedField  = 'updated_at';

    }

?>