<?php 

include_once "../../../datos/configuracion.vista.php";?>

<html>

<link rel="stylesheet" href="<?php echo RUTA_BASE; ?>/plugins/tempusdominus-bootstrap-4.css" crossorigin="anonymous" />
<body>

<input type="text" class="form-control" onchange="trick(this)" style="width:100%">

<!-- jQuery -->
<script src="<?php echo RUTA_BASE; ?>/template/plugins/jquery/jquery.min.js"></script>
<script>
    var trick = function($this){
        var val = $this.value;	
        var x = val.split("=");
        val = x[x.length - 1];

        val = val.replaceAll("%3A", ":");
        val = val.replaceAll("%2F", "/");
        val = val.replaceAll("%21", "!");
        val = val.replaceAll("%23", "#");
        
        $this.value = val;
        $this.select();
    };

</script>
</body>

</html>