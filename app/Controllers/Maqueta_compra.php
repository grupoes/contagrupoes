<?php

namespace App\Controllers;

use DateTime;
use Error;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpParser\Node\Stmt\TryCatch;
use Smalot\PdfParser\Parser;

use PhpOffice\PhpSpreadsheet\Shared\Date;

class Maqueta_compra extends BaseController
{
    /*public function index()
    {
        if (!session()->is_logged) {
			return redirect()->to("/");
		}

        return view('inicio/index');
    }*/

    public function view_maqueta($ruc)
    {
        $datos = $this->api_dni_ruc("ruc", $ruc);

        if ($datos['respuesta'] == 'ok') {
            $data['contribuyente'] = $datos['data']->razon_social;
            $data['numero'] = $datos['data']->ruc;
        } else {
            $data['contribuyente'] = $datos['data_resp']->mensaje;
            $data['numero'] = $datos['data_resp']->ruc;
        }

        $activos = model('Ruc_activosModel');
        $ruc_activo = $activos->where('ruc', $ruc)->where('estado', 1)->first();

        if ($ruc_activo) {
            $data['mensaje'] = " - RUC activo para consultar";
            $data['ruc_activo'] = "1";
        } else {
            $data['mensaje'] = "";
            $data['ruc_activo'] = "0";
        }


        //$data['contribuyente'] = 'No se encontro';
        //$data['numero'] = $ruc;

        return view('maqueta/compras', $data);
    }

    public function si_se_repite_comprobante($numero_documento, $ruc, $fecha_registro)
    {
        $maqueta = model('MaquetaComprasModel');

        $consulta = $maqueta->where('numero_documento', $numero_documento)->where('ruc', $ruc)->where('fecha_registro', $fecha_registro)->findAll();

        return $consulta;
    }

    public function existe_ruc_dni($numero, $maqueta)
    {
        if ($maqueta == 1) {
            $maq = model('MaquetaComprasModel');

            $consulta = $maq->where('ruc', $numero)->first();

            if ($consulta) {
                return $consulta;
            } else {
                return 0;
            }
        } else {
            $maq = model('MaquetaVentaModel');

            $consulta = $maq->where('ruc', $numero)->first();

            if ($consulta) {
                return $consulta;
            } else {
                return 0;
            }
        }
    }

    public function descargarExcelMaqueta($file)
    {
        $archivo = WRITEPATH . 'maquetas/' . $file;
        if (file_exists($archivo)) {
            // Forzar la descarga del archivo
            return $this->response->download($archivo, null);
        } else {
            // Manejar el error, archivo no encontrado
            return $this->response->setStatusCode(404, 'Archivo no encontrado');
        }
    }

    public function descargarHonorarios($fecha_registro)
    {
        $maqueta = model('MaquetaComprasModel');

        $data = $maqueta->query("SELECT * FROM maqueta_compras WHERE fecha_registro = '$fecha_registro' AND total >= 1500 AND documento = 'HONORARIOS'")->getResult();

        return view('maqueta/honorarios', ['data' => $data]);
    }

