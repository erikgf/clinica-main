<?php

class Template{
    private $title;
    private $content;
    private $navbar;

    public $ID_ROL_ADMINISTRADOR = "1";
    public $ID_ROL_RECEPCION = "2";
    public $ID_ROL_FACTURACION = "3";
    public $ID_ROL_LOGISTICA = "4";
    
    public $ID_VISUALIZADOR = "6";
    public $ID_CONTABILIDAD = "7";
    public $ID_ROL_ASISTENTE_REVISION = "8";
    public $ID_ROL_LABORATORIO = "9";
    public $ID_ROL_RECEPCION_SUPERVISOR = "10";
    public $ID_ROL_COORDINADOR_LABORATORIO = "11";
    public $ID_ROL_ASISTENTE_ADMINISTRADOR = "12";
    public $ID_ROL_RECEPCION_DESCUENTOS = "13";

    public function loadContent($ruta){
        $this->content = file_get_contents($ruta, true);
    }

    public function renderNavbarItems(){
        echo '<nav class="main-header navbar navbar-expand navbar-white navbar-light">

        <ul class="navbar-nav">
        <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
        <a href="index3.html" class="nav-link">Home</a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
        <a href="#" class="nav-link">Contact</a>
        </li>
        </ul>
        
        <ul class="navbar-nav ml-auto">
        
        <li class="nav-item">
        <a class="nav-link" data-widget="navbar-search" href="#" role="button">
        <i class="fas fa-search"></i>
        </a>
        <div class="navbar-search-block">
        <form class="form-inline">
        <div class="input-group input-group-sm">
        <input class="form-control form-control-navbar" type="search" placeholder="Search" aria-label="Search">
        <div class="input-group-append">
        <button class="btn btn-navbar" type="submit">
        <i class="fas fa-search"></i>
        </button>
        <button class="btn btn-navbar" type="button" data-widget="navbar-search">
        <i class="fas fa-times"></i>
        </button>
        </div>
        </div>
        </form>
        </div>
        </li>
        
        <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
        <i class="far fa-comments"></i>
        <span class="badge badge-danger navbar-badge">3</span>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
        <a href="#" class="dropdown-item">
        
        <div class="media">
        <img src="dist/img/user1-128x128.jpg" alt="User Avatar" class="img-size-50 mr-3 img-circle">
        <div class="media-body">
        <h3 class="dropdown-item-title">
        Brad Diesel
        <span class="float-right text-sm text-danger"><i class="fas fa-star"></i></span>
        </h3>
        <p class="text-sm">Call me whenever you can...</p>
        <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 Hours Ago</p>
        </div>
        </div>
        
        </a>
        <div class="dropdown-divider"></div>
        <a href="#" class="dropdown-item">
        
        <div class="media">
        <img src="dist/img/user8-128x128.jpg" alt="User Avatar" class="img-size-50 img-circle mr-3">
        <div class="media-body">
        <h3 class="dropdown-item-title">
        John Pierce
        <span class="float-right text-sm text-muted"><i class="fas fa-star"></i></span>
        </h3>
        <p class="text-sm">I got your message bro</p>
        <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 Hours Ago</p>
        </div>
        </div>
        
        </a>
        <div class="dropdown-divider"></div>
        <a href="#" class="dropdown-item">
        
        <div class="media">
        <img src="dist/img/user3-128x128.jpg" alt="User Avatar" class="img-size-50 img-circle mr-3">
        <div class="media-body">
        <h3 class="dropdown-item-title">
        Nora Silvester
        <span class="float-right text-sm text-warning"><i class="fas fa-star"></i></span>
        </h3>
        <p class="text-sm">The subject goes here</p>
        <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 Hours Ago</p>
        </div>
        </div>
        
        </a>
        <div class="dropdown-divider"></div>
        <a href="#" class="dropdown-item dropdown-footer">See All Messages</a>
        </div>
        </li>
        
        <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
        <i class="far fa-bell"></i>
        <span class="badge badge-warning navbar-badge">15</span>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
        <span class="dropdown-item dropdown-header">15 Notifications</span>
        <div class="dropdown-divider"></div>
        <a href="#" class="dropdown-item">
        <i class="fas fa-envelope mr-2"></i> 4 new messages
        <span class="float-right text-muted text-sm">3 mins</span>
        </a>
        <div class="dropdown-divider"></div>
        <a href="#" class="dropdown-item">
        <i class="fas fa-users mr-2"></i> 8 friend requests
        <span class="float-right text-muted text-sm">12 hours</span>
        </a>
        <div class="dropdown-divider"></div>
        <a href="#" class="dropdown-item">
        <i class="fas fa-file mr-2"></i> 3 new reports
        <span class="float-right text-muted text-sm">2 days</span>
        </a>
        <div class="dropdown-divider"></div>
        <a href="#" class="dropdown-item dropdown-footer">See All Notifications</a>
        </div>
        </li>
        <li class="nav-item">
        <a class="nav-link" data-widget="fullscreen" href="#" role="button">
        <i class="fas fa-expand-arrows-alt"></i>
        </a>
        </li>
        <li class="nav-item">
        <a class="nav-link" data-widget="control-sidebar" data-controlsidebar-slide="true" href="#" role="button">
        <i class="fas fa-th-large"></i>
        </a>
        </li>
        </ul>
        </nav>';
    }

