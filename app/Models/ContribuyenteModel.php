<?php namespace App\Models;

    use CodeIgniter\Model;

    class ContribuyenteModel extends Model
    {
        protected $table      = 'ruc_empresa';
        protected $primaryKey = 'ruc_empresa_numero';

        protected $useAutoIncrement = true;

        protected $returnType     = 'array';

        protected $allowedFields = ['ruc_empresa_numero','ruc_empresa_estado','ruc_empresa_razon_social','acceso'];

        protected $useTimestamps = false;
        protected $createdField  = 'created_at';
        protected $updatedField  = 'updated_at';

    }

?>