    public function generar_maqueta()
    {
        set_time_limit(1800); // 5 minutos
        ini_set('max_execution_time', 1800);
        try {

            //echo json_encode($_POST);exit;
            $fecha_registro = date('Y-m-d H:i:s');

            $maqueta = model('MaquetaComprasModel');
            $maqueta_ventas = model('MaquetaVentaModel');

            $colores = ['FF0000', '0000FF', '008000', '800080', '348DB1', 'EE8D38', 'F58EF5', 'D94D82', '42E831', 'F3F946', '3E819B', 'A2DCF3', 'D1F054', '9A86F7', 'E586E8', 'F279A3', 'EE4667', 'B3EE99', 'F2D27E', 'ADFDEB', 'FF0000', '0000FF', '008000', '800080', '348DB1', 'EE8D38', 'F58EF5', 'D94D82', '42E831', 'F3F946', '3E819B', 'A2DCF3', 'D1F054', '9A86F7', 'E586E8', 'F279A3', 'EE4667', 'B3EE99', 'F2D27E', 'ADFDEB', 'FF0000', '0000FF', '008000', '800080', '348DB1', 'EE8D38', 'F58EF5', 'D94D82', '42E831', 'F3F946', '3E819B', 'A2DCF3', 'D1F054', '9A86F7', 'E586E8', 'F279A3', 'EE4667', 'B3EE99', 'F2D27E', 'ADFDEB', 'FF0000', '0000FF', '008000', '800080', '348DB1', 'EE8D38', 'F58EF5', 'D94D82', '42E831', 'F3F946', '3E819B', 'A2DCF3', 'D1F054', '9A86F7', 'E586E8', 'F279A3', 'EE4667', 'B3EE99', 'F2D27E', 'ADFDEB', 'FF0000', '0000FF', '008000', '800080', '348DB1', 'EE8D38', 'F58EF5', 'D94D82', '42E831', 'F3F946', '3E819B', 'A2DCF3', 'D1F054', '9A86F7', 'E586E8', 'F279A3', 'EE4667', 'B3EE99', 'F2D27E', 'ADFDEB', 'FF0000', '0000FF', '008000', '800080', '348DB1', 'EE8D38', 'F58EF5', 'D94D82', '42E831', 'F3F946', '3E819B', 'A2DCF3', 'D1F054', '9A86F7', 'E586E8', 'F279A3', 'EE4667', 'B3EE99', 'F2D27E', 'ADFDEB'];

            $ruc =  $this->request->getVar('ruc');
            $razon_social =  $this->request->getVar('razon');
            $periodo = $this->request->getVar('periodo') . "-01";

            //echo json_encode($_POST);exit;

            if (isset($_POST['fecha'])) {

                if ($_POST['periodo'] == "") {
                    $json = array(
                        "respuesta" => "error",
                        "mensaje" => "ingrese el periodo"
                    );

                    echo json_encode($json);
                    exit;
                }

                $bolsa = $this->request->getVar('bolsa');
                $condicion = $this->request->getVar('condicion');
                $cuenta = $this->request->getVar('cuenta');
                $documento = $this->request->getVar('documento');
                $fecha = $this->request->getVar('fecha');
                $glosa = $this->request->getVar('glosa');
                $icb = $this->request->getVar('icb');
                $igv = $this->request->getVar('igv');
                $ruc_cliente = $this->request->getVar('ruc_cliente');
                $serie_correlativo = $this->request->getVar('serie_correlativo');
                $tipo_cambio = $this->request->getVar('tipo_cambio');
                $tipo_moneda = $this->request->getVar('tipo_moneda');
                $total = $this->request->getVar('total');
                $valor_venta = $this->request->getVar('valor_venta');
                $vventa = $this->request->getVar('vventa');
                $afectacion = $this->request->getVar('afectacion');
                $tipo_cliente = $this->request->getVar('tipo_cliente');
                $serie = $this->request->getVar('serie');
                $correlativo = $this->request->getVar('correlativo');
                $tipo_documento = $this->request->getVar('tipo_documento');
                $contribuyente = $this->request->getVar('ruc_contribuyente');
                $items = $this->request->getVar('items');

                $item_ = $this->request->getVar('numeracion');

                $cantidad = count($fecha);

                $array_colores = [];

                $error = 0;
                $ruc_cache = [];
                $batch_compras = [];
                $seen_dup = [];

                for ($i = 0; $i < $cantidad; $i++) {

                    $separar_serie_correlativo = explode('-', $serie_correlativo[$i]);

                    $serie_ = trim($separar_serie_correlativo[0]);
                    $correlativo_ = trim($separar_serie_correlativo[1]);
                    $correlativo_ = (int) $correlativo_;

                    $serie_num = $serie_ . "-" . $correlativo_;

                    $ruc_proveedor = trim($ruc_cliente[$i]);

                    // Detección de duplicados en memoria (evita query SQL por fila)
                    $dup_key = $serie_num . '|' . $ruc_proveedor;
                    if (!isset($seen_dup[$dup_key])) {
                        $color = "";
                        $seen_dup[$dup_key] = ['color' => '', 'indices' => []];
                    } else {
                        if ($seen_dup[$dup_key]['color'] === '') {
                            $color = 'FF0000';
                            if (in_array($color, $array_colores) == true) {
                                $color_ = 999;
                                $intento = 0;
                                while ($color_ <= 999) {
                                    $conteo = count($colores);
                                    $color_aleatorio = mt_rand(0, $conteo - 1);
                                    $color_aleatorio = $colores[$color_aleatorio];
                                    $intento++;
                                    if (!in_array($color_aleatorio, $array_colores) || $intento >= 50) {
                                        $color_ = 1000;
                                    }
                                    $color = $color_aleatorio;
                                }
                            }
                            array_push($array_colores, $color);
                            $seen_dup[$dup_key]['color'] = $color;
                            // Actualizar color en los registros anteriores del batch
                            foreach ($seen_dup[$dup_key]['indices'] as $prev_idx) {
                                $batch_compras[$prev_idx]['color'] = $color;
                            }
                        } else {
                            $color = $seen_dup[$dup_key]['color'];
                        }
                    }
                    $seen_dup[$dup_key]['indices'][] = $i;

                    // Caché en memoria: evita query + llamada API por cada fila con el mismo RUC
                    if (isset($ruc_cache[$ruc_proveedor])) {
                        $razon_social = $ruc_cache[$ruc_proveedor]['razon_social'];
                        $condicion_con = $ruc_cache[$ruc_proveedor]['condicion'];
                        $estado_con = $ruc_cache[$ruc_proveedor]['estado'];
                    } else {
                        $res = $this->existe_ruc_dni($ruc_proveedor, 1);
                        if ($res == 0) {
                            $data_cliente = $this->api_dni_ruc("ruc", $ruc_proveedor);
                            if (isset($data_cliente['data'])) {
                                $razon_social = $data_cliente['data']->razon_social;
                                $condicion_con = $data_cliente['data']->condicion;
                                $estado_con = $data_cliente['data']->estado;
                            } else {
                                $razon_social = "ERROR";
                                $condicion_con = "ERROR";
                                $estado_con = "ERROR";
                            }
                        } else {
                            $razon_social = $res['razon_social'];
                            $condicion_con = $res['condicion_contribuyente'];
                            $estado_con = $res['estado_contribuyente'];
                        }
                        $ruc_cache[$ruc_proveedor] = [
                            'razon_social' => $razon_social,
                            'condicion' => $condicion_con,
                            'estado' => $estado_con,
                        ];
                    }

                    $insert = array(
                        "fecha" => date('Y-m-d', strtotime($fecha[$i])),
                        "tipo_moneda" => $tipo_moneda[$i],
                        "documento" => $documento[$i],
                        "numero_documento" => $serie_num,
                        "condicion" => $condicion[$i],
                        "ruc" => $ruc_proveedor,
                        "razon_social" => $razon_social,
                        "vventa" => $vventa[$i],
                        "valor_venta" => $valor_venta[$i],
                        "igv" => $igv[$i],
                        "bolsa" => $bolsa[$i],
                        "icb" => $icb[$i],
                        "total" => $total[$i],
                        "tipo_cambio" => trim($tipo_cambio[$i]),
                        "glosa" => $glosa[$i],
                        "cuenta" => $cuenta[$i],
                        "afectacion" => $afectacion[$i],
                        "fecha_registro" => $fecha_registro,
                        "estado" => "ACEPTADO",
                        "color" => $color,
                        "condicion_contribuyente" => $condicion_con,
                        "estado_contribuyente" => $estado_con,
                        "cliente" => $contribuyente,
                        "periodo" => $periodo
                    );

                    $batch_compras[$i] = $insert;
                }

                if (!empty($batch_compras)) {
                    $maqueta->insertBatch($batch_compras);
                }

                if ($error == 1) {
                    exit;
                }


                $query = $maqueta->where('fecha_registro', $fecha_registro)->orderBy('fecha', 'ASC')->findAll();

                $maqueta->query("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");

                //consulta de los bancarizados || >= 2000 soles || >= 500 dolares
                $bancarizados = $maqueta->query("SELECT * FROM maqueta_compras WHERE fecha_registro = '$fecha_registro' AND ( (tipo_moneda = 'D' AND total >= 500 * tipo_cambio) OR (tipo_moneda = 'S' AND total >= 2000))")->getResult();

                $honorarios_mayores = $maqueta->query("SELECT * FROM maqueta_compras WHERE fecha_registro = '$fecha_registro' AND total >= 1500 AND documento = 'HONORARIOS'")->getResult();

                //echo json_encode($query);exit;

                $spreadsheet = new Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();
                $sheet->setTitle("Maqueta de compras");

                $sheet->getStyle('A1:T1')->applyFromArray(['font' => ['bold' => true]]);

                $encabezado = ["PERIODO", "FECHA", "TIPO_MONEDA", "DOCUMENTO", "#_DOCUMENTO", "CONDICION", "RUC", "RAZON_SOCIAL", "VVENTA", "VALOR_DE_VENTA", "IGV", "BOLSA", "ICB", "TOTAL", "TIPO_CAMBIO", "GLOSA", "CUENTA", "AFECTACION", 'CONDICION DEL CONTRIBUYENTE', 'ESTADO DEL CONTRIBUYENTE'];
                $sheet->fromArray($encabezado, null, 'A1');

                // Forzar columna E como texto para preservar formato de serie-correlativo
                $sheet->getStyle('E:E')->getNumberFormat()->setFormatCode('@');

                $filas_compras = [];
                $colored_rows_compras = [];

                foreach ($query as $key => $value) {
                    $filas_compras[] = [
                        date('d/m/Y', strtotime($periodo)),
                        date('d/m/Y', strtotime($value["fecha"])),
                        $value["tipo_moneda"],
                        $value['documento'],
                        $value['numero_documento'],
                        $value['condicion'],
                        $value['ruc'],
                        $value['razon_social'],
                        $value['vventa'],
                        $value['valor_venta'],
                        $value['igv'],
                        $value['bolsa'],
                        $value['icb'],
                        $value['total'],
                        $value['tipo_cambio'],
                        $value['glosa'],
                        $value['cuenta'],
                        $value['afectacion'],
                        $value['condicion_contribuyente'],
                        $value['estado_contribuyente'],
                    ];
                    if ($value['color'] !== '') {
                        $colored_rows_compras[$key + 2] = $value['color'];
                    }
                }

                if (!empty($filas_compras)) {
                    $sheet->fromArray($filas_compras, null, 'A2');
                }

                // Aplicar color a fila completa (en vez de celda por celda)
                foreach ($colored_rows_compras as $row_num => $color_val) {
                    $sheet->getStyle("A{$row_num}:T{$row_num}")
                        ->getFill()
                        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setARGB($color_val);
                }

                $file_compras = "MAQUETA_COMPRAS_" . $ruc . "_" . uniqid() . ".xlsx";

                $writer = new Xlsx($spreadsheet);
                $writer->save(WRITEPATH . "maquetas/" . $file_compras);

                $url_compra = $file_compras;
            } else {
                $url_compra = "";
                $bancarizados = "";
                $honorarios_mayores = "";
            }

            if (isset($_POST['fecha_venta'])) {
                $cantidad_venta = count($_POST['fecha_venta']);
                $batch_ventas = [];

                for ($i = 0; $i < $cantidad_venta; $i++) {

                    $ruc_dni_otro = trim($_POST['ruc_venta'][$i]);

                    if (strlen($ruc_dni_otro) == 11) {

                        if (isset($ruc_cache[$ruc_dni_otro])) {
                            $razon_social = $ruc_cache[$ruc_dni_otro]['razon_social'];
                            $condicion_con = $ruc_cache[$ruc_dni_otro]['condicion'];
                            $estado_con = $ruc_cache[$ruc_dni_otro]['estado'];
                        } else {
                            $r = $this->existe_ruc_dni($ruc_dni_otro, 2);
                            if ($r == 0) {
                                $data_cliente = $this->api_dni_ruc("ruc", $ruc_dni_otro);
                                if (isset($data_cliente['data'])) {
                                    $razon_social = $data_cliente['data']->razon_social;
                                    $condicion_con = $data_cliente['data']->condicion;
                                    $estado_con = $data_cliente['data']->estado;
                                } else {
                                    $razon_social = "ERROR";
                                    $condicion_con = "ERROR";
                                    $estado_con = "ERROR";
                                }
                            } else {
                                $razon_social = $r['razon_social'];
                                $condicion_con = $r['condicion_contribuyente'];
                                $estado_con = $r['estado_contribuyente'];
                            }
                            $ruc_cache[$ruc_dni_otro] = [
                                'razon_social' => $razon_social,
                                'condicion' => $condicion_con,
                                'estado' => $estado_con,
                            ];
                        }
                    } else {

                        if ($ruc_dni_otro == "00000001") {
                            $razon_social = "VARIOS";
                            $condicion_con = "";
                            $estado_con = "";
                        } else {

                            if (isset($ruc_cache[$ruc_dni_otro])) {
                                $razon_social = $ruc_cache[$ruc_dni_otro]['razon_social'];
                                $condicion_con = "";
                                $estado_con = "";
                            } else {
                                $r = $this->existe_ruc_dni($ruc_dni_otro, 2);
                                if ($r == 0) {
                                    if (strlen($ruc_dni_otro) == 8) {
                                        $data_cliente = $this->api_dni_ruc("dni", $ruc_dni_otro);
                                        if (isset($data_cliente['data'])) {
                                            $razon_social = $data_cliente['data']->nombre;
                                            $ruc_cache[$ruc_dni_otro] = ['razon_social' => $razon_social, 'condicion' => '', 'estado' => ''];
                                        } else {
                                            $razon_social = $_POST['name_razon'][$i];
                                        }
                                    } else {
                                        $razon_social = $_POST['name_razon'][$i];
                                    }
                                    $condicion_con = "";
                                    $estado_con = "";
                                } else {
                                    $razon_social = $r['razon_social'];
                                    $condicion_con = "";
                                    $estado_con = "";
                                    $ruc_cache[$ruc_dni_otro] = ['razon_social' => $razon_social, 'condicion' => '', 'estado' => ''];
                                }
                            }
                        }
                    }

                    $insertar = array(
                        "fecha" => $_POST['fecha_venta'][$i],
                        "tipo_moneda" => $_POST['moneda_venta'][$i],
                        "documento" => $_POST['comprobante_venta'][$i],
                        "numero_documento" => $_POST['numero_venta'][$i],
                        "condicion" => $_POST['condicion_venta'][$i],
                        "ruc" => $_POST['ruc_venta'][$i],
                        "razon_social" => $razon_social,
                        "vventa" => $_POST['vventa_venta'][$i],
                        "valor_venta" => $_POST['valor_v'][$i],
                        "igv" => $_POST['igv_venta'][$i],
                        "bolsa" => $_POST['bolsa_venta'][$i],
                        "icb" => $_POST['icb_venta'][$i],
                        "total" => $_POST['total_venta'][$i],
                        "tipo_cambio" => $_POST['tipo_cambio_venta'][$i],
                        "glosa" => $_POST['glosa_venta'][$i],
                        "cuenta" => $_POST['cuenta_venta'][$i],
                        "afectacion" => $_POST['afectacion_venta'][$i],
                        "fecha_registro" => $fecha_registro,
                        "estado" => "ACEPTADO",
                        "color" => "",
                        "condicion_contribuyente" => $condicion_con,
                        "estado_contribuyente" => $estado_con,
                        "tipo" => $_POST['tipo'][$i],
                        "referencia" => $_POST['referencia'][$i],
                        "fecha_referencia" => $_POST['fecha_referencia'][$i],
                        "contribuyente" => $_POST['ruc_contribuyente']
                    );

                    $batch_ventas[] = $insertar;
                }

                if (!empty($batch_ventas)) {
                    $maqueta_ventas->insertBatch($batch_ventas);
                }

                $all_ventas = $maqueta_ventas->where('fecha_registro', $fecha_registro)->orderBy('fecha', 'ASC')->orderBy('numero_documento', 'ASC')->findAll();
                $grouped_ventas = ['FACTURA' => [], 'BOLETA' => [], 'NOTA DE CREDITO' => [], 'NOTA DE DEBITO' => []];
                foreach ($all_ventas as $v) {
                    if (isset($grouped_ventas[$v['documento']])) {
                        $grouped_ventas[$v['documento']][] = $v;
                    }
                }
                $query_venta        = $grouped_ventas['FACTURA'];
                $query_venta_boletas = $grouped_ventas['BOLETA'];
                $query_notas_credito = $grouped_ventas['NOTA DE CREDITO'];
                $query_notas_debito  = $grouped_ventas['NOTA DE DEBITO'];

                $spreadsheet = new Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();
                $sheet->setTitle("Maqueta de ventas");

                $sheet->getStyle('A1:V1')->applyFromArray(['font' => ['bold' => true]]);

                $encabezado = ["FECHA", "TIPO_MONEDA", "DOCUMENTO", "#_DOCUMENTO", "CONDICION", "RUC", "RAZON_SOCIAL", "VVENTA", "VALOR_DE_VENTA", "IGV", "BOLSA", "ICB", "TOTAL", "TIPO_CAMBIO", "GLOSA", "CUENTA", "AFECTACION", 'CONDICION DEL CONTRIBUYENTE', 'ESTADO DEL CONTRIBUYENTE', 'TIPO', 'REFERENCIA', 'FECHA REFERENCIA'];
                $sheet->fromArray($encabezado, null, 'A1');

                // Forzar columna D como texto para preservar formato de serie-correlativo
                $sheet->getStyle('D:D')->getNumberFormat()->setFormatCode('@');

                $filas_ventas = [];

                $grupos_venta = [$query_venta, $query_venta_boletas, $query_notas_credito, $query_notas_debito];
                $tiene_referencia = [false, false, true, true];

                foreach ($grupos_venta as $gi => $grupo) {
                    foreach ($grupo as $v) {
                        $filas_ventas[] = [
                            date('d/m/Y', strtotime($v["fecha"])),
                            $v["tipo_moneda"],
                            $v['documento'],
                            $v['numero_documento'],
                            $v['condicion'],
                            $v['ruc'],
                            $v['razon_social'],
                            $v['vventa'],
                            $v['valor_venta'],
                            $v['igv'],
                            $v['bolsa'],
                            $v['icb'],
                            $v['total'],
                            $v['tipo_cambio'],
                            $v['glosa'],
                            $v['cuenta'],
                            $v['afectacion'],
                            $v['condicion_contribuyente'],
                            $v['estado_contribuyente'],
                            $tiene_referencia[$gi] ? $v['tipo'] : "",
                            $tiene_referencia[$gi] ? $v['referencia'] : "",
                            ($tiene_referencia[$gi] && $v['fecha_referencia'] !== '0000:00:00') ? $v['fecha_referencia'] : "",
                        ];
                    }
                }

                if (!empty($filas_ventas)) {
                    $sheet->fromArray($filas_ventas, null, 'A2');
                }

                $file_ventas = "MAQUETA_VENTA_" . $ruc . "_" . uniqid() . '.xlsx';

                $writer = new Xlsx($spreadsheet);
                $writer->save(WRITEPATH . "maquetas/" . $file_ventas);

                $url_venta = $file_ventas;
            } else {
                $url_venta = "";
            }


            $json = array(
                "respuesta" => "ok",
                "mensaje" => "se creo correctamente la maqueta",
                "url_compra" => $url_compra,
                "url_venta" => $url_venta,
                "bancarizados" => $bancarizados,
                "honorarios" => $honorarios_mayores,
                "registro" => $fecha_registro
            );

            echo json_encode($json);
        } catch (Error $e) {
            $json = array(
                "respuesta" => "error",
                "mensaje" => $e->getMessage(),

            );

            echo json_encode($json);
        }
    }

