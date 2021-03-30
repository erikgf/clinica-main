<?php

class Sesion{
    public $id_usuario_registrado;
    public $nombre_usuario;

    private static $nombre_sesion = "dpi_session";
    private static $key_usuario = "usuario";

    public static function obtenerSesion(){
        return $_SESSION[Sesion::$key_usuario];
    }

    public static function setSesion($objUsuario){
        $_SESSION[Sesion::$key_usuario] = $objUsuario;
        Sesion::$id_usuario_registrado = $objUsuario["id_usuario_registrado"];
        Sesion::$nombre_usuario = $objUsuario["nombre_usuario"];
    }

    public static function iniciarSesion(){
        session_name(Sesion::$nombre_sesion);
        if(session_status() == PHP_SESSION_NONE){
            session_start();
        }
    }

    public static function obtenerSesionId(){
        return Sesion::$id_usuario_registrado;
    }

    public static function destruirSesion(){
        // Destruir todas las variables de sesión.
        Sesion::iniciarSesion();
        session_destroy();
    }

}

Sesion::iniciarSesion();
