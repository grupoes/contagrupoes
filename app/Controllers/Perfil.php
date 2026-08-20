<?php

namespace App\Controllers;

class Perfil extends BaseController
{
    public function index()
    {
        if (!session()->is_logged) {
			return redirect()->to("/");
		}

        return view('perfil/index');
    }

    public function cambiar_contrasena()
    {
        $actual = $this->request->getVar('actual');
        $nueva = $this->request->getVar('nueva');
        $renueva = $this->request->getVar('renueva');

        $cliente = model('ContribuyenteModel');

        $ruc = session()->ruc;

        $consulta_contrasena_actual = $cliente->where('acceso',$actual)->where('ruc_empresa_numero',$ruc)->first();

        $contrasena_actual = $consulta_contrasena_actual['acceso'];

        if ($actual != $contrasena_actual) {
            $json = array(
                "respuesta" => "error",
                "mensaje" => "Tu contraseña actual no coincide"
            );

            echo json_encode($json);
            exit();
        }

        if (strlen($nueva) < 8) {
            $json = array(
                "respuesta" => "error",
                "mensaje" => "El número de caracteres es menor que 8"
            );

            echo json_encode($json);
            exit();
        }

        if ($nueva != $renueva) {
            $json = array(
                "respuesta" => "error",
                "mensaje" => "la contraseña nueva no coinciden con la confirmación de la contraseña"
            );

            echo json_encode($json);
            exit();
        }

        $data_update = array("acceso" => $nueva);

        $cliente->update($ruc,$data_update);

        $json = array(
            "respuesta" => "ok",
            "mensaje" => "Se cambio la contraseña correctamente"
        );

        echo json_encode($json);
        
    }

}
