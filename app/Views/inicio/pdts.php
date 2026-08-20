<?= $this->extend('layouts/main') ?>

<?= $this->section('css') ?>
<link href="public/assets/libs/sweetalert2/sweetalert2.min.css" rel="stylesheet" type="text/css" />
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="page-content">
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18">PDT MENSUAL</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Pdt Mensual</a></li>
                            <li class="breadcrumb-item active">consultar</li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>
        <!-- end page title -->
        <form method="POST" id="form_pdt">
            <div class="row">

                <div class="col-md-3">
                    <div class="mb-3">
                        <label class="form-label">Año</label>
                        <select class="form-select" name="anio" id="anio" required>
                            <option value="">Select</option>
                            <option value="6">2022</option>
                            <option value="5">2021</option>
                            <option value="4">2020</option>
                            <option value="3">2019</option>
                            <option value="2">2018</option>
                            <option value="1">2017</option>
                            <option value="8">2016</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label class="form-label">Inicio Periodo</label>
                        <select class="form-select" name="inicio_periodo" id="inicio_periodo" required>
                            <option value="">Select</option>
                            <option value="1">Enero</option>
                            <option value="2">Febrero</option>
                            <option value="3">Marzo</option>
                            <option value="4">Abril</option>
                            <option value="5">Mayo</option>
                            <option value="6">Junio</option>
                            <option value="7">Julio</option>
                            <option value="8">Agosto</option>
                            <option value="9">Setiembre</option>
                            <option value="10">Octubre</option>
                            <option value="11">Noviembre</option>
                            <option value="12">Diciembre</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="mb-3">
                        <label class="form-label">Fin Periodo</label>
                        <select class="form-select" name="fin_periodo" id="fin_periodo" required>
                            <option value="">Select</option>
                            <option value="1">Enero</option>
                            <option value="2">Febrero</option>
                            <option value="3">Marzo</option>
                            <option value="4">Abril</option>
                            <option value="5">Mayo</option>
                            <option value="6">Junio</option>
                            <option value="7">Julio</option>
                            <option value="8">Agosto</option>
                            <option value="9">Setiembre</option>
                            <option value="10">Octubre</option>
                            <option value="11">Noviembre</option>
                            <option value="12">Diciembre</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="mb-3">
                        <button type="submit" class="btn btn-primary mt-4" id="consultar">Consultar</button>
                    </div>
                </div>

            </div><!-- end row-->
        </form>

        <div class="row">

            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Periodo</th>
                        <th>Archivos</th>
                    </tr>
                </thead>

                <tbody id="result_data">

                </tbody>
            </table>

        </div>

    </div>
    <!-- container-fluid -->
</div>
<script>
    const form = document.getElementById("form_pdt");
    const consultar = document.getElementById("consultar");

    const result = document.getElementById('result_data');

    form.addEventListener('submit', (e) => {
        e.preventDefault();

        consultar.disabled = true;
        consultar.textContent = "Consultando...";

        const formData = new FormData(form);

        fetch("./traer_data", {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                consultar.disabled = false;
                consultar.textContent = "Consultar";

                if (data.respuesta == "error") {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: data.mensaje
                    });

                    return false;
                }

                let html = "";

                if (data.length > 0) {

                    data.forEach((pdt, index) => {
                        html += `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${pdt.mes_descripcion}</td>
                            <td>
                                <a href="https://grupoesconsultores.com/contabilidad/public/archivos/pdt/${pdt.nombre_pdt}" target="_blank" class="btn btn-soft-light btn-sm w-xs waves-effect btn-label waves-light detalle"><i class="bx bx-download label-icon"></i> Detalle</a>
                                <a href="https://grupoesconsultores.com/contabilidad/public/archivos/pdt/${pdt.nombre_constancia}" target="_blank" class="btn btn-soft-light btn-sm w-xs waves-effect btn-label waves-light constancia" ><i class="bx bx-download label-icon"></i> Constancia</a>
                            </td>
                        </tr>
                    `;
                    });

                    result.innerHTML = html;
                } else {
                    result.innerHTML = `
                    <tr>
                        <td colspan="3"><h3 class='text-center mt-4'>NO HAY INFORMACIÓN</h3></td>
                    </tr>
                `;
                }
            })
    })
</script>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script src="public/assets/libs/sweetalert2/sweetalert2.min.js"></script>
<?= $this->endSection() ?>