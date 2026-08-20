<?php

namespace App\Controllers;

class Facturar extends BaseController
{
    public function index()
    {
        return view('facturar/index');
    }

    public function facturando()
    {
        //$id_producto_contable = 1214;
        //$id_producto_alquiler = 1229;

        $id_producto_contable = 1214;
        $id_producto_alquiler = 1229;

        $codigo_contable = "824T64";
        $codigo_alquiler = "DE39BL";

        $id_sucursal = 35;
        $id_vendedor = 43;

        //$id_sucursal = 35;
        //$id_vendedor = 43;

        //$codigo_contable = "824T64";
        //$codigo_alquiler = "DE39BL";

        //token es consultores R9ENP23ZUVMSGXNARRNPWHH0Q5A8MBEZRY83J
        $token = "R9ENP23ZUVMSGXNARRNPWHH0Q5A8MBEZRY83J";
        $tipo_proceso = "produccion";

        $rucs = $this->request->getVar('ruc');
        $precios = $this->request->getVar('precio');
        $descripciones = $this->request->getVar('descripcion');
        $servicios = $this->request->getVar('servicio');
        $razon = $this->request->getVar('razon');

        for ($i=0; $i < count($rucs); $i++) {
            
            if($servicios[$i] === 'CONTABLE') {
                $id_producto = $id_producto_contable;
                $codigo = $codigo_contable;
            } else {
                $id_producto = $id_producto_alquiler;
                $codigo = $codigo_alquiler;
            }
            
            $data["contribuyente"] = array(
                "token_contribuyente" => $token, //Token del contribuyente
                "id_usuario_vendedor" => $id_vendedor, //Debes ingresar el ID de uno de tus vendedores (opcional)
                "tipo_proceso" => $tipo_proceso, //Funcional en una siguiente versión. El ambiente al que se enviará, puede ser: {prueba, produccion}
                "tipo_envio" => "inmediato" //funcional en una siguiente versión. Aquí puedes definir si se enviará de inmediato a sunat
            );

            $data["cliente"] = array(
                "tipo_docidentidad" => 6, //{0: SINDOC, 1: DNI, 6: RUC}
                "numerodocumento" => $rucs[$i], //Es opcional solo cuando tipo_docidentidad es 0, caso contrario se debe ingresar el número de ruc
                "nombre" => $razon[$i], //Es opcional solo cuando tipo_docidentidad es 1, caso contrario es obligatorio ingresar aquí la razón social
                "email" => "email_cliente@gmail.com", //opcional: (si tiene correo se enviará automáticamente el email)
                "direccion" => "", //opcional: 
                "ubigeo"	=> "",
                "sexo" => "", //opcional: masculino
                "fecha_nac" => "", //opcional: 
                "celular" => "" //opcional
            );

            $data["cabecera_comprobante"] = array(
                "tipo_documento" => "01",  //{"01": FACTURA, "03": BOLETA}
                "moneda" => "PEN",  //{"USD", "PEN"}
                "idsucursal" => $id_sucursal,  //{ID DE SUCURSAL}
                "id_condicionpago" => "",  //condicionpago_comprobante
                "fecha_comprobante" => date('d/m/Y'),  //fecha_comprobante
                "nro_placa" => "",  //nro_placa_vehiculo
                "nro_orden" => "",  //nro_orden
                "guia_remision" => "",  //guia_remision_manual
                "descuento_monto" => 0,  // (máximo 2 decimales) (monto total del descuento)
                "descuento_porcentaje" => 0,  // (máximo 2 decimales) (porcentaje total del descuento)
                "observacion" => "",  //observacion_documento
            );

            $detalle[] = array(
                "idproducto" => $id_producto,  //(opcional, puede ser cero) (si el idproducto coincide con la BD se llevará control del stock)
                "codigo"	=> $codigo, //codigo del producto (requerido)
                "afecto_icbper" => "no",  //"afecto_icbper":"no",
                "id_tipoafectacionigv" => 20,  //"id_tipoafectacionigv":"10",
                "descripcion" => $descripciones[$i],  //"descripcion":"Zapatos",
                "idunidadmedida" => 'NIU',  //{NIU para unidades, ZZ para servicio}
                "precio_venta" => $precios[$i],  //Precio unitario de venta (inc. IGV),
                "cantidad" => 1,  //"cantidad":"1"
            );

            $data["detalle"] = $detalle;
        
            $ruta = "https://esfacturador.com/facturacionv7/api/procesar_venta";
            $data_json = json_encode($data);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $ruta);
            curl_setopt(
                $ch, CURLOPT_HTTPHEADER, array(
                    "Authorization: Bearer ".$token,
                    "Content-Type: application/json",
                    "cache-control: no-cache"
                )
            );
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS,$data_json);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $respuesta  = curl_exec($ch);
            if (curl_error($ch)) {
                $error_msg = curl_error($ch);
            }
            curl_close($ch);
            if (isset($error_msg)) {
                $resp["respuesta"] = "error";
                $resp["titulo"] = "Error";
                $resp["data"] = "";
                $resp["encontrado"] = false;
                $resp["mensaje"] = "Error en Api de Búsqueda";
                $resp["errores_curl"] = $error_msg;
                echo json_encode($resp);
                exit();
            }

            sleep(1);

            echo $respuesta;

            $detalle = [];

        }

        echo json_encode("ok");

    }

    public function lista()
    {
        $url = "http://157.230.239.170:4010/empresas-facturar";

        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        echo json_encode($response);
    }

}
