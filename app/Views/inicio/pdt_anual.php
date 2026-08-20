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
                    <h4 class="mb-sm-0 font-size-18">PDT ANUAL</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Pdt Anual</a></li>
                            <li class="breadcrumb-item active">consultar</li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>
        <!-- end page title -->
        <form method="POST" id="form_pdt_anual">
            <div class="row">

                <div class="col-md-3">
                    <div class="mb-3">
                        <label class="form-label">Año Inicio</label>
                        <select class="form-select" name="anio_inicio" id="anio_inicio" required>
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
                        <label class="form-label">Año fin</label>
                        <select class="form-select" name="anio_fin" id="anio_fin" required>
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
                        <th>Año</th>
                        <th>Archivo</th>
                    </tr>
                </thead>

                <tbody id="result_data">

                </tbody>
            </table>

        </div>

    </div>
    <!-- container-fluid -->
</div>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script src="public/assets/libs/sweetalert2/sweetalert2.min.js"></script>
<script src="public/js/pdt_anual.js"></script>
<?= $this->endSection() ?>