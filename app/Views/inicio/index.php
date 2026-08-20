<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-content">
                    <div class="container-fluid">

                        <!-- start page title -->
                        <div class="row">
                            <div class="col-12">
                                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                                    <h4 class="mb-sm-0 font-size-18">Dashboard</h4>

                                    <div class="page-title-right">
                                        <ol class="breadcrumb m-0">
                                            <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                                            <li class="breadcrumb-item active">Dashboard</li>
                                        </ol>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <!-- end page title -->

                        <div class="row">
                            <div class="col-xl-3 col-md-6">
                                <!-- card -->
                                <a href="pdts">
                                    <div class="card card-h-100">
                                        <!-- card body -->
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col-12">
                                                    
                                                    <h4 class="mb-3 text-center">
                                                        <span>PDT MENSUAL</span>
                                                    </h4>
                                                </div>
            
                                            </div>
                                            <!--<div class="text-nowrap">
                                                <span class="badge bg-soft-success text-success">+$20.9k</span>
                                                <span class="ms-1 text-muted font-size-13">Since last week</span>
                                            </div>-->
                                        </div><!-- end card body -->
                                    </div><!-- end card -->
                                </a>
                                
                            </div><!-- end col -->
        
                            <div class="col-xl-3 col-md-6">
                                <!-- card -->
                                <div class="card card-h-100">
                                    <!-- card body -->
                                    <a href="pdt-anual">
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col-12">
                                                    <h4 class="mb-3 text-center">
                                                        <span>PDT ANUAL</span>
                                                    </h4>
                                                </div>
                                            </div>

                                        </div>
                                    </a>
                                </div><!-- end card -->
                            </div><!-- end col-->
        
                            

                        </div><!-- end row-->

                    </div>
                    <!-- container-fluid -->
                </div>
<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script src="public/js/inicio.js"></script>
<?= $this->endSection() ?>