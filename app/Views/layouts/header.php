<header id="page-topbar">
                <div class="navbar-header">
                    <div class="d-flex">
                        <!-- LOGO -->
                        <div class="navbar-brand-box">
                            <a href="./home" class="logo logo-dark">
                                <span class="logo-sm">
                                    <img src="public/assets/images/icon.png" alt="" height="24">
                                </span>
                                <span class="logo-lg">
                                    <img src="public/assets/images/icon.png" alt="" height="24"> <span class="logo-txt">GRUPO ES</span>
                                </span>
                            </a>

                            <a href="./home" class="logo logo-light">
                                <span class="logo-sm">
                                    <img src="public/assets/images/icon.png" alt="" height="24">
                                </span>
                                <span class="logo-lg">
                                    <img src="public/assets/images/icon.png" alt="" height="24"> <span class="logo-txt">GRUPO ES</span>
                                </span>
                            </a>
                        </div>

                        <button type="button" class="btn btn-sm px-3 font-size-16 header-item" id="vertical-menu-btn">
                            <i class="fa fa-fw fa-bars"></i>
                        </button>

                        
                    </div>

                    <div class="d-flex">
                        <div style="justify-content: center;">
                            <h3><?= session()->contribuyente ?></h3>
                        </div>
                    </div>

                    <div class="d-flex">

                        <div class="dropdown d-inline-block">
                            <button type="button" class="btn header-item bg-soft-light border-start border-end" id="page-header-user-dropdown"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <img class="rounded-circle header-profile-user" src="public/assets/images/icon.png"
                                    alt="Header Avatar">
                                <span class="d-none d-xl-inline-block ms-1 fw-medium"><?= session()->ruc ?></span>
                                <i class="mdi mdi-chevron-down d-none d-xl-inline-block"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end">
                                <!-- item-->
                                <a class="dropdown-item" href="./perfil"><i class="mdi mdi-face-profile font-size-16 align-middle me-1"></i> Perfil</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="./close"><i class="mdi mdi-logout font-size-16 align-middle me-1"></i> Salir</a>
                            </div>
                        </div>

                    </div>
                </div>
            </header>