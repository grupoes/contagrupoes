<?php namespace App\Models;

    use CodeIgniter\Model;

    class ArchivosComprasModel extends Model
    {
        protected $table      = 'archivos_compra';
        protected $primaryKey = 'id';

        protected $useAutoIncrement = true;

        protected $returnType     = 'array';

        protected $allowedFields = ['id','filename','descripcion','estado', 'idcompra'];

        protected $useTimestamps = false;
        protected $createdField  = 'created_at';
        protected $updatedField  = 'updated_at';

    }

?>