    public function procesar_archivo()
    {
        $tipo = $this->request->getVar('comprobante');
        $file = $this->request->getFile('archivo');
        $cliente = $this->request->getVar('idcliente');

        $name_file = $file->getName();

        try {
            $file->move(WRITEPATH . 'uploads/');

            if ($tipo == 1 || $tipo == 2) {
                $this->leer_pdf_factura($name_file, $cliente);
            }

            if ($tipo == 3) {
                $this->leer_pdf_nota_credito($name_file, $cliente);
            }
        } catch (\Exception  $e) {
            echo json_encode($e);
        }
    }

    public function leer_pdf_factura($name, $cliente)
    {
        try {
            $parser = new Parser();
            //$pdf = $parser->parseFile('NOTA_DE_CREDITO.pdf');
            $pdf = $parser->parseFile(WRITEPATH . 'uploads/' . $name);
            //$pdf = $parser->parseFile('BOLETAS_DE_VENTA.pdf');

            $text = $pdf->getText();

            $text = rawurlencode($text);

            $texto = rawurldecode(str_replace("%0D%0A", "\n", $text));

            //$texto_pdf = nl2br(htmlentities($texto));

            //echo "<pre>"; print_r($pdf);exit;

            $arr = explode("\n", $texto);
            $res = array();
            foreach ($arr as $row) {
                $res[] = trim($row);
            }

            //echo "<pre>"; print_r($res);exit;

            $indices = array();

            $comp = "";

            for ($i = 0; $i < count($res); $i++) {
                if (substr($res[$i], 0, 2) == "EB" || substr($res[$i], 0, 2) == "E0") {
                    array_push($indices, $i);
                }

                if (substr($res[$i], 0, 2) == "EB") {
                    $comp = "BOLETA";
                } else {
                    $comp = "FACTURA";
                }
            }

            $array_unido = array();

            for ($i = 0; $i < count($indices); $i++) {
                if ($i == (count($indices) - 1)) {
                    $resta = count($res) - $indices[$i];
                } else {
                    $resta = $indices[$i + 1] - $indices[$i];
                }

                $intervalo = $resta - 1;

                $unido = $res[$indices[$i]];

                for ($j = 1; $j <= $intervalo; $j++) {
                    $del =  str_replace(" ", "", $res[$indices[$i] + $j]);
                    $unido .= " " . $del;
                }

                array_push($array_unido, $unido);
            }

            //echo "<pre>"; print_r($array_unido);exit;

            $data_facturas = array();
            $data_repetidos = array();

            foreach ($array_unido as $key => $value) {
                $sep = explode("$", $value);
                $moneda = "D";

                if (count($sep) == 1) {
                    $sep = explode("S/", $value);
                    $moneda = "S";

                    if (count($sep) == 1) {
                        $sep = explode("S /", $value);
                    }
                }

                //echo "<pre>"; print_r($sep);

                $izquierda = str_replace(" ", "", $sep[0]);
                $derecha = str_replace(" ", "", $sep[1]);

                //echo "<pre>"; print_r($derecha);

                //aca modificando
                $slash = explode('/', $derecha);

                //echo "<pre>"; print_r($slash);

                $dia = trim(substr($slash[0], -2));
                $mes = trim($slash[1]);
                $anio = trim($slash[2]);

                $monto = trim(substr($slash[0], 0, -2));

                $first = explode("-", $izquierda);

                $serie = $first[0];

                if (strlen($serie) == 4) {
                    $num_ruc = trim($first[1]);

                    $ruc = substr($num_ruc, -11);


                    if (strlen($anio) == 4) {
                        $num = trim(substr($num_ruc, 0, -11));
                    } else {
                        $cn = strlen($anio);
                        $r = - ($cn - 4);
                        $num = trim(substr($anio, $r));

                        $anio = substr($anio, 0, 4);
                    }

                    $fecha_date = $anio . "-" . $mes . "-" . $dia;

                    if ($moneda == "D") {
                        $api_tipo_cambio = $this->api_tipo_cambio($fecha_date);
                        $tipo_cambio = $api_tipo_cambio->data->venta;
                    } else {
                        $tipo_cambio = 1;
                    }

                    $serie_numero = trim($serie) . "-" . $num;

                    $compras_maqueta = $this->comprobar_repetido_comprobantes($serie_numero, $cliente, $ruc, $comp);

                    if ($compras_maqueta == "no existe") {
                        $object = array(
                            "serie" => trim($serie),
                            "numero" => $num,
                            "ruc" => $ruc,
                            "monto" => $monto,
                            "fecha" => $fecha_date,
                            "moneda" => $moneda,
                            "tipo_cambio" => $tipo_cambio,
                            "comprobante" => $comp
                        );

                        array_push($data_facturas, $object);
                    } else {

                        $repe = array(
                            "documento" => trim($serie) . "-" . $num,
                            "proveedor" => $ruc
                        );
                        array_push($data_repetidos, $repe);
                    }
                }
            }

            $datos = array(
                "no_existe" => $data_facturas,
                "existe" => $data_repetidos
            );

            //echo "<pre>"; print_r($data_facturas);

            //echo json_encode(ROOTPATH); exit;

            unlink(ROOTPATH . $name);

            echo json_encode($datos);
        } catch (\Exception $e) {
            echo json_encode($e);
        }
    }

    public function leer_pdf_nota_credito($name, $cliente)
    {
        $parser = new Parser();
        //$pdf = $parser->parseFile('NOTA_DE_CREDITO.pdf');
        $pdf = $parser->parseFile(WRITEPATH . 'uploads/' . $name);
        //$pdf = $parser->parseFile('BOLETAS_DE_VENTA.pdf');

        $text = $pdf->getText();

        $text = rawurlencode($text);

        $texto = rawurldecode(str_replace("%0D%0A", "\n", $text));

        //$texto_pdf = nl2br(htmlentities($texto));

        //echo $texto;

        $arr = explode("\n", $texto);
        $res = array();
        foreach ($arr as $row) {
            $res[] = trim($row);
        }

        //echo "<pre>"; print_r($res);exit;

        $indices = array();

        $comp = "";

        for ($i = 0; $i < count($res); $i++) {
            if (substr($res[$i], 0, 2) == "EB" || substr($res[$i], 0, 2) == "E0") {
                array_push($indices, $i);
            }
        }

        $array_unido = array();

        for ($i = 0; $i < count($indices); $i++) {
            if ($i == (count($indices) - 1)) {
                $resta = count($res) - $indices[$i];
            } else {
                $resta = $indices[$i + 1] - $indices[$i];
            }

            $intervalo = $resta - 1;

            $unido = $res[$indices[$i]];

            for ($j = 1; $j <= $intervalo; $j++) {
                $del =  str_replace(" ", "", $res[$indices[$i] + $j]);
                $unido .= " " . $del;
            }

            array_push($array_unido, $unido);
        }

        //echo "<pre>"; print_r($array_unido);exit;

        $data_facturas = array();
        $data_repetidos = array();

        foreach ($array_unido as $key => $value) {
            $sep = explode("$", $value);
            $moneda = "D";

            if (count($sep) == 1) {
                $sep = explode("S/", $value);
                $moneda = "S";

                if (count($sep) == 1) {
                    $sep = explode("S /", $value);
                }
            }

            //echo "<pre>"; print_r($sep);

            $izquierda = str_replace(" ", "", $sep[0]);
            $derecha = str_replace(" ", "", $sep[1]);

            $fecha = trim(substr($derecha, -10));
            $monto = trim(substr($derecha, 0, -10));

            $form_date = explode("/", $fecha);
            $fecha_date = $form_date[2] . "-" . $form_date[1] . "-" . $form_date[0];

            if ($moneda == "D") {
                $api_tipo_cambio = $this->api_tipo_cambio($fecha_date);
                $tipo_cambio = $api_tipo_cambio->data->venta;
            } else {
                $tipo_cambio = 1;
            }

            $data_iz = explode("-", $izquierda);

            $serie_numero = trim($data_iz[0]) . "-" . trim(substr($data_iz[1], 0, -4));

            $compras_maqueta = $this->comprobar_repetido_comprobantes($serie_numero, $cliente, trim(substr($data_iz[2], -11)), $comp);


            if ($compras_maqueta == "no existe") {
                $object = array(
                    "serie" => $data_iz[0],
                    "numero" => trim(substr($data_iz[1], 0, -4)),
                    "ruc" => trim(substr($data_iz[2], -11)),
                    "monto" => $monto,
                    "fecha" => $fecha_date,
                    "moneda" => $moneda,
                    "tipo_cambio" => $tipo_cambio,
                    "comprobante" => "NOTA DE CREDITO",
                    "tipo" => '01',
                    "referencia" => trim(substr($data_iz[1], -4)) . "-" . trim(substr($data_iz[2], 0, -11))
                );

                array_push($data_facturas, $object);
            } else {

                $repe = array(
                    "documento" => trim($data_iz[0]) . "-" . trim(substr($data_iz[1], 0, -4)),
                    "proveedor" => trim(substr($data_iz[2], -11))
                );
                array_push($data_repetidos, $repe);
            }
        }

        unlink(ROOTPATH . $name);

        $datos = array(
            "no_existe" => $data_facturas,
            "existe" => $data_repetidos
        );

        //echo "<pre>"; print_r($data_facturas);exit;
        echo json_encode($datos);
    }

