<?php 

class Conexion{
    protected $dblink;
    protected $transactionCounter = 0;
    private $estado = false;
    private $stmt;
    
    public function __construct() {
        $this->abrirConexion();
    }
    
    public function __destruct() {
        $this->dblink = NULL;
        $this->estado = false;
    }
    
    protected function abrirConexion(){
        $servidor = TIPO_BD == "postgres" ?
                         "pgsql:host=".SERVIDOR_BD.";port=".PUERTO_BD.";dbname=".NOMBRE_BD : //PGSQL
                         'mysql:host='.SERVIDOR_BD.';port='.PUERTO_BD.';dbname='.NOMBRE_BD; //MYSQL
        

        $usuario = USUARIO_BD;
        $clave = CLAVE_BD;

        $opciones = [
        	PDO::ATTR_PERSISTENT => true,
        	PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ];	
        
        try {
            $this->dblink = new PDO($servidor, $usuario, $clave, $opciones); //MYSQL

            $this->dblink->exec("set names utf8");
            $this->estado = true;   
        } catch (Exception $exc) {
            $this->error = $exc->getMessage();
            echo $this->error;   
        }
        
        return $this->dblink;
    }

    public function beginTransaction()
    {
       if(!$this->transactionCounter++)
            return  $this->dblink->beginTransaction();    
       return $this->transactionCounter >= 0;
    }
    
    public function commit()
    {
       if(!--$this->transactionCounter)
           return $this->dblink->commit();
       return $this->transactionCounter >= 0;
    }
    
    public function rollBack()
    {
        if($this->transactionCounter >= 0)
        {
            $this->transactionCounter = 0;
            return $this->dblink->rollback();
        }
        $this->transactionCounter = 0;
        return false;
    }
    
