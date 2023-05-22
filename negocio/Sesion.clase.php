<?php


class Sesion{
    public static $id_usuario_registrado;
    public static $nombre_usuario;
    public static $id_rol;

    private static $nombre_sesion = "dpi_session";
    private static $key_usuario = "usuario";

    public static function obtenerSesion(){
        return isset($_SESSION[Sesion::$key_usuario]) ? $_SESSION[Sesion::$key_usuario] : null;
    }

    public static function setSesion($objUsuario){
        $_SESSION[Sesion::$key_usuario] = $objUsuario;
        Sesion::$id_usuario_registrado = $objUsuario["id_usuario_registrado"];
        Sesion::$nombre_usuario = $objUsuario["nombre_usuario"];
        Sesion::$id_rol = $objUsuario["id_rol"];
    }

    public static function iniciarSesion(){
        ini_set('session.gc_maxlifetime', 28800);
        session_name(Sesion::$nombre_sesion);
        if(session_status() == PHP_SESSION_NONE){
            session_start();
            if (Sesion::obtenerSesion()){
                Sesion::$id_usuario_registrado = Sesion::obtenerSesion()["id_usuario_registrado"];
            }
        }
    }

    public static function obtenerSesionId(){
        return Sesion::$id_usuario_registrado;
    }

    public static function destruirSesion(){
        session_destroy();
    }



}

Sesion::iniciarSesion();
