<!doctype html>
<html lang="en">


<!-- Mirrored from themesbrand.com/minia/layouts/layouts-horizontal.html by HTTrack Website Copier/3.x [XR&CO'2014], Sun, 27 Jun 2021 19:34:22 GMT -->

<head>

    <meta charset="utf-8" />
    <title>GRUPO ES | MAQUETA DE COMPRAS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="" name="description" />
    <meta content="" name="author" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="<?= base_url() ?>/assets/images/icon.png">

    <!-- Sweet Alert-->
    <link href="<?= base_url() ?>/assets/libs/sweetalert2/sweetalert2.min.css" rel="stylesheet" type="text/css" />

    <!-- plugin css -->
    <link href="<?= base_url() ?>/assets/libs/admin-resources/jquery.vectormap/jquery-jvectormap-1.2.2.css" rel="stylesheet" type="text/css" />

    <!-- preloader css -->
    <link rel="stylesheet" href="<?= base_url() ?>/assets/css/preloader.min.css" type="text/css" />

    <!-- Bootstrap Css -->
    <link href="<?= base_url() ?>/assets/css/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="<?= base_url() ?>/assets/css/icons.min.css" rel="stylesheet" type="text/css" />

    <link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">

    <!-- App Css-->
    <link href="<?= base_url() ?>/assets/css/app.min.css" id="app-style" rel="stylesheet" type="text/css" />

    <style>
        .spinner-container {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }

        .spinner {
            width: 40px;
            height: 40px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #3498db;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        .spinner-text {
            color: #f3f3f3;
            font-size: 18px;
            font-weight: bold;
            margin-top: 20px;
            text-align: center;
        }

        .spinner-visible {
            display: none;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>

</head>

<body data-layout="horizontal">

    <div class="spinner-container spinner-visible" id="spinnerVisible">
        <div class="spinner"></div>
        <p class="spinner-text">
            Estamos consultando los datos, espere...
        </p>
    </div>

    <input type="hidden" id="url_base" value="<?= base_url() ?>">
    <input type="hidden" id="ruc_contribuyente" value="<?= $numero ?>">
    <input type="hidden" id="ruc_activo" value="<?= $ruc_activo ?>">
    <input type="hidden" id="name_contribuyente" value="<?= $contribuyente ?>">
    <!-- Begin page -->
    <div id="layout-wrapper">

        <header id="page-topbar">
            <div class="navbar-header">
                <div class="d-flex">
                    <!-- LOGO -->
                    <div class="navbar-brand-box">
                        <a href="#" class="logo logo-dark">
                            <span class="logo-sm">
                                <img src="<?= base_url() ?>/assets/images/icon.png" alt="" height="24">
                            </span>
                            <span class="logo-lg">
                                <img src="<?= base_url() ?>/assets/images/icon.png" alt="" height="24">
                            </span>
                        </a>

                        <a href="#" class="logo logo-light">
                            <span class="logo-sm">
                                <img src="<?= base_url() ?>/assets/images/icon.png" alt="" height="24">
                            </span>
                            <span class="logo-lg">
                                <img src="<?= base_url() ?>/assets/images/icon.png" alt="" height="24"> <span class="logo-txt">Minia</span>
                            </span>
                        </a>
                    </div>

                </div>

                <div class="d-flex w-100">
                    <div style="justify-content: center; width: 100%">
                        <h3>GRUPO ES CONSULTORES</h3>
                    </div>
                </div>


            </div>
        </header>

        <div class="topnav">
            <div class="container-fluid">
                <nav class="navbar navbar-light navbar-expand-lg topnav-menu">

                    <div class="collapse navbar-collapse" id="topnav-menu-content">
                        <ul class="navbar-nav">

                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle arrow-none" href="#" id="topnav-dashboard" role="button">
                                    <i data-feather="home"></i><span data-key="t-dashboards">Maqueta de Compras -
                                        <?= $contribuyente ?> -
                                        <?= $numero ?> <span class="text-success" style="font-weight: bold; font-size: 22px"><?= $mensaje ?></span></span>
                                </a>
                            </li>

                        </ul>
                    </div>
                </nav>
            </div>
        </div>

        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="main-content">

            <div class="page-content">
                <div class="row">
                    <div class="col-xl-12 col-md-12">
                        <!-- card -->
                        <div class="card card-h-100">
                            <!-- card body -->
                            <div class="card-body">

                                <div class="row justify-content-end mb-3">
                                    <div class="col-auto">
                                        <button type="button" class="btn btn-primary" id="abancarizados" style="background: purple;border-color: purple; color: #fff">Comprobantes a bancarizar</button>
                                    </div>

                                    <div class="col-auto">
                                        <div class="dropdown">
                                            <button class="btn btn-success dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                                Descargar <i class="mdi mdi-chevron-down"></i>
                                            </button>
                                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                <a class="dropdown-item" href="#" id="downloadMaqueta">Maqueta de Compras</a>
                                                <a class="dropdown-item" href="#" id="maquetaRegistro">Maqueta de Compras por Registro</a>
                                                <a class="dropdown-item" href="#" id="consultaBancarizados">Consulta de comprobantes bancarizados</a>
                                            </div>
                                        </div>
                                    </div>

                                    <?php if ($numero === '20445761550') { ?>

                                        <div class="col-auto">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="radioGroup" id="radio1" value="1" checked>
                                                <label class="form-check-label" for="radio1">Plan Antiguo</label>
                                            </div>
                                        </div>

                                    <?php } else { ?>
                                        <div class="col-auto">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="radioGroup" id="radio2" value="2" checked>
                                                <label class="form-check-label" for="radio2">Plan Actual</label>
                                            </div>
                                        </div>

                                    <?php } ?>
                                </div>


                                <form id="form_maqueta" method="post">
                                    <input type="hidden" name="ruc_contribuyente" value="<?= $numero ?>">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <input type="text" class="form-control" id="codigoqr" name="codigoqr" placeholder="Escanear el código qr" autocomplete="off">
                                        </div>

                                        <div class="col-md-2">
                                            <input type="month" class="form-control" id="periodo" name="periodo">
                                        </div>

                                        <div class="col-md-4">
                                            <button type="button" class="btn btn-success" id="add_item" title="Agregar Item"><i class="bx bx-list-plus"></i></button>
                                            <button type="submit" class="btn btn-primary" id="generar_maqueta" title="Descargar Maqueta"><i class="bx bx-download"></i></button>
                                            <button type="button" class="btn btn-info" id="modal_subir_pdf">PDF COMPRA</button>
                                            <button type="button" class="btn btn-warning" id="modal_maqueta_venta">PDF VENTA</button>
                                            <button type="button" class="btn btn-secondary" id="modal_rh">TXT RH</button>
                                            <button type="button" class="btn btn-success" id="modalSire">SIRE</button>
                                            <button type="button" class="btn btn-danger" id="cancelar" title="Cancelar"><i class="mdi mdi-close"></i></button>
                                        </div>
                                    </div>

                                    <!--<div class="row mt-3">
                                        <div class="col-md-3">
                                            <label for="">RUC del Proveedor</label>
                                            <input type="text" class="form-control" id="ruc_proveedor" placeholder="">
                                        </div>
                                        <div class="col-md-2">
                                            <label for="">Serie</label>
                                            <input type="text" class="form-control" id="serie_consultar" placeholder="">
                                        </div>
                                        <div class="col-md-2">
                                            <label for="">Número</label>
                                            <input type="text" class="form-control" id="numero_consultar" placeholder="">
                                        </div>
                                        <div class="col-md-3">
                                            <button type="button" class="btn btn-primary mt-4">Consultar</button>
                                        </div>
                                    </div>-->


                                    <input type="hidden" name="ruc" id="ruc" value="<?= $numero ?>">
                                    <input type="hidden" name="razon" id="razon" value="<?= $contribuyente ?>">
                                    <div class="row mt-4">
                                        <div class="accordion" id="accordionExample">
                                            <div class="accordion-item">
                                                <h2 class="accordion-header" id="headingOne">
                                                    <button class="accordion-button fw-medium" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                                        <strong>COMPRAS</strong>
                                                    </button>
                                                </h2>
                                                <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                                    <div class="accordion-body">
                                                        <div class="table-responsive" style="overflow-y: auto; max-height: 300px">
                                                            <table class="table table-sm" id="tableCompras">

                                                                <thead>
                                                                    <tr>
                                                                        <th onclick="ordenarPorVacios(0, this)" style="cursor:pointer">ITEM <span class="icono-orden">⭥</span></th>
                                                                        <th onclick="ordenarPorVacios(1, this)" style="cursor:pointer">FECHA <span class="icono-orden">⭥</span></th>
                                                                        <th onclick="ordenarPorVacios(2, this)" style="cursor:pointer">T_MON <span class="icono-orden">⭥</span></th>
                                                                        <th onclick="ordenarPorVacios(3, this)" style="cursor:pointer"">DOC <span class=" icono-orden">⭥</span></th>
                                                                        <th onclick=" ordenarPorVacios(4, this)" style="cursor:pointer"">#_DOCUMENTO <span class=" icono-orden">⭥</span></th>
                                                                        <th onclick=" ordenarPorVacios(5, this)" style="cursor:pointer">RUC <span class="icono-orden">⭥</span></th>
                                                                        <th onclick="ordenarPorVacios(6, this)" style="cursor:pointer">VVENTA <span class="icono-orden">⭥</span></th>
                                                                        <th onclick="ordenarPorVacios(7, this)" style="cursor:pointer">VALOR_V <span class="icono-orden">⭥</span></th>
                                                                        <th onclick="ordenarPorVacios(8, this)" style="cursor:pointer">IGV <span class="icono-orden">⭥</span></th>
                                                                        <th onclick="ordenarPorVacios(9, this)" style="cursor:pointer">BOLSA <span class="icono-orden">⭥</span></th>
                                                                        <th onclick="ordenarPorVacios(10, this)" style="cursor:pointer">ICB <span class="icono-orden">⭥</span></th>
                                                                        <th onclick="ordenarPorVacios(11, this)" style="cursor:pointer">TOTAL <span class="icono-orden">⭥</span></th>
                                                                        <th onclick="ordenarPorVacios(12, this)" style="cursor:pointer">T.CAM <span class="icono-orden">⭥</span></th>
                                                                        <th onclick="ordenarPorVacios(13, this)" style="cursor:pointer">GLOSA <span class="icono-orden">⭥</span></th>
                                                                        <th onclick="ordenarPorVacios(14, this)" style="cursor:pointer">CUENTA <span class="icono-orden">⭥</span></th>
                                                                        <th onclick="ordenarPorVacios(15, this)" style="cursor:pointer">AFECT. <span class="icono-orden">⭥</span></th>
                                                                        <th></th>
                                                                    </tr>
                                                                </thead>

                                                                <tbody id="data_maqueta"></tbody>

                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="accordion-item">
                                                <h2 class="accordion-header" id="headingTwo">
                                                    <button class="accordion-button fw-medium collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                                        <strong>VENTAS</strong>
                                                    </button>
                                                </h2>
                                                <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
                                                    <div class="accordion-body">
                                                        <div class="table-responsive">
                                                            <table class="table table-sm">

                                                                <thead>
                                                                    <tr>
                                                                        <th>ITEM</th>
                                                                        <th>FECHA</th>
                                                                        <th>T_MONEDA</th>
                                                                        <th>DOCUMENTO</th>
                                                                        <th>#_DOCUMENTO</th>
                                                                        <th>COND</th>
                                                                        <th>RUC</th>
                                                                        <th>VVENTA</th>
                                                                        <th>VALOR_V</th>
                                                                        <th>IGV</th>
                                                                        <th>BOLSA</th>
                                                                        <th>ICB</th>
                                                                        <th>TOTAL</th>
                                                                        <th>T.CAM</th>
                                                                        <th>GLOSA</th>
                                                                        <th>CUENTA</th>
                                                                        <th>AFECT.</th>
                                                                        <th>TIPO</th>
                                                                        <th>REFERENCIA</th>
                                                                        <th>FECHA_REFERENCIA</th>
                                                                    </tr>
                                                                </thead>

                                                                <tbody id="data_maqueta_ventas"></tbody>

                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div><!-- end accordion -->

                                    </div>

                                </form>

                            </div><!-- end card body -->
                        </div><!-- end card -->
                    </div><!-- end col -->

                </div>

                <!-- Modal -->
                <div class="modal fade" id="modal_pdf" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Subir Pdf</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form id="form_subir" enctype="multipart/form-data">
                                <input type="hidden" id="idcliente" name="idcliente" value="<?= $numero ?>">
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label for="comprobante" class="form-label">Comprobantes</label>
                                        <select name="comprobante" id="comprobante" class="form-control" required>
                                            <option value="">--seleccione--</option>
                                            <option value="1">Factura</option>
                                            <option value="2">Boleta</option>
                                            <option value="3">Nota de Crédito</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="archivo" class="form-label">Subir Archivo Pdf</label>
                                        <input type="file" name="archivo" id="archivo" class="form-control">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                    <button type="submit" class="btn btn-primary" id="procesar">Procesar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Modal -->
                <div class="modal fade" id="modal_pdf_venta" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Subir Pdf</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form id="form_subir_venta" enctype="multipart/form-data">
                                <input type="hidden" id="prove" name="idcliente" value="<?= $numero ?>">
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label for="archivo_factura" class="form-label">Pdf Factura</label>
                                        <input type="file" name="archivo_factura" id="archivo_factura" class="form-control">
                                    </div>
                                    <div class="mb-3">
                                        <label for="archivo_boleta" class="form-label">Pdf Boleta</label>
                                        <input type="file" name="archivo_boleta" id="archivo_boleta" class="form-control">
                                    </div>
                                    <div class="mb-3">
                                        <label for="archivo_nota_credito" class="form-label">Pdf Nota de Crédito</label>
                                        <input type="file" name="archivo_nota_credito" id="archivo_nota_credito" class="form-control">
                                    </div>

                                    <div class="mb-3">
                                        <label for="archivo_nota_debito" class="form-label">Pdf Nota de Débito</label>
                                        <input type="file" name="archivo_nota_debito" id="archivo_nota_debito" class="form-control">
                                    </div>

                                    <div class="mb-3">
                                        <label for="glosa_modal" class="form-label">Glosa</label>
                                        <input type="text" name="glosa_modal" id="glosa_modal" class="form-control" required="">
                                    </div>
                                    <div class="mb-3">
                                        <label for="cuenta_modal" class="form-label">Cuenta</label>
                                        <input type="number" name="cuenta_modal" id="cuenta_modal" class="form-control" required="">
                                    </div>
                                    <div class="mb-3">
                                        <label for="switch3" class="form-label" style="display: block;">Igv</label>
                                        <input type="checkbox" id="switch3" name="check_igv" switch="bool" />
                                        <label for="switch3" data-on-label="Si" data-off-label="No"></label>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                    <button type="submit" class="btn btn-primary" id="procesar_venta">Procesar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Modal -->
                <div class="modal fade" id="modalrh" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Subir txt</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form id="form_subir_rh" enctype="multipart/form-data">
                                <input type="hidden" id="clienteid" name="idcliente" value="<?= $numero ?>">
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label for="archivo_rh" class="form-label">Subir Archivo txt</label>
                                        <input type="file" name="archivo_rh" id="archivo_rh" class="form-control">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                    <button type="submit" class="btn btn-primary" id="procesarrh">Procesar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Modal Mensaje-->
                <div class="modal fade" id="modalMensaje" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Mensaje</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body" id="modalBody">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Documento</th>
                                            <th>Proveedor</th>
                                        </tr>
                                    </thead>
                                </table>
                                <tbody>

                                </tbody>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Mensaje Registro Maquetas por periodo-->
                <div class="modal fade" id="modalListaRegistro" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="titleMensaje"></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form id="generarMaquetaRegistro">
                                <div class="modal-body" id="bodyModal">

                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                    <button type="submit" class="btn btn-primary" id="traerMaqueta">Generar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Modal para subir excel de SIRE -->
                <div class="modal fade" id="modal_excel" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Subir Excel</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form id="form_subir_excel" enctype="multipart/form-data">
                                <input type="hidden" id="customerId" name="customerId" value="<?= $numero ?>">
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label for="archivoExcel" class="form-label">Subir Archivo Excel</label>
                                        <input type="file" name="archivoExcel" id="archivoExcel" class="form-control">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                    <button type="submit" class="btn btn-primary" id="procesarExcel">Procesar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Modal para consultar -->
                <div class="modal fade" id="modal_consulta" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Consultar </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form id="form_verificar">
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label for="archivoExcel" class="form-label">Comprobante</label>
                                        <select name="comprobante" id="comprobante" class="form-select">
                                            <option value="01">FACTURA ELECTRÓNICA</option>
                                        </select>
                                    </div>
                                    <div class="mb-3 row">
                                        <div class="col-md-6">
                                            <label for="archivoExcel" class="form-label">Serie</label>
                                            <input type="text" class="form-control" name="serie" id="serie">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="archivoExcel" class="form-label">Número</label>
                                            <input type="text" class="form-control" name="numero" id="numero">
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="archivoExcel" class="form-label">Fecha Emisión</label>
                                        <input type="date" class="form-control" name="fecha_emision" id="fecha_emision">
                                    </div>
                                    <div class="mb-3 row">
                                        <div class="col-md-6">
                                            <label for="archivoExcel" class="form-label">Monto</label>
                                            <input type="text" class="form-control" name="monto" id="monto">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="archivoExcel" class="form-label">Ruc Emisor</label>
                                            <input type="text" class="form-control" name="emisor" id="emisor">
                                        </div>
                                    </div>
                                    <div id="mensajeSunat">

                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                    <button type="submit" class="btn btn-primary" id="verificar">Verificar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Modal archivos bancarizados -->
                <div class="modal fade" id="modal_bancarizar" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-xl">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Comprobantes a bancarizar </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Serie y Número</th>
                                            <th>Razón Social</th>
                                            <th>Total</th>
                                            <th>Voucher</th>
                                            <th>Descripción</th>
                                            <th>Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody id="listaBancarizar"></tbody>
                                </table>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal consulta comprobantes bancarizados -->
                <div class="modal fade" id="modal_consulta_bancarizar" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-xl">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Comprobantes a bancarizar </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Proveedor</th>
                                            <th>Total</th>
                                            <th>Voucher</th>
                                            <th>Descripción</th>
                                            <th>Ver</th>
                                            <th>Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody id="listaBancarizarConsulta"></tbody>
                                </table>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <!-- End Page-content -->

        </div>
        <!-- end main content-->

    </div>
    <!-- END layout-wrapper -->

    <!-- Right bar overlay-->
    <div class="rightbar-overlay"></div>

    <!-- JAVASCRIPT -->
    <script src="<?= base_url() ?>/assets/libs/jquery/jquery.min.js"></script>
    <script src="<?= base_url() ?>/assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="<?= base_url() ?>/assets/libs/metismenu/metisMenu.min.js"></script>
    <script src="<?= base_url() ?>/assets/libs/simplebar/simplebar.min.js"></script>
    <script src="<?= base_url() ?>/assets/libs/node-waves/waves.min.js"></script>
    <script src="<?= base_url() ?>/assets/libs/feather-icons/feather.min.js"></script>
    <!-- pace js -->
    <script src="<?= base_url() ?>/assets/libs/pace-js/pace.min.js"></script>

    <!-- Sweet Alerts js -->
    <script src="<?= base_url() ?>/assets/libs/sweetalert2/sweetalert2.min.js"></script>

    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>

    <!-- dashboard init -->
    <script src="<?= base_url() ?>/js/maqueta_compras.js?t=<?php echo time(); ?>"></script>

</body>

<!-- Mirrored from themesbrand.com/minia/layouts/layouts-horizontal.html by HTTrack Website Copier/3.x [XR&CO'2014], Sun, 27 Jun 2021 19:34:22 GMT -->

</html>