<?php namespace App\Models;

    use CodeIgniter\Model;

    class ArchivosPdtModel extends Model
    {
        protected $table      = 'archivos_pdt0621';
        protected $primaryKey = 'id_archivos_pdt';

        protected $useAutoIncrement = true;

        protected $returnType     = 'array';

        protected $allowedFields = ['id_archivos_pdt','id_pdt_renta','nombre_pdt','nombre_constancia'];

        protected $useTimestamps = false;
        protected $createdField  = 'created_at';
        protected $updatedField  = 'updated_at';

    }

?>