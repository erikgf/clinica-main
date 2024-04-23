<?php
require_once '../datos/Conexion.clase.php';

class Usuario extends Conexion{
    public $id_usuario;
    public $nombre_usuario;
    public $clave;

    public function iniciarSesion(){
        try {
            
            $sql = "SELECT u.id_usuario, u.clave, u.estado_acceso,
                        COALESCE(pr.descripcion, CONCAT(c.nombres,' ',apellido_paterno,' ',apellido_materno)) as nombres_apellidos, 
                        r.id_rol,
                        r.descripcion as nombre_rol,
                        COALESCE((SELECT url from rol_interfaz ri
                            INNER JOIN interfaz i ON i.id_interfaz = ri.id_interfaz
                            WHERE ri.id_rol = r.id_rol
                            LIMIt 1),'') as interfaz_inicio_sesion
                    FROM usuario u 
                    LEFT JOIN colaborador c ON c.id_colaborador = u.id_colaborador
                    LEFT JOIN promotora pr ON pr.id_promotora = u.id_promotora
                    LEFT JOIN rol r ON r.id_rol = COALESCE(c.id_rol, pr.id_rol)
                    WHERE u.estado_mrcb AND u.nombre_usuario = :0";
            $usuario = $this->consultarFila($sql, [$this->nombre_usuario]);

            if ($usuario == false){
                throw new Exception("Usuario no válido.", 1);
            }

            if ($usuario["estado_acceso"] == "I"){
                throw new Exception("Usuario está inactivo.", 1);
            }

            if ($usuario["clave"] != md5($this->clave)){
                throw new Exception("Clave NO VÁLIDA.", 1);
            }

            $objUsuario = ["id_usuario_registrado"=>$usuario["id_usuario"], 
                            "nombre_usuario"=>$usuario["nombres_apellidos"],
                            "nombre_rol"=>$usuario["nombre_rol"],
                            "id_rol"=>$usuario["id_rol"],
                            "interfaz_inicio_sesion"=>$usuario["interfaz_inicio_sesion"]];

            require_once 'Sesion.clase.php';
            Sesion::setSesion($objUsuario);
            
            return $objUsuario;
        } catch (\Throwable $th) {
            throw new Exception($th->getMessage(), 1);
        }
    }

    public function obtenerAutorizadoresDescuentos($cadenaBuscar){
        try {

            $sql = "SELECT 
                    u.id_usuario as id,
                    TRIM(CONCAT(c.nombres,' ',c.apellido_paterno,' ',c.apellido_materno)) as text
                    FROM usuario u 
                    INNER JOIN colaborador c ON c.id_colaborador = u.id_colaborador 
                    INNER JOIN rol r ON r.id_rol = c.id_rol
                    WHERE u.estado_mrcb AND u.estado_acceso = 'A' AND r.es_gestion_descuentos = 1 AND
                        COALESCE(CONCAT(c.nombres,' ',c.apellido_paterno,' ',c.apellido_materno),'') LIKE '%".$cadenaBuscar."%' 
                    LIMIT 5";
                    
            $data =  $this->consultarFilas($sql);
            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    public function validarDescuento(){
        try {

            $sql = "SELECT 
                    clave
                    FROM usuario u 
                    INNER JOIN colaborador c ON c.id_colaborador = u.id_colaborador
                    INNER JOIN rol r ON r.id_rol = c.id_rol
                    WHERE u.estado_mrcb AND u.estado_acceso = 'A' AND r.es_gestion_descuentos = 1  AND id_usuario = :0";
            
            $data =  $this->consultarFila($sql, [$this->id_usuario]);

            if ($data == false){
                throw new Exception("Usuario no existe o no tiene los permisos adecuados.", 1);
            }

            if ($data["clave"] != md5($this->clave)){
                throw new Exception("La clave ingresada no es correcta.", 1);
            }

            return true;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    public function cambiarClave($antigua_clave, $nueva_clave){
        try {

            if (strlen($nueva_clave) < 6){
                throw new Exception("La clave nueva debe de tener mas de 6 caracteres.", 1);
            }   

            if ($antigua_clave == $nueva_clave){
                throw new Exception("La clave nueva no puede ser igual a la antigua clave.", 1);
            }

            $sql = "SELECT 
                    clave
                    FROM usuario u 
                    WHERE u.estado_mrcb AND u.estado_acceso = 'A' AND id_usuario = :0";
            
            $data =  $this->consultarFila($sql, [$this->id_usuario]);

            if ($data == false){
                throw new Exception("Usuario no existe o no tiene los permisos adecuados.", 1);
            }

            if ($data["clave"] != md5($antigua_clave)){
                throw new Exception("La clave antigua no es correcta.", 1);
            }

            $this->update("usuario", ["clave"=>md5($nueva_clave)], ["id_usuario"=>$this->id_usuario]);
            return ["msj"=>"Clave cambiada con éxito"];
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    public function getInterfaces($id_usuario_registro){
        try {

            if ($id_usuario_registro == null){
                return [];
            }

            //Colaborador
            $sql  = "SELECT id_rol  
                        FROM colaborador 
                        WHERE id_colaborador IN (SELECT id_colaborador FROM usuario WHERE id_usuario = :0)";
            $id_rol = $this->consultarValor($sql, [$id_usuario_registro]);

            //Promotora
            if ($id_rol == false){
                $sql  = "SELECT id_rol  
                        FROM promotora 
                        WHERE id_promotora IN (SELECT id_promotora FROM usuario WHERE id_usuario = :0)";
                $id_rol = $this->consultarValor($sql, [$id_usuario_registro]);
            }

            $sql = "SELECT 
                        i.rotulo, 
                        i.url,
                        es_padre as padre
                        FROM rol_interfaz ri
                        INNER JOIN interfaz i ON i.id_interfaz = ri.id_interfaz AND i.es_padre = 0
                        WHERE ri.id_rol IN (:0)
                        ORDER BY i.id_interfaz";
            
            $interfaces =  $this->consultarFilas($sql, [$id_rol]);
            $alertas = $this->getAlertas($id_rol);

            return ["id_rol"=>$id_rol, "interfaces"=>$interfaces,  "alertas"=>$alertas];
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    public function getAlertas($id_rol){
        try {
            //Por ahora solo se trabajará alertas para MEDICOS POR EDITAR.

            if (!in_array($id_rol, [Globals::$ID_ROL_ADMINISTRADOR])){
                return [];
            }

            $sql = "SELECT COUNT(id_medico)
                    FROM medico_promotora_temporal m 
                    WHERE m.estado_mrcb AND m.estado_activo = 'P'";

            $cantidadPendientes = $this->consultarValor($sql);

            if ($cantidadPendientes <= 0){
                return [];
            }

            return [
                [
                    "mensaje"=>"Hay ".$cantidadPendientes." Médicos por aprobar.",
                    "url"=>"../promotoras-medicos/"
                ]
            ];
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

}

   