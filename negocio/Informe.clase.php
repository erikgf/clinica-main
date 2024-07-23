<?php

require_once '../datos/Conexion.clase.php';

class Informe extends Conexion {
    public int $id_informe;
    public int $id_atencion_medico_servicio;
    public string $contenido_informe;
    public int $numero_orden_dia;

    public int $id_usuario_registrado;

    public function __construct($objDB = null){
        if ($objDB != null){
            parent::__construct($objDB);
        } else {
            parent::__construct();
        }
    }

    public function listar(string $fechaInicio, string $fechaFin, $esUsuarioMedico = false){
        try {
            $strMedicoFiltro = "";
            //$this->id_usuario_registrado
            if ($esUsuarioMedico){
                $sql = "SELECT id_medico FROM usuario WHERE id_usuario  = :0 AND estado_acceso ='A' AND estado_mrcb";
                $id_medico = $this->consultarValor($sql, [$this->id_usuario_registrado]);
                
                if (!$id_medico){
                    throw new Exception("Usuario médico no válido. Contactar administrador.", 1);
                }

                $strMedicoFiltro = " AND id_medico = ".$id_medico." ";
            }

            $sql = "SELECT 
                        ams.id_medico_atendido as id,
                        m.nombres_apellidos as descripcion,
                        COUNT(i.id_informe) as cantidad
                        FROM informe i
                        INNER JOIN atencion_medica_servicio ams ON ams.id_atencion_medica_servicio = i.id_atencion_medica_servicio
                        INNER JOIN atencion_medica am ON am.id_atencion_medica = ams.id_atencion_medica
                        INNER JOIN medico m ON m.id_medico = ams.id_medico_atendido
                        WHERE i.fecha_hora_eliminado IS NULL AND am.fecha_atencion BETWEEN :0 AND :1 $strMedicoFiltro
                        GROUP BY ams.id_medico_atendido, m.nombres_apellidos
                        ORDER BY m.nombres_apellidos";

            $medicos = $this->consultarFilas($sql, [$fechaInicio, $fechaFin]);

            foreach ($medicos as $i => $medico) {
                $sql = "SELECT 
                        i.id_informe,
                        DATE_FORMAT(am.fecha_atencion,'%d-%m-%Y') as fecha,
                        am.nombre_paciente as paciente,
                        ser.descripcion as examen,
                        cat.descripcion as area,
                        DATE_FORMAT(COALESCE(i.fecha_hora_actualizado, i.fecha_hora_registrado),'%d-%m-%Y %H:%i:%s') as ultima_modificacion
                        FROM informe i
                        INNER JOIN atencion_medica_servicio ams ON ams.id_atencion_medica_servicio = i.id_atencion_medica_servicio
                        INNER JOIN atencion_medica am ON am.id_atencion_medica = ams.id_atencion_medica
                        INNER JOIN servicio ser ON ser.id_servicio = ams.id_servicio
                        INNER JOIN categoria_servicio cat ON cat.id_categoria_servicio = ser.id_categoria_servicio
                        WHERE i.fecha_hora_eliminado IS NULL AND ams.id_medico_atendido = :0 AND am.fecha_atencion BETWEEN :1 AND :2
                        ORDER BY i.numero_orden_dia";

                $medicos[$i]["informes"] = $this->consultarFilas($sql, [$medico["id"], $fechaInicio, $fechaFin]);
            }

            return $medicos;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    public function leer(int $id_informe, bool $esUsuarioMedico = false){
        try {
            if ($esUsuarioMedico){
                $sql = "SELECT id_medico FROM usuario WHERE id_usuario  = :0 AND estado_acceso ='A' AND estado_mrcb";
                $id_medico = $this->consultarValor($sql, [$this->id_usuario_registrado]);
                
                if (!$id_medico){
                    throw new Exception("Usuario médico no válido. Contactar administrador.", 1);
                }

                $sql = "SELECT 
                            i.id_informe,
                            i.contenido_informe,
                            nombre_paciente as paciente,
                            fecha_atencion as fecha,
                            ser.descripcion as examen,
                            m.nombres_apellidos as medico,
                            IF (p.fecha_nacimiento IS NOT NULL, DATE_FORMAT(FROM_DAYS(DATEDIFF(NOW(), p.fecha_nacimiento)), '%Y') + 0, '') as edad_paciente
                            FROM informe i
                            INNER JOIN atencion_medica_servicio ams ON ams.id_atencion_medica_servicio = i.id_atencion_medica_servicio
                            INNER JOIN atencion_medica am ON am.id_atencion_medica = ams.id_atencion_medica
                            INNER JOIN servicio ser ON ser.id_servicio = ams.id_servicio 
                            INNER JOIN medico m ON m.id_medico = ams.id_medico_realizado
                            INNER JOIN paciente p ON am.id_paciente = p.id_paciente
                            WHERE i.fecha_hora_eliminado IS NULL AND ams.id_medico_atendido = :0 AND i.id_informe = :1";

                $informe = $this->consultarFila($sql, [$id_medico, $id_informe]);

                if ($informe){
                    $sql = "SELECT contenido FROM informe_bitacora WHERE id_informe = :0 AND tipo_registro = 'B'";
                    $contenido_informe = $this->consultarValor($sql, [$id_informe]);

                    if ($contenido_informe){
                        $informe["contenido_informe"] = $contenido_informe;
                    }

                    $informe["fecha"] = Funciones::fechaFormateadaCompletaMes($informe["fecha"]);
                }

                return $informe;
            }

            $sql = "SELECT 
                        i.id_informe,
                        i.contenido_informe,
                        nombre_paciente as paciente,
                        fecha_atencion as fecha,
                        ser.descripcion as examen,
                        m.nombres_apellidos as medico,
                        IF (p.fecha_nacimiento IS NOT NULL, DATE_FORMAT(FROM_DAYS(DATEDIFF(NOW(), p.fecha_nacimiento)), '%Y') + 0, '') as edad_paciente
                        FROM informe i
                        INNER JOIN atencion_medica_servicio ams ON ams.id_atencion_medica_servicio = i.id_atencion_medica_servicio
                        INNER JOIN atencion_medica am ON am.id_atencion_medica = ams.id_atencion_medica
                        INNER JOIN servicio ser ON ser.id_servicio = ams.id_servicio 
                        INNER JOIN medico m ON m.id_medico = ams.id_medico_realizado
                        INNER JOIN paciente p ON am.id_paciente = p.id_paciente
                        WHERE i.fecha_hora_eliminado IS  NULL AND i.id_informe = :0";

            $informe = $this->consultarFila($sql, [$id_informe]);

            if ($informe){
                $sql = "SELECT contenido FROM informe_bitacora WHERE id_informe = :0 AND tipo_registro = 'B'";
                $contenido_informe = $this->consultarValor($sql, [$id_informe]);

                if ($contenido_informe){
                    $informe["contenido_informe"] = $contenido_informe;
                }

                $informe["fecha"] = Funciones::fechaFormateadaCompletaMes($informe["fecha"]);

            }

            return $informe;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    private function getContenidoPorDefecto (){
        $contenido_informe = "<br/><br/><p><b>DIAGNÓSTICO POR IMAGEN:</b></p><ul><li></li></ul>";
        return $contenido_informe;
    }

    public function registrar(int $id_atencion_medica_servicio){
        try {

            $ahora = date("Y-m-d H:i:s");

            $sql = "SELECT id_informe, fecha_hora_registrado, fecha_hora_actualizado FROM informe WHERE id_atencion_medica_servicio = :0 AND fecha_hora_eliminado IS NOT NULL";
            $informePrevio = $this->consultarFila($sql, [$id_atencion_medica_servicio]);

            $this->beginTransaction();

            if ($informePrevio){
                //ya existe, ergo se está haciendo una reasignación
                //eliminar bitacoras
                if ($informePrevio["fecha_hora_registrado"] == $informePrevio["fecha_hora_actualizado"]){
                    $this->delete("informe", ["id_informe" => $informePrevio["id_informe"]]);
                    $this->delete("informe_bitacora", ["id_informe" => $informePrevio["id_informe"]]);
                } else {
                    $this->update("informe", ["fecha_hora_eliminado" => $ahora], ["id_informe"=>$informePrevio["id_informe"]]);
                    $this->update("informe_bitacora", ["fecha_hora_eliminado" => $ahora], ["id_informe"=>$informePrevio["id_informe"], "tipo_registro"=>"R"]);
                    $this->delete("informe_bitacora", ["id_informe" => $informePrevio["id_informe"], "tipo_registro"=>"B" ]);
                }
            }

            $contenido_informe = $this->getContenidoPorDefecto();

            $campos_valores = [
                "id_atencion_medica_servicio"=>$id_atencion_medica_servicio,
                "contenido_informe"=>$contenido_informe,
                "fecha_hora_registrado"=>$ahora,
                "fecha_hora_actualizado"=>$ahora,
                "id_usuario_registrado"=>$this->id_usuario_registrado
            ];


            $this->insert("informe", $campos_valores);
            $id = $this->getLastId();

            $this->commit();
            return $id;
            
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    public function eliminar(int $id_atencion_medica_servicio){
        try {


            $ahora = date("Y-m-d H:i:s");

            $sql = "SELECT id_informe, fecha_hora_registrado, fecha_hora_actualizado FROM informe WHERE id_atencion_medica_servicio = :0 AND fecha_hora_eliminado IS NOT NULL";
            $informePrevio = $this->consultarFila($sql, [$id_atencion_medica_servicio]);


            if ($informePrevio){
                $this->beginTransaction();

                if ($informePrevio["fecha_hora_registrado"] == $informePrevio["fecha_hora_actualizado"]){
                    $this->delete("informe", ["id_informe" => $informePrevio["id_informe"]]);
                    $this->delete("informe_bitacora", ["id_informe" => $informePrevio["id_informe"]]);
                } else {
                    $this->update("informe", ["fecha_hora_eliminado" => $ahora], ["id_informe"=>$informePrevio["id_informe"]]);
                    $this->update("informe_bitacora", ["fecha_hora_eliminado" => $ahora], ["id_informe"=>$informePrevio["id_informe"], "tipo_registro"=>"R"]);
                    $this->delete("informe_bitacora", ["id_informe" => $informePrevio["id_informe"], "tipo_registro"=>"B"]);
                }

                $this->commit();
                return $$informePrevio["id_informe"];
            }

            return null;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    public function modificarContenido(int $id_informe, string $contenido_informe, bool $esTemporal = false){
        try {

            $ahora = date("Y-m-d H:i:s");

            $this->beginTransaction();

            $this->update("informe", ["contenido_informe" => $contenido_informe, "fecha_hora_actualizado"=>$ahora, "id_usuario_actualizado"=>$this->id_usuario_registrado], ["id_informe"=>$id_informe]);

            if (!$esTemporal){
                $this->delete("informe_bitacora", ["id_informe" => $id_informe, "tipo_registro"=>"B", "id_usuario_registrado"=>$this->id_usuario_registrado]);
            }
            
            $this->insert("informe_bitacora", ["id_informe" => $id_informe, "tipo_registro"=>"R", "contenido"=>$contenido_informe, "id_usuario_registrado"=>$this->id_usuario_registrado, "fecha_hora_registrado"=>$ahora]);
            $this->commit();
            
            return $id_informe;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    public function cambiarOrden(int $id_medico, array $arregloOrdenado){ //[id arreglo de informes]
        try {
            $this->beginTransaction();

            foreach ($arregloOrdenado as $key => $id) {
                $this->update("informe", ["numero_orden_dia"=>$key], ["id_informe"=>$id]);
            }

            $this->commit();
            return true;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    public function obtenerContenidoParaWord(int $id_informe){
        try {

            $fecha = date("Ymd");

            $sql = "SELECT 
                    i.contenido_informe,
                    nombre_paciente as paciente,
                    fecha_atencion as fecha,
                    ser.descripcion as examen,
                    m.nombres_apellidos as medico,
                    IF (p.fecha_nacimiento IS NOT NULL, DATE_FORMAT(FROM_DAYS(DATEDIFF(NOW(), p.fecha_nacimiento)), '%Y') + 0, '') as edad_paciente
                    FROM informe i
                    INNER JOIN atencion_medica_servicio ams ON ams.id_atencion_medica_servicio = i.id_atencion_medica_servicio
                    INNER JOIN atencion_medica am ON am.id_atencion_medica = ams.id_atencion_medica
                    INNER JOIN servicio ser ON ser.id_servicio = ams.id_servicio 
                    INNER JOIN medico m ON m.id_medico = ams.id_medico_realizado
                    INNER JOIN paciente p ON am.id_paciente = p.id_paciente
                    WHERE i.id_informe = :0 AND i.fecha_hora_eliminado IS NULL";

            $informe = $this->consultarFila($sql, [$id_informe]);

            if (!$informe){
                throw new Exception("Codigo de informe inválido", 404);
            }

            $nombre_archivo = $informe["examen"]."-".$fecha.".doc";

            $paciente = $informe["paciente"];
            $edad_paciente = $informe["edad_paciente"];
            $fecha = strtoupper(Funciones::fechaFormateadaCompletaMes($informe["fecha"]));
            $examen = $informe["examen"];
            $medico = $informe["medico"];
            $contenido_informe = $informe["contenido_informe"];

           // $contenido_informe = str_replace("></p>", "><br></p>", $contenido_informe);

            $contenido_word = <<<EOD
                <head>
                <style>
                    .body{   
                        font-family: Arial;
                        font-size: 11pt;
                    }

                    .table {
                        font-family: Arial;
                        font-size: 11pt;
                        width: 70%;
                    }

                    p {
                        font-size: 11pt;
                    }

                    .ql-align-right {
                        text-align: right;
                    }

                    .ql-align-justify {
                        text-align: justify;
                    }

                    .ql-align-center {
                        text-align: center;
                    }

                    .ql-align-left {
                        text-align: left;
                    }
                </style>
                </head>
                <body class="body">
                    <br/>
                    <br/>
                    <table class="table">
                        <tbody>
                            <tr>
                                <td>PACIENTE</td>
                                <td>$paciente</td>
                            </tr>
                            <tr>
                                <td>EDAD</td>
                                <td>$edad_paciente AÑOS</td>
                            </tr>
                            <tr>
                                <td>EXAMEN</td>
                                <td><b>$examen</b></td>
                            </tr>
                            <tr>
                                <td>FECHA</td>
                                <td>$fecha</td>
                            </tr>
                            <tr>
                                <td>MÉDICO</td>
                                <td>Dr. $medico</td>
                            </tr>
                        </tbody>
                    </table>
                    $contenido_informe
                 </body>
                EOD;
            
            return [ "contenido"=>$contenido_word, "nombre_archivo"=>$nombre_archivo];
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

}

