<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="x-ua-compatible" content="ie=edge">

  <title><?php $objTemplate->renderTitle(); ?></title>

  <link rel="icon" href="<?php echo RUTA_BASE; ?>/icon/icon_dmi_white.png">
  <!-- Font Awesome Icons -->
  <link href="<?php echo RUTA_BASE; ?>/views/css/toastr.css" rel="stylesheet"/>
  <link rel="stylesheet" href="<?php echo RUTA_BASE; ?>/plugins/fontawesome-free/css/all.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?php echo RUTA_BASE; ?>/adminlte/dist/css/adminlte.min.css">
  <link rel="stylesheet" href="<?php echo RUTA_BASE; ?>/views/css/custom-styles.css">
  <!-- Google Font: Source Sans Pro -->
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
  <link rel="stylesheet" href="https://netdna.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.css">
  <link rel="stylesheet" type="text/css" href="<?php echo RUTA_BASE; ?>/views/css/datatables.css">
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.dataTables.min.css">
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css">
  <link rel="manifest" href="<?php echo RUTA_BASE; ?>/manifest.webmanifest">

  <link rel="stylesheet" href="<?php echo RUTA_BASE; ?>/plugins/tempusdominus-bootstrap-4.css" crossorigin="anonymous" />
  <link href="<?php echo RUTA_BASE; ?>/plugins/select2.min.css" rel="stylesheet" />

  <script type="text/javacript">    
    window.addEventListener("load", function(event) {
        document.body.style.zoom = "95%";
    });
  </script>

</head>
<body class="sidebar-mini sidebar-collapse">
<div class="wrapper">
  <!-- Navbar -->
    <!-- Left navbar links -->
      <?php $objTemplate->renderNavbarItems(); ?>
      <!--
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
          <ul class="navbar-nav">
            <li class="nav-item">
              <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
              <a onclick="showContent('registro-paciente')" class="nav-link" id="ctn-historial">Pacientes</a>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
              <a onclick="showContent('registro-atencion')"  class="nav-link">Registro de Atenciones</a>
            </li>
          </ul>
        </nav>
      -->
    
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
  <!--
    <a href="#" class="brand-link">
      <img src="<?php echo RUTA_BASE; ?>/icon/icon_dmi_white.png" alt="DMI Logo" class="brand-image img-circle elevation-3">
      <span class="brand-text font-weight-light">Software | DMI</span>
    </a>
  -->

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image" style="display:flex;justify-content:center;align-items:center">
          <img style="background-color: #d6d6d6;" src="<?php echo RUTA_BASE; ?>/icon/user_icon.png" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
          <a href="#" class="d-block"><?php echo Sesion::obtenerSesion()["nombre_usuario"]; ?> <br>
            <small><?php echo Sesion::obtenerSesion()["nombre_rol"]; ?></small></a>
        </div>
      </div>

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
            <?php $objTemplate->renderMenu(); ?>        
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="content">
          <?php  $objTemplate->renderContent(); ?>
        </div>
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->
  <?php // include_once "pages/sidebar.php";  ?>

  <?php // include_once "pages/footer.php";  ?>
</div>
<!-- ./wrapper -->

<!-- REQUIRED SCRIPTS -->

<!-- jQuery -->
<script src="<?php echo RUTA_BASE; ?>/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="<?php echo RUTA_BASE; ?>/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="<?php echo RUTA_BASE; ?>/adminlte/dist/js/adminlte.min.js"></script>
<script type="text/javascript" src="<?php echo RUTA_BASE; ?>/plugins/moment.js"></script>
<script type="text/javascript" src="<?php echo RUTA_BASE; ?>/plugins/handlebars-v4.7.7.js"></script>
<script src="<?php echo RUTA_BASE; ?>/plugins/tempusdominus-bootstrap-4.js" crossorigin="anonymous"></script>
<script src="<?php echo RUTA_BASE; ?>/plugins/select2.js"></script>

<script type="text/javascript" charset="utf8" src="<?php echo RUTA_BASE; ?>/views/js/jquery.dataTables.js"></script>

<script type="text/javascript"  src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
<script type="text/javascript"  src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script type="text/javascript"  src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap4.min.js"></script>
<script type="text/javascript"  src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script type="text/javascript"  src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script type="text/javascript"  src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script type="text/javascript"  src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
<script type="text/javascript"  src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.print.min.js"></script>

<script src="<?php echo RUTA_BASE; ?>/views/js/toastr.min.js"></script>
<script type="text/javascript" src="<?php echo RUTA_BASE; ?>/views/js/variables.js"></script>

</body>
</html>