    public function renderContent(){
        echo $this->content;
    }

    public function setTitle($title){
        $this->title = $title;
    }

    public function renderTitle(){
        echo $this->title;
    }

    public function validarPermisoRoles($objUsuario, $arregloRoles){
        if ($objUsuario == null && count($arregloRoles)<=0 ){
            return false;
        }

        $id_rol = $objUsuario["id_rol"];
        if (in_array($id_rol, $arregloRoles)){
            return true;
        }

        return false;
    }

    public function renderMenu(){
        $id_rol = Sesion::obtenerSesion()["id_rol"];
        $interfaz_actual = basename($_SERVER['REQUEST_URI']);

        $actualDir = getcwd();
        chdir("../../");
        require_once '../negocio/Usuario.clase.php';
        $obj = new Usuario();
        $objMenu = $obj->getInterfaces($id_rol);
        chdir($actualDir);
        array_push($objMenu,  ["rotulo"=>"Cambiar Clave", "url"=>"cambiar-clave", "padre"=>"0"]);

        $html = '';
        foreach ($objMenu as $key => $value) {
            $activo = $interfaz_actual == $value["url"] ? "active" : ""; 
            if ($value["padre"] == "0"){
                $html .= '<li class="nav-item">
                            <a href="../'.$value["url"].'" class="nav-link '.$activo.'">
                                <i class="far fa-circle nav-icon"></i>
                                <p>'.$value["rotulo"].'</p>
                            </a>
                        </li>';
            }
        }


        $html .= '
            <li class="nav-item">
                <a href="'.RUTA_BASE.'/controlador/usuario.controlador.php?op=cerrar_sesion" class="nav-link bg-red color-white">
                    <i class="fa fa-lock nav-icon"></i>
                    <p>Cerrar Sesión</p>
                </a>
            </li>';

        echo $html;

    }

