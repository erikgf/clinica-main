 <?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
//SERVIDOR
 define("MODELO", "../negocio");
 define("MODELO_UTIL", MODELO."/util");
 define("MODELO_FUNCIONES",MODELO_UTIL."/Funciones.php");
 define("MODELO_VISTA","../../modelo/Util/Vista.php");
 
 //SESION
require_once '../negocio/Sesion.clase.php';
require_once '../negocio/util/Funciones.php';
