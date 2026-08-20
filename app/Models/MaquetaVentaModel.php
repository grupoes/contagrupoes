<?php namespace App\Models;

    use CodeIgniter\Model;

    class MaquetaVentaModel extends Model
    {
        protected $table      = 'maqueta_ventas';
        protected $primaryKey = 'id_maqueta';

        protected $useAutoIncrement = true;

        protected $returnType     = 'array';

        protected $allowedFields = ['id_maqueta','fecha','tipo_moneda','documento','numero_documento','condicion','ruc','razon_social','vventa','valor_venta','igv','bolsa','icb','total','tipo_cambio','glosa','cuenta','afectacion','fecha_registro','estado','color','condicion_contribuyente','estado_contribuyente', 'tipo', 'referencia', 'fecha_referencia','contribuyente'];

        protected $useTimestamps = false;
        protected $createdField  = 'created_at';
        protected $updatedField  = 'updated_at';

    }

?>