<?php namespace App\Models;

    use CodeIgniter\Model;

    class PdtRentaModel extends Model
    {
        protected $table      = 'pdt_renta';
        protected $primaryKey = 'id_pdt_renta';

        protected $useAutoIncrement = true;

        protected $returnType     = 'array';

        protected $allowedFields = ['id_pdt_renta','ruc_empresa','periodo','anio'];

        protected $useTimestamps = false;
        protected $createdField  = 'created_at';
        protected $updatedField  = 'updated_at';

    }

?>