    public function tipo_cambio($fecha)
    {
        $date = date('Y-m-d', strtotime($fecha));

        $tipo_cambio = $this->api_tipo_cambio($date);
        $tipo_cambio = $tipo_cambio->data;

        echo json_encode($tipo_cambio->venta);
    }

    public function maqueta_ventas()
    {
        $file_factura = $this->request->getFile('archivo_factura');
        $file_boleta = $this->request->getFile('archivo_boleta');
        $file_nota_credito = $this->request->getFile('archivo_nota_credito');
        $file_nota_debito = $this->request->getFile('archivo_nota_debito');
        $glosa = $this->request->getVar('glosa_modal');
        $cuenta = $this->request->getVar('cuenta_modal');
        $check_igv = $this->request->getVar('check_igv');
        $contri = $this->request->getVar('idcliente');

        if (isset($check_igv)) {
            $igv = 1;
        } else {
            $igv = 0;
        }

        $name_file_factura = $file_factura->getName();
        $name_file_boleta = $file_boleta->getName();
        $name_file_nota_credito = $file_nota_credito->getName();
        $name_file_nota_debito = $file_nota_debito->getName();

        if ($name_file_factura != "") {
            $file_factura->move(WRITEPATH . 'uploads/');
            //$facturas = $this->leer_pdf_factura_venta($name_file_factura, $igv, $contri);

            //vamos aplicar la api de python para leer pdfs
            $datos_factura = $this->apiLoadPdtFileFactura(WRITEPATH . 'uploads/' . $name_file_factura);

            $facturas = $this->getDatosFactura($datos_factura['facturas'], $igv, $contri);

            unlink(WRITEPATH . 'uploads/' . $name_file_factura);
        } else {
            $facturas = [];
        }

        if ($name_file_boleta != "") {
            $file_boleta->move(WRITEPATH . 'uploads/');

            $datos_boleta = $this->apiLoadPdtFileBoleta(WRITEPATH . 'uploads/' . $name_file_boleta);

            $boletas = $this->getDatosBoleta($datos_boleta['data'], $igv);

            unlink(WRITEPATH . 'uploads/' . $name_file_boleta);
        } else {
            $boletas = [];
        }

        if ($name_file_nota_credito != "") {
            $file_nota_credito->move(WRITEPATH . 'uploads/');

            $datos_nota = $this->apiLoadPdtFileNotaCredito(WRITEPATH . 'uploads/' . $name_file_nota_credito);

            $notas_credito = $this->getDatosNota($datos_nota['comprobantes'], $igv);

            unlink(WRITEPATH . 'uploads/' . $name_file_nota_credito);

            //$notas_credito = $this->leer_pdf_nota_credito_venta($name_file_nota_credito, $igv, $contri);
        } else {
            $notas_credito = [];
        }

        if ($name_file_nota_debito != "") {
            $file_nota_debito->move(WRITEPATH . 'uploads/');
            $notas_debito = $this->leer_pdf_nota_debito_venta($name_file_nota_debito, $igv, $contri);
        } else {
            $notas_debito = [];
        }

        $json = array(
            "facturas" => $facturas,
            "boletas" => $boletas,
            "notas_credito" => $notas_credito,
            "notas_debito" => $notas_debito,
            "glosa" => $glosa,
            "cuenta" => $cuenta,
            "igv" => $igv
        );

        echo json_encode($json);
    }

    public function getDatosFactura($datos, $igv, $contribuyente)
    {
        $data_facturas = [];

        $comp = "FACTURA";

        foreach ($datos as $key => $value) {

            $fecha = $value['fecha'];

            if ($value['tipo_moneda'] === "D") {
                $tipo_cambio = $this->api_tipo_cambio_base_datos($fecha);
                $tipo_cambio = $tipo_cambio->data->venta;
            } else {
                $tipo_cambio = 1;
            }

            $total = $value['monto'];

            if ($igv == 1) {
                $valor_venta = $total / 1.18;
                $total_igv = $valor_venta * 0.18;
            } else {
                $total_igv = 0;
                $valor_venta = $total;
            }

            $object = array(
                "serie" => $value['serie'],
                "numero" => $value['correlativo'],
                "ruc" => $value['ruc'],
                "monto" => $total,
                "fecha" => $fecha,
                "moneda" => $value['tipo_moneda'],
                "tipo_cambio" => $tipo_cambio,
                "comprobante" => $comp,
                "valor_venta" => number_format($valor_venta, 2, '.', ''),
                "total_igv" => number_format($total_igv, 2, '.', ''),
                "razon" => "",
                "tipo" => "",
                "referencia" => "",
                "fecha_referencia" => ""
            );

            array_push($data_facturas, $object);
        }

        return $data_facturas;
    }

    public function getDatosBoleta($datos, $igv)
    {
        $data_boletas = [];

        for ($i = 0; $i < count($datos); $i++) {

            $tipo_cambio = 1;
            $fecha = $datos[$i]['fecha'];
            $numero_documento = $datos[$i]['documento'];
            $monto = $datos[$i]['monto'];
            $moneda = $datos[$i]['moneda'];

            if ($moneda === '$' || $moneda === 'D') {
                $tipo_cambio = $this->api_tipo_cambio_base_datos($fecha);
                $tipo_cambio = $tipo_cambio->data->venta;
            }

            $monto_real = $datos[$i]['monto'] * $tipo_cambio;

            if ($monto_real < 700) {
                $numero_documento = "00000001";
            }

            if ($igv == 1) {

                $valor_venta = $monto / 1.18;
                $total_igv = $valor_venta * 0.18;
            } else {
                $total_igv = 0;
                $valor_venta = $monto;
            }

            $object = array(
                "serie" => $datos[$i]['serie'],
                "numero" => $datos[$i]['correlativo'],
                "ruc" => $numero_documento,
                "monto" => $monto,
                "fecha" => $fecha,
                "moneda" => $moneda,
                "tipo_cambio" => $tipo_cambio,
                "comprobante" => "BOLETA",
                "valor_venta" => number_format($valor_venta, 2, '.', ''),
                "total_igv" => number_format($total_igv, 2, '.', ''),
                "razon" => "",
                "tipo" => "",
                "referencia" => "",
                "fecha_referencia" => ""
            );

            array_push($data_boletas, $object);
        }

        return $data_boletas;
    }

    public function getDatosNota($datos, $igv)
    {
        $data_notas = [];

        foreach ($datos as $key => $value) {

            $tipo_cambio = 1;
            $fecha = $value['fecha'];
            $numero_documento = $value['documento'];
            $monto = $value['monto'];
            $moneda = $value['moneda'];

            if ($moneda === 'D') {
                $tipo_cambio = $this->api_tipo_cambio_base_datos($fecha);
                $tipo_cambio = $tipo_cambio->data->venta;
            }

            if ($igv == 1) {

                $valor_venta = $monto / 1.18;
                $total_igv = $valor_venta * 0.18;
            } else {
                $total_igv = 0;
                $valor_venta = $monto;
            }

            if (substr($value['serie_asociada'], 0, 2) == "E0") {
                $tipo = "01";
            } else {
                $tipo = "03";
            }

            $object = array(
                "serie" => $value['serie'],
                "numero" => $value['correlativo'],
                "ruc" => $numero_documento,
                "monto" => "-" . $monto,
                "fecha" => $fecha,
                "moneda" => $moneda,
                "tipo_cambio" => $tipo_cambio,
                "comprobante" => "NOTA DE CREDITO",
                "valor_venta" => "-" . number_format($valor_venta, 2, '.', ''),
                "total_igv" => "-" . number_format($total_igv, 2, '.', ''),
                "razon" => "",
                "tipo" => $tipo,
                "referencia" => $value['serie_asociada'] . "-" . $value['correlativo_asociado'],
                "fecha_referencia" => ""
            );

            array_push($data_notas, $object);
        }

        return $data_notas;
    }

