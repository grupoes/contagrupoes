<?php

namespace App\Controllers;

class Login extends BaseController
{
    public function index()
    {
        if (!session()->is_logged) {
			return view('login/index');
		} else {
            return view('inicio/index');
        }
        
    }

    public function acceder()
    {
        $user = $this->request->getvar("username");
        $password = $this->request->getvar("password");

        $cliente = model('ContribuyenteModel');

        $consulta = $cliente->where('ruc_empresa_numero',$user)->where('acceso',$password)->where("ruc_empresa_estado",1)->first();

        if ($consulta) {

            session()->set([
                'ruc' => $consulta['ruc_empresa_numero'],
                'contribuyente' => $consulta['ruc_empresa_razon_social'],
                'is_logged' => true
            ]);

            $json = array(
                "respuesta" => "ok",
                "mensaje" => "Ingresando al sistema"
            );

            echo json_encode($json);
            
        } else {
            $json = array(
                "respuesta" => "error",
                "mensaje" => "Credenciales inválidas"
            );

            echo json_encode($json);
        }
        
    }

    public function cerrar()
    {
        session()->destroy();
        return redirect()->to("/");
    }

}