    public function consulta($p_consulta)
    {
        $this->stmt = $this->dblink->prepare($p_consulta);
        $this->stmt->execute();
        return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function consulta_raw($p_consulta)
    {
        $this->stmt = $this->dblink->prepare($p_consulta);
        return $this->stmt->execute();
    }

    /*
    public function insert($p_nombre_tabla, $p_campos, $p_valores)
    {
        $sql_campos = implode(",", $p_campos);
        $sql_valores = $this->sentenciaPreparada($p_campos);
        
        $consulta = $this->dblink->prepare("INSERT INTO $p_nombre_tabla ($sql_campos) VALUES ($sql_valores)");
        
        for ($i = 0; $i < count($p_campos); $i++) {
            $consulta->bindParam(':'.$p_campos[$i], $p_valores[$i]);
        }
        
        return $consulta->execute();
    }*/

    private function reformatEne($key){
        $r = str_replace("ñ","n",$key);
        return $r;
    }

    public function insert($p_nombre_tabla, $p_campos_valores)
    {
        $p_campos = array_keys($p_campos_valores);
        $p_valores = array_values($p_campos_valores);

        $sql_campos = implode(",", $p_campos);
        $sql_valores = $this->sentenciaPreparada($p_campos);
        
        $this->stmt = $this->dblink->prepare("INSERT INTO $p_nombre_tabla ($sql_campos) VALUES ($sql_valores)");
        
        for ($i = 0; $i < count($p_campos); $i++) {
            $this->stmt->bindParam(':'.$this->reformatEne($p_campos[$i]), $p_valores[$i]);
        }
        
        return $this->stmt->execute();
    }

    /*un insert que hara muchos registros.*/
    public function insertMultiple($p_nombre_tabla, $p_campos, $p_valores_arreglo)
    {
        /*
        "INSERT INTO XXX(a,b,c,d) VALUES
        (:a00,:b01,:c02,:d13),
        (:a10,:b11,:c12,:d13);";
        */

        $sql_campos = implode(",", $p_campos);
        $sql_valores = "";

        $cantidadValores = count($p_valores_arreglo);
        $cantidadCols = count($p_campos);

         for ($i=0; $i < $cantidadValores ; $i++) { 
            if ($i > 0){
              $sql_valores .= ",\n";
            }
            $sql_valores .= "(";
            for ($j=0; $j <count($p_campos); $j++) { 
                if ($j > 0){
                    $sql_valores .= ",";
                }
                $sql_valores .= (":".$this->reformatEne($p_campos[$j])).$i.$j;
            }
            $sql_valores .= ")";
        }
        $sql_valores .= ";";

        $this->stmt = $this->dblink->prepare("INSERT INTO $p_nombre_tabla ($sql_campos) VALUES \n $sql_valores");

        for ($i=0; $i < $cantidadValores ; $i++) { 
            for ($j=0; $j < $cantidadCols; $j++) { 
                $this->stmt->bindParam(':'.$this->reformatEne($p_campos[$j]).$i.$j, $p_valores_arreglo[$i][$j]);
            }
        }

        return $this->stmt->execute();
    }

    public function update($p_nombre_tabla, $p_campos_valores, $p_campos_valores_where = null)
    {
        $p_campos = array_keys($p_campos_valores);
        $p_valores = array_values($p_campos_valores);
        $p_campos_where = isset($p_campos_valores_where) ? array_keys($p_campos_valores_where) : null;
        $p_valores_where = isset($p_campos_valores_where) ? array_values($p_campos_valores_where) : null;

        $sql_campos = $this->sentenciaPreparadaUpdate($p_campos);

        if (isset($p_campos_where)){
            $sql_campos_where = $this->sentenciaPreparadaAND($p_campos_where);
        } else {
            $sql_campos_where = true;
        }

        $sql = "UPDATE $p_nombre_tabla SET $sql_campos WHERE $sql_campos_where";
                
        $this->stmt = $this->dblink->prepare($sql);
        
        for ($i = 0; $i < count($p_campos); $i++) {
            $this->stmt->bindParam(':'.$this->reformatEne($p_campos[$i]), $p_valores[$i]);
        }

        if (isset($p_valores_where)){
            for ($i = 0; $i < count($p_campos_where); $i++) {
                $this->stmt->bindParam(':'.$this->reformatEne($p_campos_where[$i]), $p_valores_where[$i]);
            }
        } 
        
        return $this->stmt->execute();
    }

    public function delete($p_nombre_tabla, $p_campos_valores_where)
    {
        $p_campos_where = isset($p_campos_valores_where) ? array_keys($p_campos_valores_where) : null;
        $p_valores_where = isset($p_campos_valores_where) ? array_values($p_campos_valores_where) : null;

        if (isset($p_campos_where)){
            $sql_campos_where = $this->sentenciaPreparadaAND($p_campos_where);
        } else {
            $sql_campos_where = true;
        }

        $this->stmt = $this->dblink->prepare("DELETE FROM $p_nombre_tabla WHERE $sql_campos_where");
        
        if (isset($p_valores_where)){
            for ($i = 0; $i < count($p_campos_where); $i++) {                
                
                $this->stmt->bindParam(':'.$this->reformatEne($p_campos_where[$i]), $p_valores_where[$i]);
            }
        } 
        
        return $this->stmt->execute();
    }

    public function rowCount(){
    	return $this->stmt->rowCount();
    }
    
    private function sentenciaPreparadaUpdate($array)
    {
        $sql_temp = "";
        for ($i = 0; $i < count($array); $i++) {
            if ($i == count($array)-1)
            {
                $sql_temp = $sql_temp . $array[$i] . "=:" .$this->reformatEne($array[$i]);
            } else{
                $sql_temp = $sql_temp . $array[$i] . "=:" .$this->reformatEne($array[$i]) . ", ";
            }
        }
        return $sql_temp;
    }
    
    private function sentenciaPreparadaAND($array)
    {
        $sql_temp = "";
        for ($i = 0; $i < count($array); $i++) {
            if ($i == count($array)-1)
            {
                $sql_temp = $sql_temp . $array[$i] . "=:" .$this->reformatEne($array[$i]);
            } else{
                $sql_temp = $sql_temp . $array[$i] . "=:" .$this->reformatEne($array[$i]) . " AND ";
            }
        }
        return $sql_temp;
    }
    
    private function sentenciaPreparada($array)
    {
        $sql_temp = "";
        for ($i = 0; $i < count($array); $i++) {
            if ($i == count($array)-1)
            {
                $sql_temp = $sql_temp . ":" .$this->reformatEne($array[$i]);
            } else{
                $sql_temp = $sql_temp . ":" .$this->reformatEne($array[$i]) . ", ";
            }
        }
        return $sql_temp;
    }

    private function consulta_x($sql,$valores,$tipo,$fech = null)
    {
        $this->stmt = $this->dblink->prepare($sql);

        if ($valores != null){
            $valores = is_array($valores) ? $valores : [$valores];
            for ($i = 0; $i < count($valores); $i++){
                $this->stmt->bindParam(":".$i, $valores[$i]);
            }
        }

        $this->stmt->execute();

        return $this->getLastResultSet($tipo);
        
    }

    public function getLastResultSet($tipo = ""){
    	switch($tipo){
            case "*":
                return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
            case "1*":
                return $this->stmt->fetch(PDO::FETCH_ASSOC);
            case "1":
                return $this->stmt->fetch(PDO::FETCH_NUM)[0];
            case "[_]":
                return true;
            case "":
            	return $this->stmt->fetchAll(PDO::FETCH_ASSOC); 
        }

        return false;
    }

    public function consultarValor($p_sql, $p_valores = null)
    {
        return $this->consulta_x($p_sql, $p_valores,"1");
    }

    public function consultarFila($p_sql, $p_valores = null)
    {
        return $this->consulta_x($p_sql, $p_valores,"1*");
    }

    public function consultarFilas($p_sql, $p_valores = null)
    {
        return $this->consulta_x($p_sql, $p_valores,"*");
    }

    public function consultarFN($p_sql, $p_valores = null)
    {
        return $this->consulta_x($p_sql, $p_valores,"[_]");
    }

    
}