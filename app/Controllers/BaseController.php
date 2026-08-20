<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
class BaseController extends Controller
{
	/**
	 * Instance of the main Request object.
	 *
	 * @var CLIRequest|IncomingRequest
	 */
	protected $request;

	/**
	 * An array of helpers to be loaded automatically upon
	 * class instantiation. These helpers will be available
	 * to all other controllers that extend BaseController.
	 *
	 * @var array
	 */
	protected $helpers = [];

	/**
	 * Constructor.
	 */
	public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
	{
		// Do Not Edit This Line
		parent::initController($request, $response, $logger);

		// Preload any models, libraries, etc, here.

		// E.g.: $this->session = \Config\Services::session();
	}

	public function api_dni_ruc($tipo, $num_doc, $tipo_busqueda = 'completa')
	{
		$token_user = 'facturalaya_erickpeso_05jFE7sAOudi8j0';
		$bloquear_busquedas = false;
		if ($bloquear_busquedas) {
			$resp['respuesta'] = 'error';
			$resp['titulo'] = 'Error';
			$resp['mensaje'] = 'Tenemos Problemas en los Servidores de SUNAT y RENIEC, ingresa los datos manualmente por favor...';
			return $resp;
		}

		$password = $token_user; //AQUÍ TU PASSWORD 
		if ($tipo == 'dni') {
			$ruta = "https://facturalahoy.com/api/persona/" . $num_doc . '/' . $password . '/' . $tipo_busqueda;
		} elseif ($tipo == 'ruc') {
			$ruta = "https://facturalahoy.com/api/empresa/" . $num_doc . '/' . $password . '/' . $tipo_busqueda;
		} else {
			$resp['respuesta'] = 'error';
			$resp['titulo'] = 'Error';
			$resp['mensaje'] = 'Tipo de Documento Desconocido';
			return $resp;
		}

		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_URL => $ruta,
			CURLOPT_USERAGENT => 'Consulta Datos',
			CURLOPT_CONNECTTIMEOUT => 0,
			CURLOPT_TIMEOUT => 400,
			CURLOPT_FAILONERROR => true
		));

		$data = curl_exec($curl);
		if (curl_error($curl)) {
			$error_msg = curl_error($curl);
		}

		curl_close($curl);

		if (isset($error_msg)) {
			$resp['respuesta'] = 'error';
			$resp['titulo'] = 'Error';
			$resp['data'] = $data;
			$resp['encontrado'] = false;
			$resp['mensaje'] = 'Error en Api de Búsqueda';
			$resp['errores_curl'] = $error_msg;
			return $resp;
		}

		$data_resp = json_decode($data);
		if (!isset($data_resp->respuesta) || $data_resp->respuesta == 'error') {
			$resp['respuesta'] = 'error';
			$resp['titulo'] = 'Error';
			$resp['encontrado'] = false;
			$resp['data_resp'] = $data_resp;
			return $resp;
		}

		$resp['respuesta'] = 'ok';
		$resp['encontrado'] = true;
		$resp['api'] = true;
		$resp['data'] = json_decode($data);

		return $resp;
	}

	public function generar_token()
	{
		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => "https://api-seguridad.sunat.gob.pe/v1/clientesextranet/d7930572-5d49-4d3f-9fc2-7f7ce8969fc4/oauth2/token/",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => "grant_type=client_credentials&scope=https%3A//api.sunat.gob.pe/v1/contribuyente/contribuyentes&client_id=d7930572-5d49-4d3f-9fc2-7f7ce8969fc4&client_secret=k7V/L/ppBr4ybTXK326fSg%3D%3D",
			CURLOPT_HTTPHEADER => array(
				"Content-Type: application/x-www-form-urlencoded"
			),
		));

		$response = curl_exec($curl);

		curl_close($curl);
		return $response;
	}

	public function consultar_sunat_comprobante($fecha, $monto, $emisor, $tipo_comprobante, $serie, $numero)
	{
		$fecha_parseada = date("d/m/Y", strtotime($fecha));
		$total = number_format($monto, 2, '.', '');

		$recibir_token = $this->generar_token();
		$recibir_token = json_decode($recibir_token, true);

		$token = $recibir_token['access_token'];

		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => "https://api.sunat.gob.pe/v1/contribuyente/contribuyentes/$emisor/validarcomprobante",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => "{\n\t\"numRuc\": \"$emisor\",\n\t\"codComp\": \"$tipo_comprobante\",\n\t\"numeroSerie\": \"$serie\",\n\t\"numero\": \"$numero\",\n\t\"fechaEmision\": \"$fecha_parseada\",\n\t\"monto\": \"$total\"\n}",
			CURLOPT_HTTPHEADER => array(
				"Content-Type: application/json",
				"Authorization: Bearer $token"
			),
		));

		$response = curl_exec($curl);

		curl_close($curl);

		$response = json_decode($response);

		return $response;
	}

	public function api_tipo_cambio($fecha)
	{
		$token = 'apis-token-1.aTSI1U7KEuT-6bbbCguH-4Y8TI6KS73N';

		// Iniciar llamada a API
		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => 'https://esconsultoresyasesores.com:9300/api/consulta-tipo-cambio/' . $fecha,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 2,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'GET'
		));

		$response = curl_exec($curl);

		curl_close($curl);
		// Datos listos para usar
		$tipoCambioSunat = json_decode($response);
		return $tipoCambioSunat;
	}

	public function apiLoadPdtFileFactura($rutaFile)
	{
		$curl = curl_init();

		$postData = json_encode(['filepath' => $rutaFile]);

		curl_setopt_array($curl, array(
			CURLOPT_URL => "http://157.230.239.170:4100/procesar-facturas",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_POSTFIELDS => $postData,
			CURLOPT_HTTPHEADER => array(
				'Content-Type: application/json',
				'Content-Length: ' . strlen($postData)
			),
		));

		$response = curl_exec($curl);

		curl_close($curl);
		return json_decode($response, true);
	}

	public function apiLoadPdtFileBoleta($rutaFile)
	{
		$curl = curl_init();

		$postData = json_encode(['filepath' => $rutaFile]);

		curl_setopt_array($curl, array(
			CURLOPT_URL => 'http://157.230.239.170:4100/procesar-boletas',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_POSTFIELDS => $postData,
			CURLOPT_HTTPHEADER => array(
				'Content-Type: application/json'
			),
		));

		$response = curl_exec($curl);

		curl_close($curl);

		return json_decode($response, true);
	}

	public function apiLoadPdtFileNotaCredito($rutaFile)
	{
		$curl = curl_init();

		$postData = json_encode(['filepath' => $rutaFile]);

		curl_setopt_array($curl, array(
			CURLOPT_URL => 'http://157.230.239.170:4100/procesar-notas-credito',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_POSTFIELDS => $postData,
			CURLOPT_HTTPHEADER => array(
				'Content-Type: application/json'
			),
		));

		$response = curl_exec($curl);

		curl_close($curl);

		return json_decode($response, true);
	}

	public function api_tipo_cambio_base_datos($fecha)
	{
		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => 'https://esconsultoresyasesores.com:9300/api/consulta-tipo-cambio/' . $fecha,
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
		$tipoCambioSunat = json_decode($response);
		return $tipoCambioSunat;
	}
}
