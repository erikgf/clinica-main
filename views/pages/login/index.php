<?php

include_once "../../../datos/configuracion.vista.php";

$ruta_base = "../../../";

?>


<!DOCTYPE html>
<html>
<head lang="es">
  <meta charset="utf-8">
  <link rel="manifest" href="<?php echo RUTA_BASE; ?>/manifest.webmanifest">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title><?php echo NOMBRE_SISTEMA." - Iniciar Sesión"; ?></title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?php echo $ruta_base; ?>template/plugins/fontawesome-free/css/all.min.css">
  <link href="<?php echo $ruta_base; ?>/views/css/toastr.css" rel="stylesheet"/>
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="<?php echo $ruta_base; ?>template/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?php echo $ruta_base; ?>template/dist/css/adminlte.min.css">
  <!-- Google Font: Source Sans Pro -->
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
</head>

<style>
  .login-card-body, .login-page{
    background-color:white;
  }

  .login-page{
    background-image: url(../../img/bg_dmi.png);
    background-position: center;
    background-repeat: no-repeat;
    margin-top: -10vh;
  }

  .add-button {
    position: absolute;
    top: 1px;
    left: 1px;
  }

</style>

<body class="hold-transition login-page">
<div class="login-box">
  <div class="login-logo">
    <div>
      <img src="<?php echo $ruta_base; ?>/views/img/logo_dmi.png" alt="Logo">
    </div>
  </div>
  <!-- /.login-logo -->
  <div class="card">
    <div class="card-body login-card-body">
      <p class="login-box-msg">Iniciar Sesión</p>

      <form id="frm-sesion">
        <div class="input-group mb-3">
          <input type="text" class="form-control" required placeholder="Usuario" id="txt-usuario" autofocus>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fa fa-user"></span>
            </div>
          </div>
        </div>
        <div class="input-group mb-3">
          <input type="password" class="form-control"  required placeholder="Contraseña" id="txt-clave">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fa fa-lock"></span>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-8">
            <!-- 
            <div class="icheck-primary">
              <input type="checkbox" id="remember">
              <label for="remember">
                Remember Me
              </label>
            </div>
            -->
          </div>
          <!-- /.col -->
          <div class="col-4">
            <button type="submit" id="btn-acceder" class="btn btn-primary btn-block">ACCEDER</button>
          </div>
          <!-- /.col -->
        </div>

        <!-- <p class="text-red"><b>Ha habido una actualización del sistema. Antes de acceder, SE RECOMIENDA actualizar el navegador usando CTRL + F5 (solo por única vez). Gracias.</b></p> -->
      </form>
    </div>
    <!-- /.login-card-body -->
  </div>
</div>
<!-- /.login-box -->

<!-- jQuery -->
<script src="<?php echo $ruta_base; ?>template/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="<?php echo $ruta_base; ?>template/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="<?php echo $ruta_base; ?>template/dist/js/adminlte.min.js"></script>
<script src="<?php echo $ruta_base; ?>/views/js/toastr.min.js"></script>

<script type="text/javascript" src="<?php echo $ruta_base; ?>/views/js/variables.js"></script>


<script src="index.js"></script>
<script>
  let installPromptEvent;
  window.addEventListener('beforeinstallprompt', (event) => {
    // Prevent Chrome <= 67 from automatically showing the prompt
    event.preventDefault();
    // Stash the event so it can be triggered later.
    installPromptEvent = event;
    // Update the install UI to notify the user app can be installed
    document.querySelector('#install-button').disabled = false;
  });

</script>

</body>
</html>
