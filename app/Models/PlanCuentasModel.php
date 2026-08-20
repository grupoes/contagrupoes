<?php namespace App\Models;

    use CodeIgniter\Model;

    class PlanCuentasModel extends Model
    {
        protected $table      = 'plan_cuentas';
        protected $primaryKey = 'id';

        protected $useAutoIncrement = true;

        protected $returnType     = 'array';

        protected $allowedFields = ['id','glosa','cuenta','estado'];

        protected $useTimestamps = false;
        protected $createdField  = 'created_at';
        protected $updatedField  = 'updated_at';

    }

?>