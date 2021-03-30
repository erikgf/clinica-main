<?php

class TemplateController{
    public function loadContent($page){
        switch($page){
            case "frm-registro-atencion":
                require('./views/pages/frm-registro-atencion.php');  
            break;

            case "navigation-buttons":
                require('./views/pages/navigation-buttons.php');  
            break;

            case "frm-registro-atencion-caja":
                require('./views/pages/frm-registro-atencion-caja.php');  
            break;
        }
    }
    
    public function template(){
        include 'views/template.php';
    }
}
