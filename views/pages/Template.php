<?php

class Template{
    private $title;
    private $content;
    private $navbar;

    public $ID_ROL_ADMINISTRADOR = "1";
    public $ID_ROL_RECEPCION = "2";
    public $ID_ROL_FACTURADOR = "3";
    public $ID_ROL_LOGISTICA = "4";
    
    public $ID_VISUALIZADOR = "6";
    public $ID_CONTABILIDAD = "7";
    public $ID_ROL_ASISTENTE_REVISION = "8";
    public $ID_ROL_LABORATORIO = "9";
    public $ID_ROL_RECEPCION_SUPERVISOR = "10";
    public $ID_ROL_COORDINADOR_LABORATORIO = "11";
    public $ID_ROL_ASISTENTE_ADMINISTRADOR = "12";

    public function loadContent($ruta){
        $this->content = file_get_contents($ruta, true);
    }

    public function renderNavbarItems(){
        echo "";
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
                ];
            break;

            case $this->ID_ROL_RECEPCION:
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
                ];
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