    public function leer_pdf_nota_debito_venta($name, $igv, $contribuyente)
    {
        $parser = new Parser();
        //$pdf = $parser->parseFile('NOTA_DE_CREDITO.pdf');
        $pdf = $parser->parseFile(WRITEPATH . 'uploads/' . $name);
        //$pdf = $parser->parseFile('BOLETAS_DE_VENTA.pdf');

        $text = $pdf->getText();

        $text = rawurlencode($text);

        $texto = rawurldecode(str_replace("%0D%0A", "\n", $text));

        //$texto_pdf = nl2br(htmlentities($texto));

        //echo $texto;exit;

        $arr = explode("\n", $texto);
        $res = array();
        foreach ($arr as $row) {
            $res[] = trim($row);
        }

        $indices = array();

        $comp = "";

        for ($i = 0; $i < count($res); $i++) {
            if (substr($res[$i], 0, 2) == "EB" || substr($res[$i], 0, 2) == "E0") {
                array_push($indices, $i);
            }

            if (substr($res[$i], 0, 2) == "EB") {
                $comp = "NOTA DE DEBITO";
            } else {
                $comp = "NOTA DE DEBITO";
            }
        }

        $array_unido = array();

        for ($i = 0; $i < count($indices); $i++) {
            if ($i == (count($indices) - 1)) {
                $resta = count($res) - $indices[$i];
            } else {
                $resta = $indices[$i + 1] - $indices[$i];
            }

            $intervalo = $resta - 1;

            $unido = $res[$indices[$i]];

            for ($j = 1; $j <= $intervalo; $j++) {
                $del =  str_replace(" ", "", $res[$indices[$i] + $j]);
                $unido .= " " . $del;
            }

            $unido = preg_replace(['/\s+/', '/^\s|\s$/'], [' ', ''], $unido);

            array_push($array_unido, $unido);
        }

        //echo "<pre>"; print_r($array_unido);
        $data_nota = array();

        for ($j = 0; $j < count($array_unido); $j++) {

            $sep = explode("$", $array_unido[$j]);
            $moneda = "D";

            if (count($sep) == 1) {
                $sep = explode("S/", $array_unido[$j]);
                $moneda = "S";
            }

            $zurdo = $sep[0];
            $diestro = $sep[1];

            $data_diestro = explode(' ', $diestro);

            $number_monto = str_replace(",", "", $data_diestro[0]);

            $monto = number_format($number_monto, 2, '.', '');

            $fecha = str_replace("/", "-", $data_diestro[1]);

            $fecha_emision = date('Y-m-d', strtotime($fecha));

            $data_zurdo = explode("-", $zurdo);

            $numero_serieEliminado = explode(" ", trim($data_zurdo[1]));
            $correlativo_nota = $numero_serieEliminado[0];
            $serie_doc = $numero_serieEliminado[1];

            $numero_doc_ruc = explode(" ", trim($data_zurdo[2]));

            $num_doc = $numero_doc_ruc[0];
            $ruc = $numero_doc_ruc[1];

            if ($moneda == "D") {
                $api_tipo_cambio = $this->api_tipo_cambio(trim($fecha_emision));
                $tipo_cambio = $api_tipo_cambio->data->venta;
            } else {
                $tipo_cambio = 1;
            }

            if ($igv == 1) {
                $valor_venta = $monto / 1.18;
                $total_igv = $monto - $valor_venta;
            } else {
                $valor_venta = $monto;
                $total_igv = 0;
            }

            if (substr($serie_doc, 0, 2) == "E0") {
                $tipo = "01";
            } else {
                $tipo = "03";
            }

            $serie_numero = trim($data_zurdo[0]) . "-" . $correlativo_nota;

            //$verif = $this->verificar_ventas($serie_numero, $ruc, $comp, $contribuyente);

            //if ($verif == "no existe") {
            $object = array(
                "serie" => trim($data_zurdo[0]),
                "numero" => $correlativo_nota,
                "ruc" => $ruc,
                "monto" => "-" . $monto,
                "fecha" => $fecha_emision,
                "moneda" => $moneda,
                "tipo_cambio" => $tipo_cambio,
                "comprobante" => $comp,
                "valor_venta" => "-" . number_format($valor_venta, 2, '.', ''),
                "total_igv" => "-" . number_format($total_igv, 2, '.', ''),
                "razon" => "",
                "tipo" => $tipo,
                "referencia" => $serie_doc . "-" . $num_doc,
                "fecha_referencia" => ""
            );

            array_push($data_nota, $object);
            //}

            sleep(1);
        }

        return $data_nota;
    }

    public function procesar_rh()
    {
        try {

            $file = $this->request->getFile('archivo_rh');

            $name_file = $file->getName();

            $cliente = $this->request->getVar('idcliente');

            $file->move(WRITEPATH . 'uploads/');

            $txt_file = fopen(WRITEPATH . 'uploads/' . $name_file, 'r');
            $a = 1;

            $unidos = array();

            while ($line = fgets($txt_file)) {
                if ($a > 1) {
                    array_push($unidos, $line);
                }
                $a++;
            }

            $data = array();


            foreach ($unidos as $key => $value) {
                $desp = explode("|", $value);

                if ($desp[3] == "NO ANULADO") {
                    $fecha = explode("/", $desp[0]);

                    if ($desp[11] == "SOLES") {
                        $moneda = "S";
                        $tipo_cambio = 1;
                    } else {
                        $moneda = "D";
                        $tipo_cambio = 1;
                    }

                    if ($desp[1] == 'RH') {
                        $doc = 'RXH';
                        $monto = trim($desp[12]);
                        $documento = "HONORARIOS";
                    } else {
                        $doc = 'NC';
                        $monto = "-" . trim($desp[12]);
                        $documento = "NOTA DE CREDITO";
                    }

                    $serie_numero = trim($desp[2]) . "-" . $doc;

                    $data_verificacion_maqueta = $this->comprobar_repetido_comprobantes($serie_numero, $cliente, trim($desp[5]), $documento);

                    if ($data_verificacion_maqueta == "no existe") {
                        $object = array(
                            "num_doc" => trim($desp[2]),
                            "doc" => $doc,
                            "ruc" => trim($desp[5]),
                            "monto" => $monto,
                            "fecha" => $fecha[2] . "-" . $fecha[1] . "-" . $fecha[0],
                            "fecha_date" => $fecha[0] . "-" . $fecha[1] . "-" . $fecha[2],
                            "moneda" => $moneda,
                            "tipo_cambio" => $tipo_cambio,
                            "comprobante" => $documento,
                            "glosa" => "SERVICIOS PRESTADOS POR TERCEROS",
                            "cuenta" => "63211",
                            "otros_impuestos" => trim($desp[13]),
                            "total_cancelar" => trim($desp[14])
                        );

                        array_push($data, $object);
                    }
                }
            }

            fclose($txt_file);

            echo json_encode($data);

            unlink(WRITEPATH . 'uploads/' . $name_file);
        } catch (\Exception $e) {
            unlink(WRITEPATH . 'uploads/' . $name_file);
            echo json_encode($e->getMessage());
        }
    }

    public function comprobar_duplicidad()
    {
        $serie_numero = $this->request->getVar('serie_numero');
        $cliente = $this->request->getVar('cliente');
        $proveedor = $this->request->getVar('proveedor');
        $tipo_doc = $this->request->getVar('tipo_doc');

        $maq = model('MaquetaComprasModel');

        $consulta = $maq->where('numero_documento', $serie_numero)->where('cliente', $cliente)->where('ruc', $proveedor)->where('documento', $tipo_doc)->first();

        if ($consulta) {
            $json = array(
                "respuesta" => "existe",
                "mensaje" => "Este comprobante ya existe: " . $serie_numero . " en el periodo " . date('d-m-Y', strtotime($consulta['periodo']))
            );
            echo json_encode($json);
        } else {
            $json = array(
                "respuesta" => "ok"
            );
            echo json_encode($json);
        }
    }

    public function comprobar_repetido_comprobantes($serie_numero, $cliente, $proveedor, $tipo_doc)
    {
        $maq = model('MaquetaComprasModel');

        $consulta = $maq->where('numero_documento', $serie_numero)->where('cliente', $cliente)->where('ruc', $proveedor)->where('documento', $tipo_doc)->first();

        if ($consulta) {
            $data = "existe";
        } else {
            $data = "no existe";
        }

        return $data;
    }

    public function consultar_compra()
    {
        $ruc = $this->request->getVar('ruc');
        $serie = $this->request->getVar('serie');
        $correlativo = $this->request->getVar('correlativo');
        $ruc_activo = $this->request->getVar('ruc_activo');

        $activos = model('Ruc_activosModel');

        $datos_ruc = $activos->where('ruc', $ruc_activo)->first();

        $usuario_sol_principal = $datos_ruc['usuario_sol_principal'];
        $clave_sol_principal = $datos_ruc['clave_sol_principal'];

        $data = $this->api_consulta_compra($ruc, $serie, $correlativo, $ruc_activo, $usuario_sol_principal, $clave_sol_principal);

        echo json_encode($data);
    }

