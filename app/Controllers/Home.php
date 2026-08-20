<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        if (!session()->is_logged) {
			return redirect()->to("/");
		}

        return view('inicio/index');
    }

    public function pdts()
    {
        return view('inicio/pdts');
    }
}