    public function renderMenuOld(){
        $id_rol = Sesion::obtenerSesion()["id_rol"];
        $interfaz_actual = basename($_SERVER['REQUEST_URI']);

        $objMenu = [];
        switch($id_rol){
            case $this->ID_ROL_ADMINISTRADOR:
                $objMenu = [
                    ["rotulo"=>"Gestión Caja", "url"=>"gestion-caja", "padre"=>"0"],
                    ["rotulo"=>"Registro de Atenciones", "url"=>"registro-atencion", "padre"=>"0"],
                    ["rotulo"=>"Ver Atenciones", "url"=>"gestion-atenciones-admin", "padre"=>"0"],
                    ["rotulo"=>"Ver Atenciones Saldos", "url"=>"gestion-atenciones-saldo", "padre"=>"0"],
                    ["rotulo"=>"Ver Exámenes", "url"=>"ver-examenes", "padre"=>"0"],
                    ["rotulo"=>"Mantenimientos Servicios", "url"=>"mantenimientos-servicios", "padre"=>"0"],
                    ["rotulo"=>"Revisión Exámenes", "url"=>"gestion-servicios-revision", "padre"=>"0"],
                    ["rotulo"=>"Gestión de Convenios", "url"=>"gestion-convenios", "padre"=>"0"],
                    ["rotulo"=>"Gestión Médicos - Promotoras", "url"=>"promotoras-medicos", "padre"=>"0"],
                    ["rotulo"=>"Reportes Promotoras - Médicos", "url"=>"reportes-promotoras-medicos", "padre"=>"0"],
                    ["rotulo"=>"Gestión Laboratorio", "url"=>"gestion-laboratorio", "padre"=>"0"],                    
                    ["rotulo"=>"Reportes Laboratorio", "url"=>"reportes-laboratorio", "padre"=>"0"],
                    ["rotulo"=>"Resultados de Laboratorio", "url"=>"ver-resultados-laboratorio", "padre"=>"0"],
                    ["rotulo"=>"Mantenimiento Usuarios", "url"=>"mantenimientos-usuarios", "padre"=>"0"],
                ];
            break;

            case $this->ID_ROL_RECEPCION:
            case $this->ID_ROL_RECEPCION_DESCUENTOS:
                $objMenu = [
                    ["rotulo"=>"Gestión Caja", "url"=>"gestion-caja", "padre"=>"0"],
                    ["rotulo"=>"Registro de Atenciones", "url"=>"registro-atencion", "padre"=>"0"],
                    ["rotulo"=>"Ver Atenciones", "url"=>"gestion-atenciones", "padre"=>"0"],
                    ["rotulo"=>"Resultados de Laboratorio", "url"=>"ver-resultados-laboratorio", "padre"=>"0"]
                ];
            break;

            case $this->ID_ROL_RECEPCION_SUPERVISOR:
                $objMenu = [
                    ["rotulo"=>"Gestión Caja", "url"=>"gestion-caja", "padre"=>"0"],
                    ["rotulo"=>"Registro de Atenciones", "url"=>"registro-atencion", "padre"=>"0"],
                    ["rotulo"=>"Ver Atenciones", "url"=>"gestion-atenciones", "padre"=>"0"],
                    ["rotulo"=>"Resultados de Laboratorio", "url"=>"ver-resultados-laboratorio", "padre"=>"0"],
                    ["rotulo"=>"Gestión Servicios", "url"=>"gestion-servicios", "padre"=>"0"]
                ];
            break;

            case $this->ID_ROL_LOGISTICA:
                $objMenu = [
                    ["rotulo"=>"Gestión Caja", "url"=>"gestion-caja", "padre"=>"0"],
                    ["rotulo"=>"Registro de Atenciones", "url"=>"registro-atencion", "padre"=>"0"],
                    ["rotulo"=>"Ver Atenciones", "url"=>"gestion-atenciones-admin", "padre"=>"0"],
                    ["rotulo"=>"Ver Exámenes", "url"=>"ver-examenes", "padre"=>"0"],
                    ["rotulo"=>"Revisión Exámenes", "url"=>"gestion-servicios-revision", "padre"=>"0"],
                    ["rotulo"=>"Gestión Médicos - Promotoras", "url"=>"promotoras-medicos", "padre"=>"0"],
                    ["rotulo"=>"Reportes Promotoras - Médicos", "url"=>"reportes-promotoras-medicos", "padre"=>"0"],
                    ["rotulo"=>"Gestión Laboratorio", "url"=>"gestion-laboratorio", "padre"=>"0"],
                    ["rotulo"=>"Reportes Laboratorio", "url"=>"reportes-laboratorio", "padre"=>"0"]
                ];
            break;

            case $this->ID_VISUALIZADOR:
                $objMenu = [
                    ["rotulo"=>"Monitoreo Caja", "url"=>"gestion-caja-vis", "padre"=>"0"],
                    ["rotulo"=>"Ver Atenciones", "url"=>"gestion-atenciones", "padre"=>"0"],
                    ["rotulo"=>"Ver Atenciones Saldos", "url"=>"gestion-atenciones-saldo", "padre"=>"0"],
                ];
            break;

            case $this->ID_CONTABILIDAD:
                $objMenu = [
                    ["rotulo"=>"Generar Archivo Ventas", "url"=>"generar-exportar-ventas", "padre"=>"0"],
                ];
            break;

            case $this->ID_ROL_ASISTENTE_REVISION:
                $objMenu = [
                    ["rotulo"=>"Revisión Exámenes", "url"=>"gestion-servicios-revision", "padre"=>"0"],
                ];
            break; 

            case $this->ID_ROL_LABORATORIO:
                $objMenu = [
                    ["rotulo"=>"Gestión Laboratorio", "url"=>"gestion-laboratorio", "padre"=>"0"],
                ];
            break;  

            case $this->ID_ROL_COORDINADOR_LABORATORIO;
                $objMenu = [
                    ["rotulo"=>"Mantenimientos", "url"=>"mantenimientos-laboratorio", "padre"=>"0"],
                    ["rotulo"=>"Gestión Laboratorio", "url"=>"gestion-laboratorio", "padre"=>"0"]
                    //["rotulo"=>"Reportes Laboratorio", "url"=>"reportes-laboratorio", "padre"=>"0"]
                ];
            break;  

            case $this->ID_ROL_ASISTENTE_ADMINISTRADOR:
                $objMenu = [                
                    ["rotulo"=>"Revisión Exámenes", "url"=>"gestion-servicios-revision", "padre"=>"0"],
                    ["rotulo"=>"Gestión Médicos - Promotoras", "url"=>"promotoras-medicos", "padre"=>"0"],
                    ["rotulo"=>"Ver Atenciones", "url"=>"gestion-atenciones-admin", "padre"=>"0"],
                    ["rotulo"=>"Registro de Atenciones", "url"=>"registro-atencion", "padre"=>"0"],
                    ["rotulo"=>"Ver Atenciones Saldos", "url"=>"gestion-atenciones-saldo", "padre"=>"0"],
                ];
            break; 

            case $this->ID_ROL_FACTURACION:

            break;
            
            default:
            break;
        }

        array_push($objMenu,  ["rotulo"=>"Cambiar Clave", "url"=>"cambiar-clave", "padre"=>"0"]);

        $html = '';
        foreach ($objMenu as $key => $value) {
            $activo = $interfaz_actual == $value["url"] ? "active" : ""; 
            if ($value["padre"] == "0"){
                $html .= '<li class="nav-item">
                            <a href="../'.$value["url"].'" class="nav-link '.$activo.'">
                                <i class="far fa-circle nav-icon"></i>
                                <p>'.$value["rotulo"].'</p>
                            </a>
                        </li>';
            }
        }


        $html .= '
            <li class="nav-item">
                <a href="'.RUTA_BASE.'/controlador/usuario.controlador.php?op=cerrar_sesion" class="nav-link bg-red color-white">
                    <i class="fa fa-lock nav-icon"></i>
                    <p>Cerrar Sesión</p>
                </a>
            </li>';

        echo $html;

    }

    public function mostrarAccesoNoValido(){
        echo '<script> alert("No tiene permiso para ver esto"); history.back() </script>';
    }

}