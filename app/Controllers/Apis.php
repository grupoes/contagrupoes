<?php

namespace App\Controllers;

class Apis extends BaseController
{
    public function index()
    {
        if (!session()->is_logged) {
			return redirect()->to("/");
		}

        return view('inicio/index');
    }

    public function tipo_cambio($fecha)
    {
        $tipo_cambio = $this->api_tipo_cambio($fecha);

        echo $tipo_cambio->venta;
    }
}
