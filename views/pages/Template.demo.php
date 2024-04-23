<?php

include_once '../../../negocio/Globals.clase.php';

class Template{
    private $title;
    private $content;
    private $navbar;
    
    private $usuario = null;
    private $arregloInterfacesDisponibles = [];
    private $interfazActual = "";
    public static $alertas = [];

    function __construct(){
        $this->usuario = Sesion::obtenerSesion();

        if ($this->usuario == null){
            $this->mostrarSesionNoActiva();
        }

        $this->interfazActual = basename($_SERVER['REQUEST_URI']);
        //$id_rol = isset( $this->usuario) ?  $this->usuario["id_rol"] : "";
        $actualDir = getcwd();
        chdir("../../");
        require_once '../negocio/Usuario.clase.php';
        $obj = new Usuario();
        $data = $obj->getInterfaces($this->usuario["id_usuario_registrado"]);
        $this->arregloInterfacesDisponibles = $data["interfaces"];
        Template::$alertas = $data["alertas"];
        $this->usuario["id_rol"] = $data["id_rol"];
        chdir($actualDir);

        $this->validarPermisos();
    }

    public function loadContent($ruta){
        $this->content = file_get_contents($ruta, true);
    }

    public function getAlertas(){
        return $this->alertas;
    }

    public function renderNavbarItems(){
        include '../navbar.php';
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

    public function validarPermisos(){   
       if (count($this->arregloInterfacesDisponibles)<=0 ){
            $this->mostrarAccesoNoValido();
            exit;
        }

        $interfazEncontrada = array_search($this->interfazActual, array_column($this->arregloInterfacesDisponibles, 'url'));
        if ($interfazEncontrada === false){
            $this->mostrarAccesoNoValido();
            exit;
        }
    }

    public function renderMenu(){
        $html = '';
        foreach ($this->arregloInterfacesDisponibles as $key => $value) {
            $activo = $this->interfazActual == $value["url"] ? "active" : ""; 
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

    /*
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
    */

    public function mostrarAccesoNoValido(){
        '<script> alert("No tiene permiso para ver esto"); history.back() </script>';
    }

    public function mostrarSesionNoActiva(){
        echo '<script> alert("Permisos de sesión no validados o ha pasado mucho tiempo de inactividad."); </script>';
        header("Location: ../login");
        exit;
    }

    public function esIdRolSupervisor(){
        $id_rol = $this->usuario["id_rol"];
        return ($id_rol == Globals::$ID_ROL_RECEPCION_SUPERVISOR ||  $id_rol ==  Globals::$ID_ROL_RECEPCION_DESCUENTOS) ? "1" : "0"; 
    }

}