    public function api_consulta_compra($ruc, $serie, $correlativo, $ruc_activo, $usuario, $clave)
    {
        $data['token_cliente'] = 'facturalaya_erickpeso_05jFE7sAOudi8j0'; //KEY para que puedas consumir nuestra api
        $data['ruc_proveedor'] = "20603670249"; //Tu número de RUC, el cuál será responsable por los datos enviados en todos los json

        $token = 'facturalaya_erickpeso_05jFE7sAOudi8j0';

        $data['secret_data'] = array(
            "tipo_certificado"        => "pse_facturalaya", //no cambiar
            "tipo_proceso"             => "produccion", //prueba, produccion: aquí si deseas enviar en prueba o producción
        );

        //Datos del Receptor del comprobante de pago
        $data['receptor'] = array(
            'ruc'                         => $ruc_activo, //RUC del contribuyente, de tu cliente
            'tipo_doc'                     => '6', //no cambiar (6: RUC)
        );

        //Datos para recuperar el comprobante electrónico
        $data['ruc_emisor'] = $ruc; //RUC del Proveedor
        $data['serie'] = $serie; //Serie del Comprobante
        $data['correlativo'] = $correlativo; //Correlativo del comprobante electrónico

        //datos del cliente que está consultando
        $data['sol_ruc'] = $ruc_activo; //RUC SOL PRINCIPAL
        $data['sol_user'] = $usuario; //USUARIO SOL PRINCIPAL
        $data['sol_password'] = $clave; //PASSOWORD USUARIO SOL PRINCIPAL

        $ruta = 'https://facturalahoy.com/api/facturalaya/recuperar_cpe';
        $data_json = json_encode($data);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $ruta);
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array(
                'Authorization: Token token="' . $token . '"',
                'Content-Type: application/json',
            )
        );
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $respuesta  = curl_exec($ch);
        if (curl_error($ch)) {
            $error_msg = curl_error($ch);
        }
        curl_close($ch);

        if (isset($error_msg)) {
            $resp['respuesta'] = 'error';
            $resp['titulo'] = 'Error';
            $resp['data'] = '';
            $resp['encontrado'] = false;
            $resp['mensaje'] = 'Error en Api de Búsqueda';
            $resp['errores_curl'] = $error_msg;
            echo json_encode($resp);
            exit();
        }

        return $respuesta;
        //exit();
    }

    public function verificar_ventas($serie_numero, $proveedor, $tipo_doc, $contribuyente)
    {
        $maq = model('MaquetaVentaModel');

        $consulta = $maq->where('numero_documento', $serie_numero)->where('contribuyente', $contribuyente)->where('ruc', $proveedor)->where('documento', $tipo_doc)->first();

        if ($consulta) {
            $data = "existe";
        } else {
            $data = "no existe";
        }

        return $data;
    }

    public function completar_glosa()
    {
        $buscar = $this->request->getVar('term');
        $plan = $this->request->getVar('plan');

        $plan_cuentas = model('PlanCuentasModel');

        $resultado = $plan_cuentas->where('antiguedad', $plan)->like('glosa', $buscar)->findAll();

        $array = array();

        foreach ($resultado as $key => $value) {
            $data = array(
                'value' => $value['glosa'],
                'id' => $value['cuenta']
            );

            array_push($array, $data);
        }

        // Crear la respuesta en formato JSONP
        $response = json_encode($array);

        // Preparar la respuesta para ser enviada con la función de retroceso de llamada
        $jsonp_response = $_GET['callback'] . '(' . $response . ')';

        // Establecer las cabeceras adecuadas para indicar que se trata de una respuesta JSONP
        header('Content-type: application/javascript');
        header('Access-Control-Allow-Origin: *');

        // Enviar la respuesta JSONP
        echo $jsonp_response;
    }

    public function sugerir_glosa()
    {
        $buscar = $this->request->getVar('term');
        $plan = $this->request->getVar('plan');

        $plan_cuentas = model('PlanCuentasModel');

        $resultado = $plan_cuentas->where('antiguedad', $plan)->like('cuenta', $buscar)->findAll();

        $array = array();

        foreach ($resultado as $key => $value) {
            $data = array(
                'value' => $value['cuenta'],
                'label' => $value['cuenta'],
                'desc' => $value['glosa'],
                'id' => $value['glosa']
            );

            array_push($array, $data);
        }

        // Crear la respuesta en formato JSONP
        $response = json_encode($array);

        // Preparar la respuesta para ser enviada con la función de retroceso de llamada
        $jsonp_response = $_GET['callback'] . '(' . $response . ')';

        // Establecer las cabeceras adecuadas para indicar que se trata de una respuesta JSONP
        header('Content-type: application/javascript');
        header('Access-Control-Allow-Origin: *');

        // Enviar la respuesta JSONP
        echo $jsonp_response;
    }

    public function completar_ruc()
    {
        $buscar = $this->request->getVar('term');

        $mcompras = model('MaquetaComprasModel');

        $mcompras->query("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");

        $datos = $mcompras->distinct()->select('ruc, razon_social')->like('ruc', $buscar)->orlike('razon_social', $buscar)->groupBy('ruc')->findAll();

        //echo json_encode($datos);exit;

        $array = array();

        foreach ($datos as $key => $value) {
            $data = array(
                'value' => $value['ruc'],
                'label' => $value['ruc'],
                'desc' => $value['razon_social'],
                'id' => $value['ruc']
            );

            array_push($array, $data);
        }

        // Crear la respuesta en formato JSONP
        $response = json_encode($array);

        // Preparar la respuesta para ser enviada con la función de retroceso de llamada
        $jsonp_response = $_GET['callback'] . '(' . $response . ')';

        // Establecer las cabeceras adecuadas para indicar que se trata de una respuesta JSONP
        header('Content-type: application/javascript');
        header('Access-Control-Allow-Origin: *');

        // Enviar la respuesta JSONP
        echo $jsonp_response;
    }

    public function download_maqueta()
    {
        $periodo = $this->request->getVar('periodo');

        $periodo = $periodo . "-01";

        $ruc = $this->request->getVar('ruc');

        $maqueta_compra = model('MaquetaComprasModel');

        $maqueta_compra->query("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");

        $datos = $maqueta_compra->where('periodo', $periodo)->where('cliente', $ruc)->groupBy('numero_documento')->orderBy('fecha', 'ASC')->findAll();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setTitle("Maqueta de compras");

        $styleTitle = [
            'font' => [
                'bold' => true,
                //'size' => 20
            ]
        ];

        $sheet->getStyle('A1')->applyFromArray($styleTitle);
        $sheet->getStyle('B1')->applyFromArray($styleTitle);
        $sheet->getStyle('C1')->applyFromArray($styleTitle);
        $sheet->getStyle('D1')->applyFromArray($styleTitle);
        $sheet->getStyle('E1')->applyFromArray($styleTitle);
        $sheet->getStyle('F1')->applyFromArray($styleTitle);
        $sheet->getStyle('G1')->applyFromArray($styleTitle);
        $sheet->getStyle('H1')->applyFromArray($styleTitle);
        $sheet->getStyle('I1')->applyFromArray($styleTitle);
        $sheet->getStyle('J1')->applyFromArray($styleTitle);
        $sheet->getStyle('K1')->applyFromArray($styleTitle);
        $sheet->getStyle('L1')->applyFromArray($styleTitle);
        $sheet->getStyle('M1')->applyFromArray($styleTitle);
        $sheet->getStyle('N1')->applyFromArray($styleTitle);
        $sheet->getStyle('O1')->applyFromArray($styleTitle);
        $sheet->getStyle('P1')->applyFromArray($styleTitle);
        $sheet->getStyle('Q1')->applyFromArray($styleTitle);
        $sheet->getStyle('R1')->applyFromArray($styleTitle);
        $sheet->getStyle('S1')->applyFromArray($styleTitle);
        //$sheet->getStyle('T1')->applyFromArray($styleTitle);

        $encabezado = ["PERIODO", "FECHA", "TIPO_MONEDA", "DOCUMENTO", "#_DOCUMENTO", "CONDICION", "RUC", "RAZON_SOCIAL", "VVENTA", "VALOR_DE_VENTA", "IGV", "BOLSA", "ICB", "TOTAL", "TIPO_CAMBIO", "GLOSA", "CUENTA", "AFECTACION", 'CONDICION DEL CONTRIBUYENTE', 'ESTADO DEL CONTRIBUYENTE'];
        # El último argumento es por defecto A1 pero lo pongo para que se explique mejor
        $sheet->fromArray($encabezado, null, 'A1');

        foreach ($datos as $key => $value) {

            $sheet->setCellValueByColumnAndRow(1, $key + 2, date('d/m/Y', strtotime($periodo)));
            $sheet->setCellValueByColumnAndRow(2, $key + 2, date('d/m/Y', strtotime($value["fecha"])));
            $sheet->setCellValueByColumnAndRow(3, $key + 2, $value["tipo_moneda"]);
            $sheet->setCellValueByColumnAndRow(4, $key + 2, $value['documento']);
            //$sheet->setCellValueByColumnAndRow(5, $key + 2, $value['numero_documento']);
            $sheet->getCellByColumnAndRow(5, $key + 2)->setValueExplicit($value['numero_documento'], DataType::TYPE_STRING);
            $sheet->setCellValueByColumnAndRow(6, $key + 2, $value['condicion']);
            $sheet->setCellValueByColumnAndRow(7, $key + 2, $value['ruc']);
            $sheet->setCellValueByColumnAndRow(8, $key + 2, $value['razon_social']);
            $sheet->setCellValueByColumnAndRow(9, $key + 2, $value['vventa']);
            $sheet->setCellValueByColumnAndRow(10, $key + 2, $value['valor_venta']);
            $sheet->setCellValueByColumnAndRow(11, $key + 2, $value['igv']);
            $sheet->setCellValueByColumnAndRow(12, $key + 2, $value['bolsa']);
            $sheet->setCellValueByColumnAndRow(13, $key + 2, $value['icb']);
            $sheet->setCellValueByColumnAndRow(14, $key + 2, $value['total']);
            $sheet->setCellValueByColumnAndRow(15, $key + 2, $value['tipo_cambio']);
            $sheet->setCellValueByColumnAndRow(16, $key + 2, $value['glosa']);
            $sheet->setCellValueByColumnAndRow(17, $key + 2, $value['cuenta']);
            $sheet->setCellValueByColumnAndRow(18, $key + 2, $value['afectacion']);
            //$sheet->setCellValueByColumnAndRow(18, $key + 2, $value['estado']);
            $sheet->setCellValueByColumnAndRow(19, $key + 2, $value['condicion_contribuyente']);
            $sheet->setCellValueByColumnAndRow(20, $key + 2, $value['estado_contribuyente']);
        }

        $file_compras = "MAQUETA_COMPRAS_" . $periodo . "_" . $ruc . "_" . uniqid() . ".xlsx";

        $writer = new Xlsx($spreadsheet);
        $writer->save(WRITEPATH . "uploads/maquetas/" . $file_compras);

        $url_compra = $file_compras;

        $json = array(
            "respuesta" => "ok",
            "mensaje" => "se genero correctamente la maqueta de compras",
            "url_compra" => $url_compra
        );

        echo json_encode($json);
    }

    public function downloadMaquetaPeriodo($file)
    {
        $archivo = WRITEPATH . 'uploads/maquetas/' . $file;
        if (file_exists($archivo)) {
            // Forzar la descarga del archivo
            return $this->response->download($archivo, null);
        } else {
            // Manejar el error, archivo no encontrado
            return $this->response->setStatusCode(404, 'Archivo no encontrado');
        }
    }

    public function comprobantes_abancarizados()
    {
        $periodo = $this->request->getVar('periodo');

        $periodo = $periodo . "-01";

        $ruc = $this->request->getVar('ruc');
        $name = $this->request->getVar('name');

        $name = substr($name, 0, 19);

        $maqueta_compra = model('MaquetaComprasModel');

        $maqueta_compra->query("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");

        //$datos = $maqueta_compra->where('periodo', $periodo)->where('cliente', $ruc)->where('tipo_moneda', 'S')->where('total >=', 2000)->orWhere('tipo_moneda','D')->where('total >=', 500)->groupBy('numero_documento')->orderBy('fecha', 'ASC')->findAll();

        $datos = $maqueta_compra->query("(SELECT * FROM maqueta.maqueta_compras WHERE periodo = '$periodo' and cliente = '$ruc' 
        and ((tipo_moneda = 'S' and total >= 2000) or (tipo_moneda = 'D' and total >= 500)) 
        GROUP BY numero_documento ORDER BY fecha ASC)
        UNION
        SELECT c.*
        FROM maqueta.maqueta_compras c
        INNER JOIN (
            SELECT ruc, fecha, tipo_moneda, SUM(total) as total_sum
            FROM maqueta.maqueta_compras
            WHERE periodo = '$periodo' AND cliente = '$ruc'
            GROUP BY ruc, fecha, tipo_moneda
            HAVING COUNT(*) > 1 AND 
                   ((tipo_moneda = 'S' AND total_sum >= 2000) OR (tipo_moneda = 'D' AND total_sum >= 500))
        ) d ON c.ruc = d.ruc AND c.fecha = d.fecha AND c.tipo_moneda = d.tipo_moneda
        WHERE c.periodo = '$periodo' AND c.cliente = '$ruc' group by numero_documento order by fecha asc, tipo_moneda asc")->getResult();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setTitle($name . " " . date('d-m-Y', strtotime($periodo)));

        $styleTitle = [
            'font' => [
                'bold' => true,
                //'size' => 20
            ]
        ];

        $sheet->getStyle('A1')->applyFromArray($styleTitle);
        $sheet->getStyle('B1')->applyFromArray($styleTitle);
        $sheet->getStyle('C1')->applyFromArray($styleTitle);
        $sheet->getStyle('D1')->applyFromArray($styleTitle);
        $sheet->getStyle('E1')->applyFromArray($styleTitle);
        $sheet->getStyle('F1')->applyFromArray($styleTitle);
        $sheet->getStyle('G1')->applyFromArray($styleTitle);
        $sheet->getStyle('H1')->applyFromArray($styleTitle);
        $sheet->getStyle('I1')->applyFromArray($styleTitle);
        $sheet->getStyle('J1')->applyFromArray($styleTitle);
        $sheet->getStyle('K1')->applyFromArray($styleTitle);
        $sheet->getStyle('L1')->applyFromArray($styleTitle);
        $sheet->getStyle('M1')->applyFromArray($styleTitle);
        //$sheet->getStyle('T1')->applyFromArray($styleTitle);

        $encabezado = ["FECHA", "TIPO_MONEDA", "DOCUMENTO", "#_DOCUMENTO", "RUC", "RAZON_SOCIAL", "VVENTA", "VALOR_DE_VENTA", "IGV", "BOLSA", "ICB", "TOTAL", "TIPO_CAMBIO"];
        # El último argumento es por defecto A1 pero lo pongo para que se explique mejor
        $sheet->fromArray($encabezado, null, 'A1');

        foreach ($datos as $key => $value) {

            $sheet->setCellValueByColumnAndRow(1, $key + 2, date('d/m/Y', strtotime($value->fecha)));
            $sheet->setCellValueByColumnAndRow(2, $key + 2, $value->tipo_moneda);
            $sheet->setCellValueByColumnAndRow(3, $key + 2, $value->documento);
            //$sheet->setCellValueByColumnAndRow(5, $key + 2, $value['numero_documento']);
            $sheet->getCellByColumnAndRow(4, $key + 2)->setValueExplicit($value->numero_documento, DataType::TYPE_STRING);
            $sheet->setCellValueByColumnAndRow(5, $key + 2, $value->ruc);
            $sheet->setCellValueByColumnAndRow(6, $key + 2, $value->razon_social);
            $sheet->setCellValueByColumnAndRow(7, $key + 2, $value->vventa);
            $sheet->setCellValueByColumnAndRow(8, $key + 2, $value->valor_venta);
            $sheet->setCellValueByColumnAndRow(9, $key + 2, $value->igv);
            $sheet->setCellValueByColumnAndRow(10, $key + 2, $value->bolsa);
            $sheet->setCellValueByColumnAndRow(11, $key + 2, $value->icb);
            $sheet->setCellValueByColumnAndRow(12, $key + 2, $value->total);
            $sheet->setCellValueByColumnAndRow(13, $key + 2, $value->tipo_cambio);
        }

        $file_compras = "COMPROBANTES_A_BANCARIZAR_" . $periodo . "_" . $ruc . "_" . uniqid() . ".xlsx";

        $writer = new Xlsx($spreadsheet);
        $writer->save("./public/maquetas/" . $file_compras);

        $url_compra = $file_compras;

        $json = array(
            "respuesta" => "ok",
            "mensaje" => "se genero correctamente la maqueta de compras",
            "url_compra" => $url_compra
        );

        echo json_encode($json);
    }

    public function get_registros()
    {
        $periodo = $this->request->getVar('periodo');

        $periodo = $periodo . "-01";

        $ruc = $this->request->getVar('ruc');

        $maqueta_compras = model('MaquetaComprasModel');

        $maqueta_compras->query("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");

        $datos = $maqueta_compras->where('periodo', $periodo)->where('cliente', $ruc)->groupBy('fecha_registro')->findAll();

        $html = "";
        $html .= '<input type="hidden" name="periodo" id="per" value="' . $periodo . '" />';
        $html .= '<input type="hidden" name="ruc" id="ruc_c" value="' . $ruc . '" />';

        foreach ($datos as $key => $value) {
            $html .= '
            <div class="form-check mb-3">
                <input class="form-check-input" name="registros[]" type="checkbox" id="formCheck' . $key . '" value="' . $value['fecha_registro'] . '">
                <label class="form-check-label" for="formCheck' . $key . '">
                    ' . date('d-m-Y H:i:s', strtotime($value['fecha_registro'])) . '
                </label>
            </div>
            ';
        }

        $data = array(
            "periodo" => $periodo,
            "periodo_formato" => date('d-m-Y', strtotime($periodo)),
            "ruc" => $ruc,
            "check" => $html
        );

        echo json_encode($data);
    }

    public function postMaquetaRegistros()
    {
        if (!isset($_POST['registros'])) {
            $json = array(
                "respuesta" => "error",
                "mensaje" => "Elige una o varias fecha de registro de la maqueta a generar"
            );

            echo json_encode($json);
            exit;
        }

        $periodo = $this->request->getVar('periodo');
        $ruc = $this->request->getVar('ruc');

        $maqueta = model('MaquetaComprasModel');

        $maqueta->query("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");

        $query = $maqueta->whereIn('fecha_registro', $_POST['registros'])->groupBy('numero_documento')->orderBy('fecha', 'ASC')->findAll();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setTitle("Maqueta de compras");

        $styleTitle = [
            'font' => [
                'bold' => true,
                //'size' => 20
            ]
        ];

        $sheet->getStyle('A1')->applyFromArray($styleTitle);
        $sheet->getStyle('B1')->applyFromArray($styleTitle);
        $sheet->getStyle('C1')->applyFromArray($styleTitle);
        $sheet->getStyle('D1')->applyFromArray($styleTitle);
        $sheet->getStyle('E1')->applyFromArray($styleTitle);
        $sheet->getStyle('F1')->applyFromArray($styleTitle);
        $sheet->getStyle('G1')->applyFromArray($styleTitle);
        $sheet->getStyle('H1')->applyFromArray($styleTitle);
        $sheet->getStyle('I1')->applyFromArray($styleTitle);
        $sheet->getStyle('J1')->applyFromArray($styleTitle);
        $sheet->getStyle('K1')->applyFromArray($styleTitle);
        $sheet->getStyle('L1')->applyFromArray($styleTitle);
        $sheet->getStyle('M1')->applyFromArray($styleTitle);
        $sheet->getStyle('N1')->applyFromArray($styleTitle);
        $sheet->getStyle('O1')->applyFromArray($styleTitle);
        $sheet->getStyle('P1')->applyFromArray($styleTitle);
        $sheet->getStyle('Q1')->applyFromArray($styleTitle);
        $sheet->getStyle('R1')->applyFromArray($styleTitle);
        $sheet->getStyle('S1')->applyFromArray($styleTitle);
        //$sheet->getStyle('T1')->applyFromArray($styleTitle);

        $encabezado = ["PERIODO", "FECHA", "TIPO_MONEDA", "DOCUMENTO", "#_DOCUMENTO", "CONDICION", "RUC", "RAZON_SOCIAL", "VVENTA", "VALOR_DE_VENTA", "IGV", "BOLSA", "ICB", "TOTAL", "TIPO_CAMBIO", "GLOSA", "CUENTA", "AFECTACION", 'CONDICION DEL CONTRIBUYENTE', 'ESTADO DEL CONTRIBUYENTE'];
        # El último argumento es por defecto A1 pero lo pongo para que se explique mejor
        $sheet->fromArray($encabezado, null, 'A1');

        foreach ($query as $key => $value) {
            $sheet->setCellValueByColumnAndRow(1, $key + 2, date('d/m/Y', strtotime($periodo)));
            $sheet->setCellValueByColumnAndRow(2, $key + 2, date('d/m/Y', strtotime($value["fecha"])));
            $sheet->setCellValueByColumnAndRow(3, $key + 2, $value["tipo_moneda"]);
            $sheet->setCellValueByColumnAndRow(4, $key + 2, $value['documento']);
            //$sheet->setCellValueByColumnAndRow(5, $key + 2, $value['numero_documento']);
            $sheet->getCellByColumnAndRow(5, $key + 2)->setValueExplicit($value['numero_documento'], DataType::TYPE_STRING);
            $sheet->setCellValueByColumnAndRow(6, $key + 2, $value['condicion']);
            $sheet->setCellValueByColumnAndRow(7, $key + 2, $value['ruc']);
            $sheet->setCellValueByColumnAndRow(8, $key + 2, $value['razon_social']);
            $sheet->setCellValueByColumnAndRow(9, $key + 2, $value['vventa']);
            $sheet->setCellValueByColumnAndRow(10, $key + 2, $value['valor_venta']);
            $sheet->setCellValueByColumnAndRow(11, $key + 2, $value['igv']);
            $sheet->setCellValueByColumnAndRow(12, $key + 2, $value['bolsa']);
            $sheet->setCellValueByColumnAndRow(13, $key + 2, $value['icb']);
            $sheet->setCellValueByColumnAndRow(14, $key + 2, $value['total']);
            $sheet->setCellValueByColumnAndRow(15, $key + 2, $value['tipo_cambio']);
            $sheet->setCellValueByColumnAndRow(16, $key + 2, $value['glosa']);
            $sheet->setCellValueByColumnAndRow(17, $key + 2, $value['cuenta']);
            $sheet->setCellValueByColumnAndRow(18, $key + 2, $value['afectacion']);
            //$sheet->setCellValueByColumnAndRow(18, $key + 2, $value['estado']);
            $sheet->setCellValueByColumnAndRow(19, $key + 2, $value['condicion_contribuyente']);
            $sheet->setCellValueByColumnAndRow(20, $key + 2, $value['estado_contribuyente']);
        }

        $file_compras = "MAQUETA_COMPRAS_" . $ruc . "_" . uniqid() . ".xlsx";

        $writer = new Xlsx($spreadsheet);
        $writer->save(WRITEPATH . 'uploads/maquetas/' . $file_compras);

        $url_compra = $file_compras;

        $json = array(
            "respuesta" => "ok",
            "mensaje" => "se creo correctamente la maqueta",
            "url_compra" => $url_compra
        );

        echo json_encode($json);
    }

    public function downloadMaquetaRegistro($file)
    {
        $archivo = WRITEPATH . 'uploads/maquetas/' . $file;
        if (file_exists($archivo)) {
            // Forzar la descarga del archivo
            return $this->response->download($archivo, null);
        } else {
            // Manejar el error, archivo no encontrado
            return $this->response->setStatusCode(404, 'Archivo no encontrado');
        }
    }

    public function uploadExcel()
    {
        $file = $this->request->getFile('archivoExcel');
        $cliente = $this->request->getVar('customerId');

        $name_file = $file->getName();

        if ($file && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move(ROOTPATH . 'uploads', $newName);

            $spreadsheet = new Spreadsheet();

            $inputFileName = WRITEPATH . 'ROOTPATH/' . $newName;

            // Cargar el archivo Excel
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load(ROOTPATH . 'uploads/' . $newName);

            // Obtener la hoja de trabajo activa
            $worksheet = $spreadsheet->getActiveSheet();

            // Obtener las filas como un array
            $rows = [];
            foreach ($worksheet->getRowIterator() as $row) {
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(FALSE);
                $cells = [];
                foreach ($cellIterator as $cell) {
                    $cells[] = $cell->getValue();
                }
                $rows[] = $cells;
            }

            //echo "<pre>"; print_r($rows);exit;

            // Visualizar el array (para este ejemplo)

            $datos = [];

            for ($i = 1; $i < count($rows); $i++) {
                //$fecha_emision = Date::excelToDateTimeObject($rows[$i]);

                $fila = $rows[$i];

                if ($fila[1] != "") {
                    if (is_numeric($fila[1])) {
                        $fecha_emision = Date::excelToDateTimeObject($fila[1]);
                        $fecha_emision = $fecha_emision->format('Y-m-d');
                    } else {
                        $dt = \DateTime::createFromFormat('d/m/Y', $fila[1]);
                        $fecha_emision = $dt->format('Y-m-d');
                    }

                    $serie_numero = $fila[4] . "-" . $fila[6];
                    $ruc = $fila[9];
                    $valor_venta = $fila[11];
                    $total = $fila[21];

                    $subtotal = $fila[17];
                    $igv = $fila[12];

                    $moneda = $fila[22];

                    if ($moneda === 'PEN') {
                        $moneda = "S";
                        $tipo_cambio = 1;
                        $total = $total;
                        $valor_venta = $valor_venta;
                        $subtotal = $subtotal;
                        $igv = $igv;
                    } else {
                        $moneda = "D";
                        $tipo_cambio = preg_replace('/^[^0-9.]*/', '', $fila[23]);

                        if ($tipo_cambio === "") {
                            $tipo_c = $this->api_tipo_cambio_base_datos($fecha_emision);
                            //var_dump($tipo_c);
                            $tipo_cambio = $tipo_c->data->venta;
                        }

                        $total = round($total / $tipo_cambio, 2);
                        $valor_venta = round($valor_venta / $tipo_cambio, 2);
                        $subtotal = round($subtotal / $tipo_cambio, 2);
                        $igv = round($igv / $tipo_cambio, 2);
                    }

                    $comprobante = "FACTURA";
                    if ($fila[3] === 1) {
                        $documento = "F";
                    } else {
                        $documento = "NC";
                    }

                    $verificar = $this->verificarGlosaCuenta($cliente, $ruc, $fila[4]);

                    $dataFila = [
                        "fecha_emision" => $fecha_emision,
                        "serie_numero" => $serie_numero,
                        "ruc" => $ruc,
                        "total" => $total,
                        "moneda" => $moneda,
                        "tipo_cambio" => $tipo_cambio,
                        "documento" => $documento,
                        "comprobante" => $comprobante,
                        "igv" => $igv,
                        "valor_venta" => $valor_venta,
                        "subtotal" => $subtotal,
                        "icbper" => $fila[19],
                        "glosa" => $verificar['glosa'],
                        "cuenta" => $verificar['cuenta']
                    ];

                    array_push($datos, $dataFila);
                }
            }

            // Extraemos los valores de 'tipo_cambio' en un array separado
            $tipo_cambio_values = array_column($datos, 'tipo_cambio');

            // Creamos un array de prioridades basado en los valores de 'tipo_cambio'
            $priority = array_map(function ($value) {
                return $value == 1 ? -1 : 1;
            }, $tipo_cambio_values);

            // Ordenamos el array principal basado en las prioridades
            array_multisort($priority, $datos);

            $seen = []; // Arreglo para rastrear combinaciones ya vistas

            foreach ($datos as $index => &$item) {
                $key = $item['serie_numero'] . '-' . $item['ruc'];

                if (isset($seen[$key])) {
                    // Si la combinación ya fue vista, marca como repetido
                    $item['repetido'] = true;
                    $datos[$seen[$key]]['repetido'] = true;
                } else {
                    $seen[$key] = $index;
                    $item['repetido'] = false;
                }
            }

            // Opcional: Borrar el archivo una vez leído
            unlink(ROOTPATH . 'uploads/' . $newName);

            return $this->response->setContentType('application/json')->setJSON($datos);
        } else {
            return $this->response->setContentType('application/json')->setJSON(ROOTPATH);
        }
    }

    public function verificarGlosaCuenta($contribuyente, $rucProveedor, $serie)
    {
        $compras = model('MaquetaComprasModel');
        $plan = model('PlanCuentasModel');

        $consulta = $compras->query("SELECT * FROM maqueta_compras WHERE cliente = '$contribuyente' AND ruc = '$rucProveedor' AND fecha >= '2023-06-01' and glosa != '' ORDER BY id_maqueta DESC LIMIT 1")->getRow();

        $existe = 0;

        if ($consulta) {
            $existe = 1;
            $cuenta = $consulta->cuenta;
            $glosa = "";

            if ($cuenta !== "") {
                if ($contribuyente === '20445761550') {
                    $antiguedad = 1;
                } else {
                    $antiguedad = 2;
                }

                $queryGlosa = $plan->where('antiguedad', $antiguedad)->where('cuenta', $cuenta)->first();

                if ($queryGlosa) {
                    $glosa = $queryGlosa['glosa'];
                }
            }
        } else {
            $cuenta = "";
            $glosa = "";
        }

        $data = [
            "glosa" => $glosa,
            "cuenta" => $cuenta,
            "existe" => $existe
        ];

        return $data;
    }

    public function verificar_sunat()
    {
        $comprobante = $this->request->getVar('comprobante');
        $emisor = $this->request->getVar('emisor');
        $fecha_emision = $this->request->getVar('fecha_emision');
        $monto = $this->request->getVar('monto');
        $numero = $this->request->getVar('numero');
        $serie = $this->request->getVar('serie');

        $data = array(
            "tipo_comprobante" => $comprobante,
            "serie" => $serie,
            "correlativo" => $numero,
            "fecha_emision" => $fecha_emision,
            "monto" => $monto,
            "emisor" => $emisor
        );

        $ruta = "https://esconsultoresyasesores.com:9092/api/consulta-comprobante";

        $token = ''; //en caso quieras utilizar algún token generado desde tu sistema

        //codificamos la data
        $data_json = json_encode($data);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $ruta);
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array(
                'Authorization: Token token="' . $token . '"',
                'Content-Type: application/json'
            )
        );
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $respuesta  = curl_exec($ch);
        curl_close($ch);
        /*echo $respuesta;
		exit();*/
        $response = json_decode($respuesta, true);

        echo json_encode($response);
    }

    public function load_vaucher()
    {
        if ($this->request->getFile('archivo')->isValid() && !$this->request->getFile('archivo')->hasMoved()) {

            $idcompra = $this->request->getVar('idcompra');
            $descripcion = $this->request->getVar('descripcion');

            $archivo = $this->request->getFile('archivo');
            $nombreArchivo = $archivo->getName();
            $archivo->move(ROOTPATH . 'uploads', $idcompra . "-" . $nombreArchivo);

            $model = model('ArchivosComprasModel');
            $maqueta = model('MaquetaComprasModel');

            $query = $model->where('idcompra', $idcompra)->first();

            /*if($query) {
                $data = array(
                    "filename" => $idcompra."-".$nombreArchivo,
                    "descripcion" => $descripcion
                );
    
                $model->update($query['id'] ,$data);
            } else {*/
            $data = array(
                "filename" => $idcompra . "-" . $nombreArchivo,
                "descripcion" => $descripcion,
                "estado" => 1,
                "idcompra" => $idcompra
            );

            $model->insert($data);

            $data_update = array(
                "estado_vaucher" => 1
            );

            $maqueta->update($idcompra, $data_update);
            //}

            return $this->response->setJSON(['status' => 'success', 'message' => 'Archivo subido exitosamente!']);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => $this->request->getFile('archivo')->getErrorString()]);
        }
    }

    public function consulta_bancarizados()
    {
        $ruc = $this->request->getVar('ruc');
        $periodo = $this->request->getVar('periodo') . "-01";

        $maqueta = model('MaquetaComprasModel');

        $maqueta->query("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");

        $bancarizados = $maqueta->query("SELECT numero_documento, razon_social, id_maqueta, total, estado_vaucher FROM maqueta_compras WHERE cliente = $ruc AND periodo = '$periodo' AND ( (tipo_moneda = 'D' AND total >= 500 * tipo_cambio) OR (tipo_moneda = 'S' AND total >= 2000)) GROUP BY numero_documento, razon_social")->getResult();

        $data = array();

        foreach ($bancarizados as $key => $value) {

            $archivos = model('ArchivosComprasModel');

            $archi = array();

            $consulta = $archivos->where('idcompra', $value->id_maqueta)->findAll();

            if ($consulta) {

                foreach ($consulta as $keys => $values) {
                    $link = "/uploads/" . $values['filename'];
                    $descripcion = $values['descripcion'];

                    $files = [
                        "link" => $link,
                        "descripcion" => $descripcion
                    ];

                    array_push($archi, $files);
                }
            }

            $new_data = array(
                "id_maqueta" => $value->id_maqueta,
                "serie_numero" => $value->numero_documento,
                "razon_social" => $value->razon_social,
                "total" => $value->total,
                "estado" => $value->estado_vaucher,
                "files" => $archi
            );

            array_push($data, $new_data);
        }

        echo json_encode($data);
    }
}
