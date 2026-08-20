<?php

namespace App\Controllers;

class Pdts extends BaseController
{
    public function index()
    {
        if (!session()->is_logged) {
			return redirect()->to("/");
		}

        return view('inicio/pdts');
    }

    public function traer_data()
    {
        $anio = $this->request->getVar('anio');
        $inicio = $this->request->getVar('inicio_periodo');
        $fin = $this->request->getVar('fin_periodo');

        $ruc = session()->ruc;

        if ($fin < $inicio) {
            $json = array(
                "respuesta" => "error",
                "mensaje" => "El periodo de fin no debe ser menor que el periodo de inicio"
            );

            echo json_encode($json);
            exit();
        }
        
        $pdt = model('PdtRentaModel');

        $consulta = $pdt->query("SELECT * FROM pdt_renta INNER JOIN archivos_pdt0621 ON archivos_pdt0621.id_pdt_renta = pdt_renta.id_pdt_renta INNER JOIN anio ON anio.id_anio = pdt_renta.anio INNER JOIN mes ON mes.id_mes = pdt_renta.periodo WHERE pdt_renta.ruc_empresa = '$ruc' AND pdt_renta.anio= $anio AND archivos_pdt0621.estado = 1 AND pdt_renta.periodo >= $inicio AND pdt_renta.periodo <= $fin")->getResult();

        echo json_encode($consulta);
    }

    public function traer_detalle($id)
    {
        $archivos = model('ArchivosPdtModel');

        $consulta = $archivos->query("SELECT * FROM archivos_pdt0621 WHERE id_pdt_renta = $id")->getRow();

        $nombre = $consulta->nombre_pdt;

        $ruta = "https://grupoesconsultores.com/contabilidad/public/archivos/pdt/".$nombre;

        fopen($ruta,"a+");

    }

    public function anual()
    {
        if (!session()->is_logged) {
			return redirect()->to("/");
		}

        return view('inicio/pdt_anual');
    }

    public function traer_data_anual()
    {
        $inicio = $this->request->getVar('anio_inicio');
        $fin = $this->request->getVar('anio_fin');

        $ruc = session()->ruc;

        if ($fin < $inicio) {
            $json = array(
                "respuesta" => "error",
                "mensaje" => "El año de inicio no debe ser mayor al año de fin"
            );

            echo json_encode($json);
            exit();
        }

        $pdt_anual = model('ArchivosPdtAnualModel');

        $consulta = $pdt_anual->query("SELECT * FROM pdt_anual INNER JOIN archivos_pdtanual ON archivos_pdtanual.id_pdt_anual = pdt_anual.id_pdt_anual INNER JOIN anio ON anio.id_anio = pdt_anual.periodo  WHERE pdt_anual.ruc_empresa = '$ruc' AND archivos_pdtanual.estado = 1 AND pdt_anual.periodo >= $inicio AND pdt_anual.periodo <= $fin")->getResult();

        echo json_encode($consulta);
    }

}
