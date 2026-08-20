<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-content">
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18">Perfil</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Perfil</a></li>
                            <li class="breadcrumb-item active">form</li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="d-flex align-items-start mt-3 mt-sm-0">
                                    <div class="flex-shrink-0">
                                        <div class="avatar-xl me-3">
                                            <img src="public/assets/images/icon.png" alt=""
                                                class="img-fluid rounded-circle d-block">
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div>
                                            <h5 class="font-size-16 mb-1"><?= session()->contribuyente ?></h5>
                                            <p class="text-muted font-size-13"><?= session()->ruc ?></p>

                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-8">
                                <h5>Cambiar la contraseña</h5>

                                <form method="POST" id="form_cambiar">

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label" for="actual">Escribir su contraseña actual</label>
                                                <input type="password" class="form-control" id="actual" name="actual" placeholder="Ingrese su contraseña actual" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label" for="nueva">Esribir nueva contraseña</label>
                                                <input type="password" class="form-control" id="nueva" name="nueva" placeholder="Mínimo 8 caracteres" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label" for="renueva">Confirmar la contraseña nueva</label>
                                                <input type="password" class="form-control" id="renueva" name="renueva" placeholder="Mínimo 8 caracteres" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <button type="submit" class="btn btn-primary" id="btnCambiar">Cambiar Contraseña</button>
                                            </div>
                                        </div>
                                    </div>

                                </form>
                            </div>

                        </div>


                    </div>
                    <!-- end card body -->
                </div>
                <!-- end card -->


            </div>
            <!-- end col -->
        </div>

    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script src="public/js/perfil.js"></script>
<?= $this->endSection('js') ?>